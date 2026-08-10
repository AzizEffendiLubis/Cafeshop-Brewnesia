@extends('layouts.app')
@section('title', 'Kelola Menu — Brewnesia')

@section('content')
<nav class="navbar">
  <div class="nav-brand">Brewnesia ✦</div>
  <a href="{{ route('admin.dashboard') }}" class="nav-link">📊 Dashboard</a>
  <a href="{{ route('admin.orders.index') }}" class="nav-link">📋 Pesanan</a>
  <a href="{{ route('admin.menus.index') }}" class="nav-link active">🍽️ Menu</a>
  <form method="POST" action="{{ route('logout') }}" style="margin:0">@csrf
    <button type="submit" style="background:var(--border);border:none;color:var(--muted);padding:.5rem 1rem;border-radius:8px;cursor:pointer;font-size:.8rem;">Keluar</button>
  </form>
</nav>

<div class="container">
  <div class="flex-between mb-2">
    <div class="page-header" style="margin:0">
      <h1 class="page-title">Kelola Menu</h1>
      <p class="page-sub">Tambah, edit, dan hapus item menu</p>
    </div>
    <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">+ Tambah Menu Baru</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <!-- Filter -->
  <form method="GET" style="display:flex;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap">
    <select name="category" class="form-control" style="width:180px" onchange="this.form.submit()">
      <option value="">Semua Kategori</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" @selected(request('category')==$cat->id)>{{ $cat->icon }} {{ $cat->name }}</option>
      @endforeach
    </select>
    <input type="text" name="search" class="form-control" placeholder="Cari menu..." value="{{ request('search') }}" style="flex:1;min-width:180px">
    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
    <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary btn-sm">Reset</a>
  </form>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Menu</th><th>Kategori</th><th>Harga</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($menus as $menu)
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:.8rem">
              <span style="font-size:1.8rem">{{ $menu->emoji }}</span>
              <div>
                <div style="font-weight:600">{{ $menu->name }}</div>
                <div style="font-size:.76rem;color:var(--muted)">{{ Str::limit($menu->description, 50) }}</div>
              </div>
            </div>
          </td>
          <td>
            <span style="background:var(--bg3);border-radius:20px;padding:.3rem .8rem;font-size:.78rem">
              {{ $menu->category->icon }} {{ $menu->category->name }}
            </span>
          </td>
          <td style="color:var(--gold2);font-weight:600">{{ $menu->formatted_price }}</td>
          <td>
            <button class="btn btn-sm {{ $menu->is_available ? 'btn-success' : 'btn-danger' }}"
              onclick="toggleAvail({{ $menu->id }}, this)"
              id="avail-{{ $menu->id }}">
              {{ $menu->is_available ? '✓ Tersedia' : '✗ Habis' }}
            </button>
          </td>
          <td>
            <div style="display:flex;gap:.5rem">
              <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-sm btn-info">✏️ Edit</a>
              <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" onsubmit="return confirm('Hapus menu {{ $menu->name }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--muted)">Tidak ada menu</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:1rem">{{ $menus->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
async function toggleAvail(id, btn) {
  btn.disabled = true;
  const res = await apiFetch(`/admin/menus/${id}/toggle`, { method: 'PATCH' });
  if (res.success) {
    btn.className = `btn btn-sm ${res.is_available ? 'btn-success' : 'btn-danger'}`;
    btn.textContent = res.is_available ? '✓ Tersedia' : '✗ Habis';
    toast(res.message);
  }
  btn.disabled = false;
}
</script>
@endpush
