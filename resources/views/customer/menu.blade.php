@extends('layouts.app')

@section('title', 'Menu — Brewnesia Meja ' . $table->number)

@push('styles')
<style>
.cat-tabs{display:flex;gap:.7rem;flex-wrap:wrap;margin-bottom:1.5rem;}
.cat-tab{background:var(--card);border:1px solid var(--border);border-radius:30px;padding:.5rem 1.2rem;cursor:pointer;font-size:.85rem;transition:all .2s;color:var(--muted);display:inline-flex;align-items:center;gap:.4rem;}
.cat-tab:hover{border-color:var(--gold);color:var(--text);}
.cat-tab.active{background:var(--gold);border-color:var(--gold);color:#000;font-weight:600;}
.menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:1.2rem;}
.menu-card{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;transition:all .25s;cursor:pointer;}
.menu-card:hover{transform:translateY(-3px);border-color:var(--gold);box-shadow:0 8px 30px rgba(212,168,83,.1);}
.menu-card.unavailable{opacity:.4;pointer-events:none;}
.menu-img{height:130px;display:flex;align-items:center;justify-content:center;font-size:3.5rem;background:var(--bg3);}
.menu-body{padding:1rem;}
.menu-name{font-weight:600;font-size:.95rem;margin-bottom:.25rem;}
.menu-desc{color:var(--muted);font-size:.78rem;line-height:1.5;margin-bottom:.8rem;min-height:2.4rem;}
.menu-footer{display:flex;align-items:center;justify-content:space-between;}
.menu-price{color:var(--gold2);font-weight:700;font-size:1rem;}
.btn-add{background:var(--gold);border:none;color:#000;border-radius:8px;padding:.4rem .9rem;cursor:pointer;font-weight:700;font-size:.8rem;transition:all .2s;}
.btn-add:hover{background:var(--gold2);}
.cart-float{position:fixed;right:1.5rem;bottom:2rem;z-index:199;background:var(--gold);border:none;border-radius:50px;padding:.9rem 1.5rem;font-size:.9rem;font-weight:700;color:#000;cursor:pointer;box-shadow:0 4px 20px rgba(212,168,83,.4);display:none;align-items:center;gap:.7rem;transition:all .2s;}
.cart-float:hover{background:var(--gold2);transform:scale(1.04);}
.cart-float.show{display:flex;}
.cart-sidebar{position:fixed;right:0;top:0;bottom:0;width:380px;background:var(--bg2);border-left:1px solid var(--border);display:flex;flex-direction:column;transform:translateX(100%);transition:transform .35s cubic-bezier(.4,0,.2,1);z-index:200;padding:1.5rem;}
.cart-sidebar.open{transform:translateX(0);}
.cart-items{flex:1;overflow-y:auto;}
.cart-item{background:var(--card);border-radius:12px;padding:.9rem;margin-bottom:.8rem;display:flex;align-items:center;gap:.9rem;}
.cart-item-icon{font-size:1.8rem;width:40px;text-align:center;}
.cart-item-info{flex:1;}
.cart-item-name{font-size:.88rem;font-weight:600;}
.cart-item-price{color:var(--gold2);font-size:.82rem;}
.qty-ctrl{display:flex;align-items:center;gap:.5rem;}
.qty-btn{background:var(--bg3);border:1px solid var(--border);color:var(--text);width:28px;height:28px;border-radius:6px;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;transition:all .2s;}
.qty-btn:hover{border-color:var(--gold);}
.cart-total{border-top:1px solid var(--border);padding-top:1rem;margin-top:.5rem;}
.total-row{display:flex;justify-content:space-between;margin-bottom:.5rem;font-size:.88rem;}
.total-row.grand{font-weight:700;font-size:1.05rem;color:var(--gold2);}
.overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:199;display:none;}
.overlay.show{display:block;}
@media(max-width:600px){.cart-sidebar{width:100%;}.menu-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr));}}
</style>
@endpush

