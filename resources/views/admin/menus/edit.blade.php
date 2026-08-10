@extends('layouts.app')
@section('title', 'Edit Menu — Brewnesia')

@section('content')
<nav class="navbar">
  <div class="nav-brand">Brewnesia ✦</div>
  <a href="{{ route('admin.menus.index') }}" class="nav-link">← Kembali ke Menu</a>
</nav>

<div class="container" style="max-width:640px">
  <div class="page-header">
    <h1 class="page-title">Edit Menu</h1>
    <p class="page-sub">Perbarui detail menu: {{ $menu->name }}</p>
  </div>

  @if($errors->any())
    <div class="alert alert-error">
      @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.menus.update', $menu) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group" style="grid-column:1/-1">
            <label>Nama Menu *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $menu->name) }}" required>
          </div>
          <div class="form-group">
            <label>Kategori *</label>
            <select name="category_id" class="form-control" required>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $menu->category_id) == $cat->id)>{{ $cat->icon }} {{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Harga (Rp) *</label>
            <input type="number" name="price" class="form-control" value="{{ old('price', $menu->price) }}" min="0" required>
          </div>
          <div class="form-group">
            <label>Emoji / Ikon</label>
            <input type="text" name="emoji" class="form-control" value="{{ old('emoji', $menu->emoji) }}" maxlength="10">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="is_available" class="form-control">
              <option value="1" @selected(old('is_available', $menu->is_available ? '1' : '0') == '1')>✓ Tersedia</option>
              <option value="0" @selected(old('is_available', $menu->is_available ? '1' : '0') == '0')>✗ Tidak Tersedia</option>
            </select>
          </div>
          <div class="form-group" style="grid-column:1/-1">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="2">{{ old('description', $menu->description) }}</textarea>
          </div>
          <div class="form-group" style="grid-column:1/-1">
            <label>Foto Menu Baru (biarkan kosong jika tidak ingin mengubah)</label>
            @if($menu->image)
              <div style="margin-bottom:.8rem">
                <img src="{{ asset('storage/' . $menu->image) }}" style="height:80px;border-radius:8px;object-fit:cover" alt="{{ $menu->name }}">
                <span style="font-size:.78rem;color:var(--muted);margin-left:.5rem">Foto saat ini</span>
              </div>
            @endif
            <input type="file" name="image" class="form-control" accept="image/*">
          </div>
        </div>
        <div style="display:flex;gap:.8rem;margin-top:.5rem">
          <button type="submit" class="btn btn-primary" style="flex:2">Simpan Perubahan</button>
          <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary" style="flex:1;text-align:center">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
