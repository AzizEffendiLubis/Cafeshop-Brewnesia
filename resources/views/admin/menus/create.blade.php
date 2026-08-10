@extends('layouts.app')
@section('title', 'Tambah Menu — Brewnesia')

@section('content')
<nav class="navbar">
  <div class="nav-brand">Brewnesia ✦</div>
  <a href="{{ route('admin.menus.index') }}" class="nav-link">← Kembali ke Menu</a>
</nav>

<div class="container" style="max-width:640px">
  <div class="page-header">
    <h1 class="page-title">Tambah Menu Baru</h1>
    <p class="page-sub">Isi detail menu yang ingin ditambahkan</p>
  </div>

  @if($errors->any())
    <div class="alert alert-error">
      @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.menus.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group" style="grid-column:1/-1">
            <label>Nama Menu *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Nasi Goreng Spesial" required>
          </div>
          <div class="form-group">
            <label>Kategori *</label>
            <select name="category_id" class="form-control" required>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->icon }} {{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Harga (Rp) *</label>
            <input type="number" name="price" class="form-control" value="{{ old('price') }}" placeholder="25000" min="0" required>
          </div>
          <div class="form-group">
            <label>Emoji / Ikon</label>
            <input type="text" name="emoji" class="form-control" value="{{ old('emoji', '🍽️') }}" placeholder="🍕" maxlength="10">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="is_available" class="form-control">
              <option value="1" @selected(old('is_available', '1') == '1')>✓ Tersedia</option>
              <option value="0" @selected(old('is_available') == '0')>✗ Tidak Tersedia</option>
            </select>
          </div>
          <div class="form-group" style="grid-column:1/-1">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi singkat menu...">{{ old('description') }}</textarea>
          </div>
          <div class="form-group" style="grid-column:1/-1">
            <label>Foto Menu (opsional, max 2MB)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
          </div>
        </div>
        <div style="display:flex;gap:.8rem;margin-top:.5rem">
          <button type="submit" class="btn btn-primary" style="flex:2">Simpan Menu</button>
          <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary" style="flex:1;text-align:center">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
