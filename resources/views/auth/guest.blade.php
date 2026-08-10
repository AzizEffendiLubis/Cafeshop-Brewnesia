<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pilih Meja — BrewNest</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--bg:#0f0d0b;--bg2:#1a1612;--card:#2a2420;--border:#3d3530;--gold:#d4a853;--gold2:#f0c96e;--cream:#f5edd6;--text:#e8ddd0;--muted:#8a7d70;--green:#4caf7d;}
*{margin:0;padding:0;box-sizing:border-box;}
body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:1.5rem;}
.logo{text-align:center;margin-bottom:2.5rem;}
.logo h1{font-family:'Playfair Display',serif;font-size:3rem;color:var(--gold);font-weight:900;}
.logo p{color:var(--muted);letter-spacing:5px;font-size:.75rem;text-transform:uppercase;margin-top:.3rem;}
.card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:2rem;width:100%;max-width:480px;}
.card h2{font-family:'Playfair Display',serif;font-size:1.3rem;color:var(--cream);margin-bottom:.4rem;}
.card p{color:var(--muted);font-size:.85rem;margin-bottom:1.8rem;}
.form-group{margin-bottom:1.2rem;}
.form-group label{display:block;font-size:.82rem;color:var(--muted);margin-bottom:.4rem;font-weight:500;}
.form-control{width:100%;background:var(--bg);border:1px solid var(--border);color:var(--text);padding:.9rem 1rem;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;outline:none;transition:border-color .2s;}
.form-control:focus{border-color:var(--gold);}
.form-control option{background:var(--bg);}
.tables-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.7rem;margin-bottom:1.5rem;}
.table-btn{background:var(--bg);border:1px solid var(--border);color:var(--muted);border-radius:12px;padding:.9rem .5rem;text-align:center;cursor:pointer;transition:all .2s;font-size:.82rem;}
.table-btn:hover,.table-btn.selected{background:rgba(212,168,83,.1);border-color:var(--gold);color:var(--gold);}
.table-btn .num{font-family:'Playfair Display',serif;font-size:1.4rem;color:var(--cream);display:block;margin-bottom:.2rem;}
.table-btn.selected .num{color:var(--gold);}
input[name="table_id"]{display:none;}
.btn{width:100%;padding:1rem;background:var(--gold);border:none;border-radius:12px;font-size:.95rem;font-weight:700;color:#000;cursor:pointer;transition:all .2s;}
.btn:hover{background:var(--gold2);}
.btn:disabled{opacity:.5;cursor:not-allowed;}
.admin-link{display:block;text-align:center;color:var(--muted);font-size:.82rem;margin-top:1.5rem;}
.admin-link a{color:var(--gold);text-decoration:underline;}
.alert{background:rgba(76,175,125,.1);border:1px solid var(--green);color:var(--green);padding:.8rem 1rem;border-radius:8px;font-size:.83rem;margin-bottom:1rem;}
</style>
</head>
<body>
<div class="logo">
  <h1>Brewnesia</h1>
  <p>Coffee & Resto</p>
</div>

<div class="card">
  <h2>🪑 Selamat Datang!</h2>
  <p>Pilih nomor meja Anda untuk mulai memesan</p>

  @if(session('success'))
    <div class="alert">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('guest.login') }}" id="tableForm">
    @csrf
    <input type="hidden" name="table_id" id="tableIdInput" value="">

    <div class="tables-grid">
      @foreach($tables as $table)
      <div class="table-btn" onclick="selectTable({{ $table->id }}, this)">
        <span class="num">{{ $table->number }}</span>
        Meja
      </div>
      @endforeach
    </div>

    <div class="form-group">
      <label>Nama Anda (Opsional)</label>
      <input type="text" name="customer_name" class="form-control" placeholder="Contoh: Budi" maxlength="50">
    </div>

    @error('table_id')
      <p style="color:var(--red);font-size:.82rem;margin-bottom:.8rem">{{ $message }}</p>
    @enderror

    <button type="submit" class="btn" id="startBtn" disabled>Pilih Meja Terlebih Dahulu</button>
  </form>

  <p class="admin-link">Admin? <a href="{{ route('login') }}">Login di sini</a></p>
</div>

<script>
function selectTable(id, el) {
  document.querySelectorAll('.table-btn').forEach(b => b.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('tableIdInput').value = id;
  const num = el.querySelector('.num').textContent;
  const btn = document.getElementById('startBtn');
  btn.disabled = false;
  btn.textContent = `☕ Mulai Pesan di Meja ${num} →`;
}
</script>
</body>
</html>