@section('content')
<nav class="navbar">
  <div class="nav-brand">Brewnesia ✦</div>
  <span class="table-chip">🪑 Meja {{ $table->number }}</span>
  <span class="table-chip" style="color:var(--text)">👤 {{ session('guest_customer_name') }}</span>
  <a href="{{ route('customer.orders') }}" class="nav-link">📋 Pesanan Saya</a>
  <form method="POST" action="{{ route('logout') }}" style="margin:0">
    @csrf
    <button type="submit" style="background:var(--border);border:none;color:var(--muted);padding:.5rem 1rem;border-radius:8px;cursor:pointer;font-size:.8rem;">Keluar</button>
  </form>
</nav>

<div class="container">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
  @endif

  <div class="page-header">
    <h1 class="page-title">Pilih Menu Favorit</h1>
    <p class="page-sub">Halo {{ session('guest_customer_name') }}! Temukan hidangan lezat untuk Meja {{ $table->number }} ✨</p>
  </div>

  <!-- Category Filter -->
  <div class="cat-tabs" id="catTabs">
    <div class="cat-tab active" data-cat="all" onclick="filterCat('all', this)">🍽️ Semua</div>
    @foreach($categories as $cat)
      <div class="cat-tab" data-cat="{{ $cat->id }}" onclick="filterCat('{{ $cat->id }}', this)">
        {{ $cat->icon }} {{ $cat->name }}
      </div>
    @endforeach
  </div>

  <!-- Menu Grid -->
  <div class="menu-grid" id="menuGrid">
    @foreach($categories as $cat)
      @foreach($cat->availableMenus as $menu)
        <div class="menu-card {{ !$menu->is_available ? 'unavailable' : '' }}" data-cat="{{ $cat->id }}">
          <div class="menu-img">
            @if($menu->image)
              <img src="{{ asset('storage/' . $menu->image) }}" style="width:100%;height:100%;object-fit:cover;" alt="{{ $menu->name }}">
            @else
              {{ $menu->emoji }}
            @endif
          </div>
          <div class="menu-body">
            <div class="menu-name">{{ $menu->name }}</div>
            <div class="menu-desc">{{ $menu->description }}</div>
            <div class="menu-footer">
              <div class="menu-price">{{ $menu->formatted_price }}</div>
              @if($menu->is_available)
                <button class="btn-add" onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', '{{ $menu->emoji }}')">+ Tambah</button>
              @else
                <span style="font-size:.75rem;color:var(--red)">Habis</span>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    @endforeach
  </div>
</div>

<!-- Cart Overlay -->
<div class="overlay" id="overlay" onclick="closeCart()"></div>

<!-- Cart Float Button -->
<button class="cart-float" id="cartFloat" onclick="toggleCart()">
  🛒 <span id="cartFloatText">Keranjang</span>
  <span class="badge" id="cartBadge">0</span>
</button>

<!-- Cart Sidebar -->
<div class="cart-sidebar" id="cartSidebar">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.2rem">
    <div style="font-family:'Playfair Display',serif;font-size:1.4rem;color:var(--cream)">🛒 Keranjang</div>
    <button onclick="closeCart()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.3rem">✕</button>
  </div>
  <div class="cart-items" id="cartItems">
    <div class="empty"><div class="empty-icon">🛒</div>Keranjang masih kosong</div>
  </div>
  <div class="form-group" style="margin-top:.8rem;margin-bottom:.5rem">
    <label>Catatan (opsional)</label>
    <textarea class="form-control" id="orderNote" rows="2" placeholder="Kurang manis, tidak pedas..."></textarea>
  </div>
  <div class="cart-total">
    <div class="total-row"><span>Subtotal</span><span id="cartSubtotal">Rp 0</span></div>
    <div class="total-row"><span>Pajak (10%)</span><span id="cartTax">Rp 0</span></div>
    <div class="total-row grand"><span>Total</span><span id="cartGrand">Rp 0</span></div>
  </div>
  <a href="{{ route('customer.checkout') }}" id="checkoutBtn" class="btn btn-primary" style="width:100%;margin-top:.8rem;text-align:center;display:none;">Checkout →</a>
