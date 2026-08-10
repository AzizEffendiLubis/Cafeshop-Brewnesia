@extends('layouts.app')
@section('title', 'Pesanan — Brewnesia Admin')

@push('styles')
<style>
.filter-bar{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:1rem 1.2rem;margin-bottom:1.5rem;display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;}
.filter-bar .form-group{margin:0;min-width:150px;}
.stat-pills{display:flex;gap:.7rem;flex-wrap:wrap;margin-bottom:1.5rem;}
.stat-pill{background:var(--card);border:1px solid var(--border);border-radius:30px;padding:.5rem 1.2rem;font-size:.82rem;display:flex;align-items:center;gap:.5rem;}
.order-row{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:1.2rem;margin-bottom:.9rem;transition:border-color .2s;}
.order-row:hover{border-color:var(--gold);}
.item-list{font-size:.82rem;color:var(--muted);margin:.5rem 0;}
</style>
@endpush

@section('content')
<nav class="navbar">
  <div class="nav-brand">Brewnesia ✦</div>
  <a href="{{ route('admin.dashboard') }}" class="nav-link">📊 Dashboard</a>
  <a href="{{ route('admin.orders.index') }}" class="nav-link active">📋 Pesanan</a>
  <a href="{{ route('admin.menus.index') }}" class="nav-link">🍽️ Menu</a>
  <form method="POST" action="{{ route('logout') }}" style="margin:0">@csrf
    <button type="submit" style="background:var(--border);border:none;color:var(--muted);padding:.5rem 1rem;border-radius:8px;cursor:pointer;font-size:.8rem;">Keluar</button>
  </form>
</nav>

<div class="container">
  <div class="flex-between mb-2">
    <div class="page-header" style="margin:0">
      <h1 class="page-title">Manajemen Pesanan</h1>
      <p class="page-sub">Terima dan proses pesanan pelanggan</p>
    </div>
    <button onclick="location.reload()" class="btn btn-secondary btn-sm">🔄 Refresh</button>
  </div>

  <!-- Stats Pills -->
  <div class="stat-pills">
    <div class="stat-pill">⏳ Menunggu: <strong style="color:var(--orange)">{{ $stats['pending'] }}</strong></div>
    <div class="stat-pill">🍳 Diproses: <strong style="color:var(--blue)">{{ $stats['process'] }}</strong></div>
    <div class="stat-pill">🔔 Siap: <strong style="color:var(--gold2)">{{ $stats['ready'] }}</strong></div>
    <div class="stat-pill">✅ Selesai Hari Ini: <strong style="color:var(--green)">{{ $stats['done_today'] }}</strong></div>
    <div class="stat-pill">💰 Revenue Hari Ini: <strong style="color:var(--gold2)">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</strong></div>
  </div>

  <!-- Filter -->
  <form method="GET" class="filter-bar">
    <div class="form-group">
      <label>Status</label>
      <select name="status" class="form-control" style="min-width:140px">
        <option value="all" @selected(request('status','all')==='all')>Semua Status</option>
        <option value="pending" @selected(request('status')==='pending')>⏳ Menunggu</option>
        <option value="process" @selected(request('status')==='process')>🍳 Diproses</option>
        <option value="ready" @selected(request('status')==='ready')>🔔 Siap</option>
        <option value="done" @selected(request('status')==='done')>✅ Selesai</option>
        <option value="cancelled" @selected(request('status')==='cancelled')>❌ Batal</option>
      </select>
    </div>
    <div class="form-group">
      <label>Meja</label>
      <select name="table" class="form-control">
        <option value="">Semua Meja</option>
        @foreach($tables as $t)
          <option value="{{ $t->id }}" @selected(request('table')==$t->id)>Meja {{ $t->number }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Tanggal</label>
      <input type="date" name="date" class="form-control" value="{{ request('date') }}">
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">Reset</a>
  </form>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <!-- Orders -->
  @forelse($orders as $order)
  <div class="order-row" id="order-{{ $order->id }}">
    <div class="flex-between" style="margin-bottom:.7rem">
      <div>
        <span style="font-family:'Playfair Display',serif;font-size:1.1rem;color:var(--cream)">{{ $order->order_code }}</span>
        <span style="font-size:.78rem;color:var(--muted);margin-left:.8rem">{{ $order->created_at->format('d/m H:i') }}</span>
      </div>
      <span class="status-badge status-{{ $order->status }}" id="status-{{ $order->id }}">{{ $order->status_label }}</span>
    </div>
    <div class="flex-between" style="margin-bottom:.7rem;flex-wrap:wrap;gap:.5rem">
      <div>
        <span class="table-chip">🪑 Meja {{ $order->table->number }}</span>
        <span style="margin-left:.5rem;font-size:.85rem">👤 {{ $order->customer_name }}</span>
        <span style="margin-left:.5rem;font-size:.82rem;color:var(--muted)">{{ $order->payment_method === 'cash' ? '💵 Cash' : '📱 QR' }}</span>
      </div>
      <span style="font-weight:700;color:var(--gold2)">{{ $order->formatted_total }}</span>
    </div>
    <div class="item-list">
      @foreach($order->items as $item)
        {{ $item->menu_name }} ×{{ $item->quantity }}@if(!$loop->last) · @endif
      @endforeach
    </div>
    @if($order->notes)
      <div style="font-size:.78rem;color:var(--muted);margin-bottom:.7rem">📝 {{ $order->notes }}</div>
    @endif
    <div style="display:flex;gap:.6rem;flex-wrap:wrap">
      @if($order->status === 'pending')
        <button class="btn btn-sm btn-info" onclick="updateStatus({{ $order->id }}, 'process', this)">▶ Mulai Proses</button>
        <button class="btn btn-sm btn-danger" onclick="updateStatus({{ $order->id }}, 'cancelled', this)">✕ Batalkan</button>
      @elseif($order->status === 'process')
        <button class="btn btn-sm btn-warning" onclick="updateStatus({{ $order->id }}, 'ready', this)">🔔 Siap Diambil</button>
        <button class="btn btn-sm btn-danger" onclick="updateStatus({{ $order->id }}, 'cancelled', this)">✕ Batalkan</button>
      @elseif($order->status === 'ready')
        <button class="btn btn-sm btn-success" onclick="updateStatus({{ $order->id }}, 'done', this)">✓ Tandai Selesai</button>
      @endif
      <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-secondary">📄 Detail</a>
    </div>
  </div>
  @empty
    <div class="card"><div class="empty"><div class="empty-icon">📭</div>Tidak ada pesanan</div></div>
  @endforelse

  {{ $orders->links() }}
</div>
@endsection

@push('scripts')
<script>
async function updateStatus(id, status, btn) {
  if (!confirm('Update status pesanan?')) return;
  btn.disabled = true;
  const oldText = btn.textContent;
  btn.textContent = '...';

  const res = await apiFetch(`/admin/orders/${id}/status`, {
    method: 'PATCH',
    body: JSON.stringify({ status })
  });

  if (res.success) {
    const badge = document.getElementById(`status-${id}`);
    if (badge) {
      badge.className = `status-badge status-${res.status}`;
      badge.textContent = res.status_label;
    }
    toast('✅ ' + res.status_label);
    setTimeout(() => location.reload(), 1000);
  } else {
    btn.disabled = false;
    btn.textContent = oldText;
    toast('Gagal update status', 'error');
  }
}

// Auto refresh pending count every 20s
setInterval(() => location.reload(), 60000);
</script>
@endpush
