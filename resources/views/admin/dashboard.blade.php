@extends('layouts.app')
@section('title', 'Dashboard Admin — Brewnesia')

@push('styles')
<style>
.stat-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:1.3rem;position:relative;overflow:hidden;}
.stat-icon{font-size:1.8rem;margin-bottom:.5rem;}
.stat-val{font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:var(--gold2);}
.stat-lbl{color:var(--muted);font-size:.8rem;margin-top:.2rem;}
.stat-card::after{content:'';position:absolute;bottom:-15px;right:-15px;width:70px;height:70px;border-radius:50%;background:rgba(212,168,83,.05);}
.popular-item{display:flex;align-items:center;gap:.8rem;padding:.8rem;background:var(--bg3);border-radius:10px;margin-bottom:.6rem;}
.popular-rank{font-family:'Playfair Display',serif;font-size:1.2rem;color:var(--gold);min-width:24px;}
.notification-dot{width:10px;height:10px;border-radius:50%;background:var(--red);display:inline-block;animation:pulse 1.5s infinite;}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
</style>
@endpush

@section('content')
<nav class="navbar">
  <div class="nav-brand">Brewnesia ✦</div>
  <span class="table-chip">👨‍🍳 Admin Panel</span>
  <a href="{{ route('admin.dashboard') }}" class="nav-link active">📊 Dashboard</a>
  <a href="{{ route('admin.orders.index') }}" class="nav-link">
    📋 Pesanan
    @if($stats['pending_orders'] > 0)
      <span class="badge badge-red" id="pendingBadge">{{ $stats['pending_orders'] }}</span>
    @endif
  </a>
  <a href="{{ route('admin.menus.index') }}" class="nav-link">🍽️ Menu</a>
  <form method="POST" action="{{ route('logout') }}" style="margin:0">
    @csrf
    <button type="submit" style="background:var(--border);border:none;color:var(--muted);padding:.5rem 1rem;border-radius:8px;cursor:pointer;font-size:.8rem;">Keluar</button>
  </form>
</nav>

