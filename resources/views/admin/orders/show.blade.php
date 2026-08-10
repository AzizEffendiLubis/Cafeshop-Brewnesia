@extends('layouts.app')
@section('title', 'Detail Pesanan — Brewnesia')

@section('content')
<nav class="navbar">
  <div class="nav-brand">Brewnesia ✦</div>
  <a href="{{ route('admin.orders.index') }}" class="nav-link">← Kembali</a>
</nav>

<div class="container" style="max-width:720px">
  <div class="flex-between mb-2">
    <div class="page-header" style="margin:0">
      <h1 class="page-title">{{ $order->order_code }}</h1>
      <p class="page-sub">{{ $order->created_at->format('l, d F Y · H:i') }}</p>
    </div>
    <span class="status-badge status-{{ $order->status }}" style="font-size:.9rem;padding:.5rem 1.2rem">{{ $order->status_label }}</span>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <!-- Info -->
  <div class="card mb-2">
    <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
      <div>
        <div class="text-muted" style="font-size:.78rem;margin-bottom:.2rem">Meja</div>
        <div style="font-weight:600">🪑 Meja {{ $order->table->number }}</div>
      </div>
      <div>
        <div class="text-muted" style="font-size:.78rem;margin-bottom:.2rem">Pelanggan</div>
        <div style="font-weight:600">{{ $order->customer_name }}</div>
      </div>
      <div>
        <div class="text-muted" style="font-size:.78rem;margin-bottom:.2rem">Pembayaran</div>
        <div>{{ $order->payment_method === 'cash' ? '💵 Cash' : '📱 QR Code' }}</div>
      </div>
      <div>
        <div class="text-muted" style="font-size:.78rem;margin-bottom:.2rem">Status Bayar</div>
        <div>{{ $order->payment_status === 'paid' ? '✅ Lunas' : '⏳ Belum Bayar' }}</div>
      </div>
      @if($order->notes)
      <div style="grid-column:1/-1">
        <div class="text-muted" style="font-size:.78rem;margin-bottom:.2rem">Catatan</div>
        <div>📝 {{ $order->notes }}</div>
      </div>
      @endif
    </div>
  </div>

  <!-- Items -->
  <div class="card mb-2">
    <div class="card-header"><span class="section-title" style="margin:0">Item Pesanan</span></div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Menu</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
          @foreach($order->items as $item)
          <tr>
            <td>
              <div style="font-weight:500">{{ $item->menu->emoji ?? '🍽️' }} {{ $item->menu_name }}</div>
              @if($item->notes)<div style="font-size:.75rem;color:var(--muted)">{{ $item->notes }}</div>@endif
            </td>
            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
            <td>{{ $item->quantity }}</td>
            <td style="color:var(--gold2);font-weight:600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-body" style="border-top:1px solid var(--border)">
      <div class="flex-between" style="margin-bottom:.4rem"><span class="text-muted">Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
      <div class="flex-between" style="margin-bottom:.8rem"><span class="text-muted">Pajak (10%)</span><span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span></div>
      <div class="flex-between" style="font-weight:700;font-size:1.1rem;color:var(--gold2)"><span>Total</span><span>Rp {{ number_format($order->total, 0, ',', '.') }}</span></div>
    </div>
  </div>

  <!-- Update Status -->
  @if(!in_array($order->status, ['done', 'cancelled']))
  <div class="card mb-2">
    <div class="card-header"><span class="section-title" style="margin:0">Update Status</span></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.orders.status', $order) }}">
        @csrf @method('PATCH')
        <div class="flex" style="gap:.8rem;flex-wrap:wrap">
          <select name="status" class="form-control" style="flex:1;min-width:180px">
            @foreach(['pending' => '⏳ Menunggu', 'process' => '🍳 Diproses', 'ready' => '🔔 Siap', 'done' => '✅ Selesai', 'cancelled' => '❌ Dibatalkan'] as $val => $lbl)
              <option value="{{ $val }}" @selected($order->status === $val)>{{ $lbl }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary">Update Status</button>
        </div>
      </form>
    </div>
  </div>
  @endif

  <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">← Kembali ke Daftar Pesanan</a>
</div>
@endsection
