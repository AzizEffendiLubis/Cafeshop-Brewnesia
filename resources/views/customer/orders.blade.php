@extends('layouts.app')
@section('title', 'Pesanan Saya — Brewnesia')

@push('styles')
<style>
.order-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:1.3rem;margin-bottom:1rem;transition:border-color .3s;}
.order-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;}
.order-code{font-family:'Playfair Display',serif;font-size:1.1rem;color:var(--cream);}
.order-meta{font-size:.78rem;color:var(--muted);}
.order-items-list{margin-bottom:1rem;}
.order-item-chip{display:inline-block;background:var(--bg3);border-radius:20px;padding:.3rem .8rem;font-size:.78rem;margin:.2rem;color:var(--text);}
.timeline{display:flex;align-items:center;gap:.3rem;margin-top:1rem;flex-wrap:wrap;}
.tl-step{display:flex;flex-direction:column;align-items:center;gap:.2rem;}
.tl-dot{width:12px;height:12px;border-radius:50%;background:var(--border);transition:all .4s;}
.tl-dot.done{background:var(--green);}
.tl-dot.active{background:var(--gold);box-shadow:0 0 10px var(--gold);}
.tl-label{font-size:.65rem;color:var(--muted);white-space:nowrap;}
.tl-line{flex:1;height:2px;background:var(--border);min-width:20px;transition:background .4s;}
.tl-line.done{background:var(--green);}
.order-total{color:var(--gold2);font-weight:700;font-size:1rem;}
.notes-chip{background:var(--bg3);border-radius:8px;padding:.4rem .8rem;font-size:.78rem;color:var(--muted);margin-top:.5rem;display:inline-block;}
.refresh-btn{background:var(--border);border:none;color:var(--muted);padding:.4rem .9rem;border-radius:8px;cursor:pointer;font-size:.8rem;float:right;}
</style>
@endpush

@section('content')
<nav class="navbar">
  <div class="nav-brand">Brewnesia ✦</div>
  <span class="table-chip">🪑 Meja {{ $table->number }}</span>
  <a href="{{ route('customer.menu') }}" class="nav-link">← Pesan Lagi</a>
</nav>

<div class="container" style="max-width:720px">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="flex-between mb-2">
    <div class="page-header" style="margin:0">
      <h1 class="page-title">📋 Pesanan Saya</h1>
      <p class="page-sub">Status pesanan diperbarui secara otomatis</p>
    </div>
    <button class="refresh-btn" onclick="refreshStatuses()">🔄 Refresh</button>
  </div>

  @if($orders->isEmpty())
    <div class="card">
      <div class="empty">
        <div class="empty-icon">📭</div>
        <p>Belum ada pesanan</p>
        <a href="{{ route('customer.menu') }}" class="btn btn-primary" style="margin-top:1rem">☕ Mulai Pesan</a>
      </div>
    </div>
  @else
    @foreach($orders as $order)
    <div class="order-card" id="order-{{ $order->id }}">
      <div class="order-header">
        <div>
          <div class="order-code">{{ $order->order_code }}</div>
          <div class="order-meta">{{ $order->created_at->format('d M Y, H:i') }} · {{ $order->payment_method === 'cash' ? '💵 Cash' : '📱 QR Code' }}</div>
        </div>
        <div class="status-badge status-{{ $order->status }}" id="badge-{{ $order->id }}">
          {{ $order->status_label }}
        </div>
      </div>

      <div class="order-items-list">
        @foreach($order->items as $item)
          <span class="order-item-chip">{{ $item->menu->emoji ?? '🍽️' }} {{ $item->menu_name }} ×{{ $item->quantity }}</span>
        @endforeach
      </div>

      @if($order->notes)
        <div class="notes-chip">📝 {{ $order->notes }}</div>
      @endif

      <div class="flex-between" style="margin-top:.8rem">
        <div class="order-total">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
      </div>

      <!-- Timeline -->
      <div class="timeline">
        @php
          $steps = ['pending', 'process', 'ready', 'done'];
          $labels = ['Diterima', 'Diproses', 'Siap', 'Selesai'];
          $currentIdx = array_search($order->status, $steps);
          if ($order->status === 'cancelled') $currentIdx = -1;
        @endphp
        @foreach($steps as $i => $step)
          <div class="tl-step">
            <div class="tl-dot {{ $i < $currentIdx ? 'done' : ($i === $currentIdx ? 'active' : '') }}" id="dot-{{ $order->id }}-{{ $i }}"></div>
            <div class="tl-label">{{ $labels[$i] }}</div>
          </div>
          @if($i < count($steps) - 1)
            <div class="tl-line {{ $i < $currentIdx ? 'done' : '' }}" id="line-{{ $order->id }}-{{ $i }}" style="margin-bottom:1rem"></div>
          @endif
        @endforeach
      </div>

      @if($order->status === 'cancelled')
        <div style="color:var(--red);font-size:.82rem;margin-top:.5rem">❌ Pesanan dibatalkan</div>
      @endif
    </div>
    @endforeach
  @endif
</div>
@endsection

@push('scripts')
<script>
const orderIds = @json($orders->pluck('id'));
const steps = ['pending', 'process', 'ready', 'done'];

async function refreshStatuses() {
  for (const id of orderIds) {
    try {
      const res = await fetch(`/order/order-status/${id}`, {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
      });
      const data = await res.json();
      updateOrderUI(id, data.status);
    } catch (e) {}
  }
}

function updateOrderUI(id, status) {
  // Update badge
  const badge = document.getElementById(`badge-${id}`);
  if (badge) {
    badge.className = `status-badge status-${status}`;
    const labels = {pending:'⏳ Menunggu', process:'🍳 Diproses', ready:'🔔 Siap Diambil', done:'✅ Selesai', cancelled:'❌ Dibatalkan'};
    badge.textContent = labels[status] || status;
  }

  // Update timeline
  const currentIdx = steps.indexOf(status);
  steps.forEach((step, i) => {
    const dot = document.getElementById(`dot-${id}-${i}`);
    const line = document.getElementById(`line-${id}-${i}`);
    if (dot) {
      dot.className = 'tl-dot ' + (i < currentIdx ? 'done' : (i === currentIdx ? 'active' : ''));
    }
    if (line) {
      line.className = 'tl-line ' + (i < currentIdx ? 'done' : '');
    }
  });

  // Highlight ready orders
  const card = document.getElementById(`order-${id}`);
  if (card) {
    card.style.borderColor = status === 'ready' ? 'var(--gold)' : '';
    if (status === 'ready') toast('🔔 Pesanan Anda sudah siap diambil!');
  }
}

// Auto refresh every 15 seconds
setInterval(refreshStatuses, 15000);
</script>
@endpush