<div class="container">
  <div class="flex-between mb-2">
    <div class="page-header" style="margin:0">
      <h1 class="page-title">Dashboard</h1>
      <p class="page-sub">{{ now()->format('l, d F Y') }}</p>
    </div>
    @if($stats['pending_orders'] > 0)
      <div style="background:rgba(224,82,82,.1);border:1px solid var(--red);border-radius:10px;padding:.7rem 1.2rem;font-size:.85rem;color:var(--red);display:flex;align-items:center;gap:.6rem">
        <span class="notification-dot"></span> {{ $stats['pending_orders'] }} pesanan menunggu konfirmasi!
      </div>
    @endif
  </div>

  <!-- Stats -->
  <div class="grid-4 mb-2">
    <div class="stat-card">
      <div class="stat-icon">📋</div>
      <div class="stat-val">{{ $stats['today_orders'] }}</div>
      <div class="stat-lbl">Pesanan Hari Ini</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">⏳</div>
      <div class="stat-val">{{ $stats['pending_orders'] }}</div>
      <div class="stat-lbl">Menunggu Proses</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🍳</div>
      <div class="stat-val">{{ $stats['process_orders'] }}</div>
      <div class="stat-lbl">Sedang Diproses</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">✅</div>
      <div class="stat-val">{{ $stats['done_today'] }}</div>
      <div class="stat-lbl">Selesai Hari Ini</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">💰</div>
      <div class="stat-val" style="font-size:1.3rem">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</div>
      <div class="stat-lbl">Pendapatan Hari Ini</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">💎</div>
      <div class="stat-val" style="font-size:1.3rem">Rp {{ number_format($stats['revenue_total'], 0, ',', '.') }}</div>
      <div class="stat-lbl">Total Pendapatan</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🍽️</div>
      <div class="stat-val">{{ $stats['total_menus'] }}</div>
      <div class="stat-lbl">Total Menu</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">📊</div>
      <div class="stat-val">{{ $stats['total_orders'] }}</div>
      <div class="stat-lbl">Total Semua Pesanan</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;flex-wrap:wrap" id="dashGrid">
    <!-- Recent Orders -->
    <div>
      <div class="flex-between mb-2" style="align-items:center">
        <h2 class="section-title" style="margin:0">Pesanan Masuk Terbaru</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-info">Lihat Semua →</a>
      </div>

      @forelse($recentOrders as $order)
      <div class="card mb-2">
        <div class="card-body">
          <div class="flex-between" style="margin-bottom:.7rem">
            <div>
              <div style="font-weight:600;font-size:.95rem">{{ $order->order_code }}</div>
              <div style="font-size:.78rem;color:var(--muted)">🪑 Meja {{ $order->table->number }} · {{ $order->customer_name }} · {{ $order->created_at->diffForHumans() }}</div>
            </div>
            <span class="status-badge status-{{ $order->status }}">{{ $order->status_label }}</span>
          </div>
          <div style="margin-bottom:.7rem;font-size:.82rem;color:var(--muted)">
            @foreach($order->items->take(3) as $item)
              {{ $item->menu_name }} ×{{ $item->quantity }}@if(!$loop->last), @endif
            @endforeach
            @if($order->items->count() > 3)  +{{ $order->items->count() - 3 }} lagi @endif
          </div>
          <div class="flex-between">
            <span style="font-weight:700;color:var(--gold2)">{{ $order->formatted_total }}</span>
            <div style="display:flex;gap:.5rem">
              @if($order->status === 'pending')
                <button class="btn btn-sm btn-info" onclick="updateStatus({{ $order->id }}, 'process', this)">▶ Proses</button>
              @elseif($order->status === 'process')
                <button class="btn btn-sm btn-warning" onclick="updateStatus({{ $order->id }}, 'ready', this)">🔔 Siap</button>
              @elseif($order->status === 'ready')
                <button class="btn btn-sm btn-success" onclick="updateStatus({{ $order->id }}, 'done', this)">✓ Selesai</button>
              @endif
              <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-secondary">Detail</a>
            </div>
          </div>
        </div>
      </div>
      @empty
        <div class="card"><div class="empty"><div class="empty-icon">📭</div>Belum ada pesanan</div></div>
      @endforelse
    </div>

    <!-- Sidebar: Popular Menu -->
    <div>
      <h2 class="section-title">🏆 Menu Terpopuler</h2>
      @forelse($popularMenus as $i => $item)
        <div class="popular-item">
          <div class="popular-rank">#{{ $i + 1 }}</div>
          <div style="flex:1">
            <div style="font-weight:600;font-size:.88rem">{{ $item->menu_name }}</div>
            <div style="font-size:.75rem;color:var(--muted)">{{ $item->total_qty }} porsi terjual</div>
          </div>
          <div style="color:var(--gold2);font-size:.82rem;font-weight:600">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</div>
        </div>
      @empty
        <div class="empty" style="padding:1.5rem"><div class="empty-icon">📊</div>Belum ada data</div>
      @endforelse

      <div class="mt-2">
        <h2 class="section-title">🔗 Aksi Cepat</h2>
        <div style="display:flex;flex-direction:column;gap:.7rem">
          <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">+ Tambah Menu Baru</a>
          <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-warning">⏳ Pesanan Pending</a>
          <a href="{{ route('admin.orders.index') }}" class="btn btn-info">📋 Semua Pesanan</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function updateStatus(id, status, btn) {
  btn.disabled = true;
  btn.textContent = '...';
  const res = await apiFetch(`/admin/orders/${id}/status`, {
    method: 'PATCH',
    body: JSON.stringify({ status })
  });
  if (res.success) {
    toast('✅ Status pesanan diperbarui!');
    setTimeout(() => location.reload(), 800);
  }
}

// Auto refresh every 30 seconds for live order notification
setInterval(async () => {
  const res = await apiFetch('{{ route("admin.live-orders") }}');
  if (Array.isArray(res)) {
    const pending = res.filter(o => o.status === 'pending').length;
    const badge = document.getElementById('pendingBadge');
    if (badge) badge.textContent = pending;
  }
}, 30000);
</script>
@endpush
