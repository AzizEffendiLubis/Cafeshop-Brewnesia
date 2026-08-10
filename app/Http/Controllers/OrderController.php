<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CafeTable;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['table', 'items'])->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('table')) {
            $query->where('table_id', $request->table);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->paginate(15)->withQueryString();
        $tables = CafeTable::orderBy('number')->get();

        $stats = [
            'pending'  => Order::where('status', 'pending')->count(),
            'process'  => Order::where('status', 'process')->count(),
            'ready'    => Order::where('status', 'ready')->count(),
            'done_today' => Order::where('status', 'done')->whereDate('updated_at', today())->count(),
            'revenue_today' => Order::where('status', 'done')->whereDate('updated_at', today())->sum('total'),
        ];

        return view('admin.orders.index', compact('orders', 'tables', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['table', 'items.menu']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,process,ready,done,cancelled']);
        $order->update(['status' => $request->status]);

        if (request()->wantsJson()) {
            return response()->json([
                'success'      => true,
                'status'       => $order->status,
                'status_label' => $order->status_label,
                'status_color' => $order->status_color,
            ]);
        }

        return back()->with('success', "Status pesanan #" . $order->order_code . " diperbarui!");
    }

    public function dashboard()
    {
        $today = today();

        $stats = [
            'total_orders'    => Order::count(),
            'today_orders'    => Order::whereDate('created_at', $today)->count(),
            'pending_orders'  => Order::where('status', 'pending')->count(),
            'process_orders'  => Order::where('status', 'process')->count(),
            'ready_orders'    => Order::where('status', 'ready')->count(),
            'done_today'      => Order::where('status', 'done')->whereDate('updated_at', $today)->count(),
            'revenue_today'   => Order::where('status', 'done')->whereDate('updated_at', $today)->sum('total'),
            'revenue_total'   => Order::where('status', 'done')->sum('total'),
            'total_menus'     => \App\Models\Menu::count(),
            'active_tables'   => CafeTable::where('status', 'occupied')->count(),
        ];

        $recentOrders = Order::with(['table', 'items'])
            ->latest()->limit(10)->get();

        $popularMenus = \App\Models\OrderItem::selectRaw('menu_name, sum(quantity) as total_qty, sum(subtotal) as total_revenue')
            ->groupBy('menu_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'popularMenus'));
    }

    // Live polling endpoint for admin
    public function liveOrders()
    {
        $orders = Order::with(['table', 'items'])
            ->whereNotIn('status', ['done', 'cancelled'])
            ->latest()->get();

        return response()->json($orders);
    }
}
