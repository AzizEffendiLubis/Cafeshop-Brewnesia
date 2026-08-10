<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Category;
use App\Models\CafeTable;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    protected function getTableId()
    {
        return session('guest_table_id');
    }

    protected function getCustomerName()
    {
        return session('guest_customer_name', 'Tamu');
    }

    public function menu(Request $request)
    {
        $tableId = $this->getTableId();
        if (!$tableId) {
            return redirect()->route('guest.select');
        }

        $table = CafeTable::findOrFail($tableId);
        $categories = Category::with(['availableMenus' => function ($q) {
            $q->orderBy('sort_order');
        }])->where('is_active', true)->orderBy('sort_order')->get();

        $cart = session('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        return view('customer.menu', compact('table', 'categories', 'cart', 'cartCount'));
    }

    public function addToCart(Request $request)
    {
        $request->validate(['menu_id' => 'required|exists:menus,id']);
        $menu = Menu::findOrFail($request->menu_id);

        if (!$menu->is_available) {
            return response()->json(['success' => false, 'message' => 'Menu tidak tersedia.'], 422);
        }

        $cart = session('cart', []);
        $key = $menu->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity']++;
            $cart[$key]['subtotal'] = $cart[$key]['quantity'] * $menu->price;
        } else {
            $cart[$key] = [
                'menu_id'  => $menu->id,
                'name'     => $menu->name,
                'emoji'    => $menu->emoji,
                'price'    => $menu->price,
                'quantity' => 1,
                'subtotal' => $menu->price,
            ];
        }

        session(['cart' => $cart]);

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $subtotal = array_sum(array_column($cart, 'subtotal'));

        return response()->json([
            'success'    => true,
            'message'    => $menu->emoji . ' ' . $menu->name . ' ditambahkan!',
            'cart_count' => $cartCount,
            'subtotal'   => $subtotal,
        ]);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'menu_id'  => 'required',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session('cart', []);
        $key = $request->menu_id;

        if ($request->quantity == 0) {
            unset($cart[$key]);
        } elseif (isset($cart[$key])) {
            $cart[$key]['quantity'] = $request->quantity;
            $cart[$key]['subtotal'] = $cart[$key]['price'] * $request->quantity;
        }

        session(['cart' => $cart]);

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $subtotal = array_sum(array_column($cart, 'subtotal'));
        $tax = round($subtotal * 0.1);
        $total = $subtotal + $tax;

        return response()->json([
            'success'    => true,
            'cart_count' => $cartCount,
            'cart'       => array_values($cart),
            'subtotal'   => $subtotal,
            'tax'        => $tax,
            'total'      => $total,
        ]);
    }

    public function getCart()
    {
        $cart = session('cart', []);
        $subtotal = array_sum(array_column($cart, 'subtotal'));
        $tax = round($subtotal * 0.1);
        $total = $subtotal + $tax;

        return response()->json([
            'cart'       => array_values($cart),
            'cart_count' => array_sum(array_column($cart, 'quantity')),
            'subtotal'   => $subtotal,
            'tax'        => $tax,
            'total'      => $total,
        ]);
    }

    public function checkout()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.menu')->with('error', 'Keranjang kosong!');
        }

        $tableId = $this->getTableId();
        $table = CafeTable::findOrFail($tableId);
        $subtotal = array_sum(array_column($cart, 'subtotal'));
        $tax = round($subtotal * 0.1);
        $total = $subtotal + $tax;

        return view('customer.checkout', compact('cart', 'table', 'subtotal', 'tax', 'total'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,qr',
            'notes'          => 'nullable|string|max:500',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.menu')->with('error', 'Keranjang kosong!');
        }

        $tableId = $this->getTableId();
        $subtotal = array_sum(array_column($cart, 'subtotal'));
        $tax = round($subtotal * 0.1);
        $total = $subtotal + $tax;

        DB::transaction(function () use ($cart, $tableId, $subtotal, $tax, $total, $request) {
            $order = Order::create([
                'order_code'     => Order::generateCode(),
                'table_id'       => $tableId,
                'customer_name'  => $this->getCustomerName(),
                'session_id'     => session()->getId(),
                'subtotal'       => $subtotal,
                'tax'            => $tax,
                'total'          => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'qr' ? 'unpaid' : 'unpaid',
                'status'         => 'pending',
                'notes'          => $request->notes,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'  => $order->id,
                    'menu_id'   => $item['menu_id'],
                    'menu_name' => $item['name'],
                    'price'     => $item['price'],
                    'quantity'  => $item['quantity'],
                    'subtotal'  => $item['subtotal'],
                ]);
            }

            session(['cart' => [], 'last_order_id' => $order->id]);
        });

        return redirect()->route('customer.orders')->with('success', '🎉 Pesanan berhasil dibuat! Silakan tunggu.');
    }

    public function orders()
    {
        $tableId = $this->getTableId();
        if (!$tableId) return redirect()->route('guest.select');

        $orders = Order::with(['items', 'table'])
            ->where('table_id', $tableId)
            ->where('session_id', session()->getId())
            ->latest()
            ->get();

        $table = CafeTable::findOrFail($tableId);

        return view('customer.orders', compact('orders', 'table'));
    }

    public function orderStatus(Order $order)
    {
        return response()->json([
            'status'       => $order->status,
            'status_label' => $order->status_label,
            'status_color' => $order->status_color,
            'updated_at'   => $order->updated_at->diffForHumans(),
        ]);
    }

    public function generateQR(Request $request)
    {
        $request->validate(['amount' => 'required|integer|min:1']);
        // Returns data for QR generation (done client-side with qrcode.js)
        $tableId = $this->getTableId();
        $table = CafeTable::findOrFail($tableId);
        $qrData = json_encode([
            'merchant' => 'BrewNest Cafe',
            'table'    => 'Meja ' . $table->number,
            'amount'   => $request->amount,
            'currency' => 'IDR',
            'ref'      => 'BN-' . time(),
        ]);

        return response()->json(['qr_data' => $qrData, 'amount' => $request->amount]);
    }
}
