@extends('layouts.app')
@section('title', 'Checkout — BrewNest')

@push('styles')
<style>
.checkout-wrap{max-width:640px;margin:0 auto;}
.pay-opts{display:flex;gap:1rem;margin-bottom:1.5rem;}
.pay-opt{flex:1;background:var(--card);border:2px solid var(--border);border-radius:14px;padding:1.3rem;text-align:center;cursor:pointer;transition:all .2s;}
.pay-opt:hover,.pay-opt.selected{border-color:var(--gold);background:rgba(212,168,83,.07);}
.pay-opt .pi{font-size:2.2rem;margin-bottom:.5rem;}
.pay-opt span{font-size:.9rem;font-weight:600;}
.order-item-row{display:flex;align-items:center;justify-content:space-between;padding:.7rem 0;border-bottom:1px solid var(--border);}
.order-item-row:last-child{border-bottom:none;}
.total-row{display:flex;justify-content:space-between;padding:.5rem 0;font-size:.9rem;}
.total-row.grand{font-weight:700;font-size:1.1rem;color:var(--gold2);border-top:1px solid var(--border);padding-top:.8rem;margin-top:.3rem;}
#qrSection{display:none;}
#qrcode{background:white;padding:1.2rem;border-radius:12px;display:inline-block;margin:1rem auto;display:block;text-align:center;}
#qrcode canvas,#qrcode img{margin:0 auto;}
.change-display{font-size:.9rem;margin-top:.5rem;padding:.5rem .8rem;border-radius:8px;}
</style>
@endpush

@section('content')
<nav class="navbar">
  <div class="nav-brand">BrewNest ✦</div>
  <span class="table-chip">🪑 Meja {{ $table->number }}</span>
  <a href="{{ route('customer.menu') }}" class="nav-link">← Kembali ke Menu</a>
</nav>

<div class="container">
  <div class="checkout-wrap">
    <div class="page-header">
      <h1 class="page-title">🧾 Konfirmasi Pesanan</h1>
      <p class="page-sub">Periksa pesanan Anda sebelum mengirim</p>
    </div>

    <!-- Order Summary -->
    <div class="card mb-2">
      <div class="card-header">
        <span class="section-title" style="margin:0">Ringkasan Pesanan</span>
        <span class="table-chip">Meja {{ $table->number }}</span>
      </div>
      <div class="card-body">
        @foreach($cart as $item)
        <div class="order-item-row">
          <div>
            <span style="font-size:1.2rem">{{ $item['emoji'] }}</span>
            <span style="margin-left:.5rem;font-weight:500">{{ $item['name'] }}</span>
            <span class="text-muted" style="font-size:.82rem"> × {{ $item['quantity'] }}</span>
          </div>
          <div class="text-gold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
        </div>
        @endforeach
        <div class="total-row" style="margin-top:.8rem"><span class="text-muted">Subtotal</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
        <div class="total-row"><span class="text-muted">Pajak (10%)</span><span>Rp {{ number_format($tax, 0, ',', '.') }}</span></div>
        <div class="total-row grand"><span>Total Pembayaran</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
      </div>
    </div>

    <!-- Payment Form -->
    <form method="POST" action="{{ route('customer.place-order') }}" id="orderForm">
      @csrf
      <div class="card mb-2">
        <div class="card-header"><span class="section-title" style="margin:0">Metode Pembayaran</span></div>
        <div class="card-body">
          <input type="hidden" name="payment_method" id="payMethodInput" value="cash">
          <div class="pay-opts">
            <div class="pay-opt selected" id="optCash" onclick="selectPay('cash')">
              <div class="pi">💵</div>
              <span>Bayar Cash</span>
            </div>
            <div class="pay-opt" id="optQR" onclick="selectPay('qr')">
              <div class="pi">📱</div>
              <span>Scan QR Code</span>
            </div>
          </div>

          <!-- Cash Section -->
          <div id="cashSection">
            <div class="form-group">
              <label>Nominal Uang yang Dibayarkan</label>
              <input type="number" id="cashInput" class="form-control" placeholder="Masukkan nominal..." oninput="calcChange()">
            </div>
            <div id="changeDisplay" class="change-display"></div>
          </div>

          <!-- QR Section -->
          <div id="qrSection">
            <p class="text-muted" style="font-size:.85rem;margin-bottom:1rem">Scan QR code di bawah untuk melakukan pembayaran sebesar <strong class="text-gold">Rp {{ number_format($total, 0, ',', '.') }}</strong></p>
            <div id="qrcode"></div>
            <p style="text-align:center;font-size:.78rem;color:var(--muted);margin-top:.5rem">QR Code untuk pembayaran digital</p>
          </div>
        </div>
      </div>

      <div class="card mb-2">
        <div class="card-body">
          <div class="form-group" style="margin:0">
            <label>Catatan Pesanan (Opsional)</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Kurang manis, tidak pedas, dll..." id="notesField"></textarea>
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;padding:1rem;font-size:1rem" id="submitBtn">
        🍽️ Kirim Pesanan
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
const TOTAL = {{ $total }};
let qrGenerated = false;

function selectPay(method) {
  document.getElementById('payMethodInput').value = method;
  document.getElementById('optCash').classList.toggle('selected', method === 'cash');
  document.getElementById('optQR').classList.toggle('selected', method === 'qr');
  document.getElementById('cashSection').style.display = method === 'cash' ? 'block' : 'none';
  document.getElementById('qrSection').style.display = method === 'qr' ? 'block' : 'none';

  if (method === 'qr' && !qrGenerated) {
    const qrData = JSON.stringify({
      merchant: 'BrewNest Cafe',
      table: 'Meja {{ $table->number }}',
      amount: TOTAL,
      currency: 'IDR',
      ref: 'BN-' + Date.now()
    });
    new QRCode(document.getElementById('qrcode'), {
      text: qrData, width: 220, height: 220,
      colorDark: '#000000', colorLight: '#ffffff',
    });
    qrGenerated = true;
  }
}

function calcChange() {
  const paid = parseInt(document.getElementById('cashInput').value) || 0;
  const el = document.getElementById('changeDisplay');
  if (paid === 0) { el.textContent = ''; el.style.background = ''; return; }
  const diff = paid - TOTAL;
  if (diff < 0) {
    el.textContent = `⚠️ Kurang: Rp ${Math.abs(diff).toLocaleString('id-ID')}`;
    el.style.background = 'rgba(224,82,82,.1)'; el.style.color = 'var(--red)';
  } else {
    el.textContent = `✅ Kembalian: Rp ${diff.toLocaleString('id-ID')}`;
    el.style.background = 'rgba(76,175,125,.1)'; el.style.color = 'var(--green)';
  }
}

// Pre-fill note from session storage
const savedNote = sessionStorage.getItem('orderNote');
if (savedNote) { document.getElementById('notesField').value = savedNote; }

document.getElementById('orderForm').addEventListener('submit', function(e) {
  const method = document.getElementById('payMethodInput').value;
  if (method === 'cash') {
    const paid = parseInt(document.getElementById('cashInput').value) || 0;
    if (paid < TOTAL) { e.preventDefault(); alert('Nominal uang kurang dari total pembayaran!'); return; }
  }
  document.getElementById('submitBtn').textContent = '⏳ Memproses...';
  document.getElementById('submitBtn').disabled = true;
  sessionStorage.removeItem('orderNote');
});
</script>
@endpush
