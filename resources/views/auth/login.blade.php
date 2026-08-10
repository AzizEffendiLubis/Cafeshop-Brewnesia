<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin — BrewNest</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--bg:#0f0d0b;--bg2:#1a1612;--card:#2a2420;--border:#3d3530;--gold:#d4a853;--gold2:#f0c96e;--cream:#f5edd6;--text:#e8ddd0;--muted:#8a7d70;--red:#e05252;}
*{margin:0;padding:0;box-sizing:border-box;}
body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;}
.wrap{width:100%;max-width:420px;padding:1.5rem;}
.logo{text-align:center;margin-bottom:2.5rem;}
.logo h1{font-family:'Playfair Display',serif;font-size:3rem;color:var(--gold);font-weight:900;}
.logo p{color:var(--muted);letter-spacing:5px;font-size:.75rem;text-transform:uppercase;margin-top:.3rem;}
.card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:2rem;}
.card h2{font-family:'Playfair Display',serif;font-size:1.4rem;color:var(--cream);margin-bottom:.4rem;}
.card p{color:var(--muted);font-size:.85rem;margin-bottom:1.8rem;}
.form-group{margin-bottom:1.2rem;}
.form-group label{display:block;font-size:.82rem;color:var(--muted);margin-bottom:.4rem;font-weight:500;}
.form-group input{width:100%;background:var(--bg);border:1px solid var(--border);color:var(--text);padding:.9rem 1rem;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;outline:none;transition:border-color .2s;}
.form-group input:focus{border-color:var(--gold);}
.btn{width:100%;padding:1rem;background:var(--gold);border:none;border-radius:12px;font-size:.95rem;font-weight:700;color:#000;cursor:pointer;margin-top:.5rem;transition:all .2s;}
.btn:hover{background:var(--gold2);}
.error{background:rgba(224,82,82,.1);border:1px solid var(--red);color:var(--red);padding:.8rem 1rem;border-radius:8px;font-size:.83rem;margin-bottom:1rem;}
.divider{text-align:center;color:var(--muted);font-size:.82rem;margin:1.5rem 0;position:relative;}
.divider::before,.divider::after{content:'';position:absolute;top:50%;width:40%;height:1px;background:var(--border);}
.divider::before{left:0;} .divider::after{right:0;}
.guest-link{display:block;text-align:center;background:var(--bg);border:1px solid var(--border);color:var(--muted);padding:.9rem;border-radius:12px;font-size:.88rem;transition:all .2s;}
.guest-link:hover{border-color:var(--gold);color:var(--text);}
</style>
</head>
<body>
<div class="wrap">
  <div class="logo">
    <h1>Brewnesia</h1>
    <p>Coffee & Resto</p>
  </div>
  <div class="card">
    <h2>Login Admin</h2>
    <p>Masuk untuk mengelola cafe</p>
    @if($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif
    @if(session('info'))
      <div class="error" style="border-color:var(--gold);color:var(--gold);background:rgba(212,168,83,.1)">{{ session('info') }}</div>
    @endif
    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@brewnest.com" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn">Masuk sebagai Admin →</button>
    </form>
    <div class="divider">atau</div>
    <a href="{{ route('guest.select') }}" class="guest-link">☕ Saya Pelanggan — Pilih Meja</a>
  </div>
</div>
</body>
</html>