</div>
@endsection

@push('scripts')
<script>
let cart = {};

function fmt(n) { return 'Rp ' + n.toLocaleString('id-ID'); }

async function addToCart(menuId, name, emoji) {
  const res = await apiFetch('{{ route("customer.cart.add") }}', {
    method: 'POST',
    body: JSON.stringify({ menu_id: menuId })
  });
  if (res.success) {
    toast(res.message);
    cart[menuId] = cart[menuId] || { menu_id: menuId, name, emoji, quantity: 0 };
    cart[menuId].quantity++;
    refreshCartUI();
  } else {
    toast(res.message, 'error');
  }
}

async function updateQty(menuId, qty) {
  const res = await apiFetch('{{ route("customer.cart.update") }}', {
    method: 'POST',
    body: JSON.stringify({ menu_id: menuId, quantity: qty })
  });
  if (res.success) {
    if (qty === 0) delete cart[menuId];
    else if (cart[menuId]) cart[menuId].quantity = qty;
    renderCartItems(res.cart, res.subtotal, res.tax, res.total);
    updateBadge(res.cart_count);
  }
}

function refreshCartUI() {
  apiFetch('{{ route("customer.cart.get") }}').then(res => {
    renderCartItems(res.cart, res.subtotal, res.tax, res.total);
    updateBadge(res.cart_count);
    res.cart.forEach(item => {
      cart[item.menu_id] = item;
    });
  });
}

function renderCartItems(items, subtotal, tax, total) {
  const container = document.getElementById('cartItems');
  const checkoutBtn = document.getElementById('checkoutBtn');

  if (!items.length) {
    container.innerHTML = '<div class="empty"><div class="empty-icon">🛒</div>Keranjang masih kosong</div>';
    checkoutBtn.style.display = 'none';
    return;
  }

  checkoutBtn.style.display = 'block';
  container.innerHTML = items.map(item => `
    <div class="cart-item">
      <div class="cart-item-icon">${item.emoji}</div>
      <div class="cart-item-info">
        <div class="cart-item-name">${item.name}</div>
        <div class="cart-item-price">${fmt(item.subtotal)}</div>
      </div>
      <div class="qty-ctrl">
        <button class="qty-btn" onclick="updateQty(${item.menu_id}, ${item.quantity - 1})">−</button>
        <span style="min-width:20px;text-align:center">${item.quantity}</span>
        <button class="qty-btn" onclick="updateQty(${item.menu_id}, ${item.quantity + 1})">+</button>
      </div>
    </div>
  `).join('');

  document.getElementById('cartSubtotal').textContent = fmt(subtotal);
  document.getElementById('cartTax').textContent = fmt(tax);
  document.getElementById('cartGrand').textContent = fmt(total);
}

function updateBadge(count) {
  const badge = document.getElementById('cartBadge');
  const floatBtn = document.getElementById('cartFloat');
  const text = document.getElementById('cartFloatText');
  badge.textContent = count;
  if (count > 0) {
    floatBtn.classList.add('show');
    text.textContent = `Keranjang (${count})`;
  } else {
    floatBtn.classList.remove('show');
  }
}

function toggleCart() {
  const sidebar = document.getElementById('cartSidebar');
  const overlay = document.getElementById('overlay');
  sidebar.classList.toggle('open');
  overlay.classList.toggle('show');
  if (sidebar.classList.contains('open')) refreshCartUI();
}

function closeCart() {
  document.getElementById('cartSidebar').classList.remove('open');
  document.getElementById('overlay').classList.remove('show');
}

// Category filter
function filterCat(cat, el) {
  document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  const cards = document.querySelectorAll('.menu-card');
  cards.forEach(card => {
    if (cat === 'all' || card.dataset.cat == cat) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
}

// Save note to session on checkout
document.getElementById('checkoutBtn')?.addEventListener('click', function(e) {
  const note = document.getElementById('orderNote').value;
  sessionStorage.setItem('orderNote', note);
});

// Init
refreshCartUI();
</script>
@endpush
