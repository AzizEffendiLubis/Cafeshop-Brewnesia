<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'BrewNest Cafe')</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@stack('styles')
<style>
:root {
  --bg: #0f0d0b; --bg2: #1a1612; --bg3: #231f1a; --card: #2a2420;
  --border: #3d3530; --gold: #d4a853; --gold2: #f0c96e; --cream: #f5edd6;
  --text: #e8ddd0; --muted: #8a7d70; --green: #4caf7d; --red: #e05252;
  --blue: #5b9bd5; --orange: #e07c3a;
}
*{margin:0;padding:0;box-sizing:border-box;}
body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh;}
a{color:inherit;text-decoration:none;}
::-webkit-scrollbar{width:6px;}
::-webkit-scrollbar-track{background:var(--bg);}
::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px;}

/* NAV */
.navbar{position:sticky;top:0;z-index:100;background:rgba(15,13,11,.96);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:.9rem 1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;}
.nav-brand{font-family:'Playfair Display',serif;font-size:1.4rem;color:var(--gold);font-weight:700;margin-right:auto;}
.nav-link{background:none;border:none;color:var(--muted);cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.85rem;padding:.5rem 1rem;border-radius:8px;transition:all .2s;display:inline-flex;align-items:center;gap:.4rem;}
.nav-link:hover,.nav-link.active{background:var(--card);color:var(--text);}
.badge{background:var(--gold);color:#000;border-radius:50%;font-size:.65rem;width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;font-weight:700;}
.badge-red{background:var(--red);color:#fff;}
.table-chip{background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:.3rem .8rem;font-size:.8rem;color:var(--gold);}

/* BUTTONS */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;padding:.7rem 1.4rem;border:none;border-radius:10px;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:600;transition:all .2s;}
.btn-primary{background:var(--gold);color:#000;}.btn-primary:hover{background:var(--gold2);}
.btn-secondary{background:var(--border);color:var(--text);}
.btn-success{background:rgba(76,175,125,.2);color:var(--green);border:1px solid var(--green);}
.btn-danger{background:rgba(224,82,82,.2);color:var(--red);border:1px solid var(--red);}
.btn-info{background:rgba(91,155,213,.2);color:var(--blue);border:1px solid var(--blue);}
.btn-warning{background:rgba(224,124,58,.2);color:var(--orange);border:1px solid var(--orange);}
.btn-sm{padding:.4rem .9rem;font-size:.8rem;border-radius:8px;}

/* FORM */
.form-group{margin-bottom:1.2rem;}
.form-group label{display:block;font-size:.85rem;color:var(--muted);margin-bottom:.4rem;font-weight:500;}
.form-control{width:100%;background:var(--card);border:1px solid var(--border);color:var(--text);padding:.8rem 1rem;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;outline:none;transition:border-color .2s;}
.form-control:focus{border-color:var(--gold);}
.form-control option{background:var(--card);}
textarea.form-control{resize:vertical;}

/* CARDS */
.card{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;}
.card-body{padding:1.5rem;}
.card-header{padding:1rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}

/* STATUS */
.status-badge{padding:.35rem .9rem;border-radius:30px;font-size:.75rem;font-weight:700;display:inline-block;}
.status-pending{background:rgba(224,124,58,.15);color:var(--orange);border:1px solid var(--orange);}
.status-process{background:rgba(91,155,213,.15);color:var(--blue);border:1px solid var(--blue);}
.status-ready{background:rgba(212,168,83,.15);color:var(--gold2);border:1px solid var(--gold2);}
.status-done{background:rgba(76,175,125,.15);color:var(--green);border:1px solid var(--green);}
.status-cancelled{background:rgba(224,82,82,.15);color:var(--red);border:1px solid var(--red);}

/* TABLE */
.table-wrap{overflow-x:auto;border-radius:12px;border:1px solid var(--border);}
table{width:100%;border-collapse:collapse;}
th{background:var(--bg3);padding:.9rem 1.2rem;text-align:left;font-size:.8rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600;}
td{padding:.9rem 1.2rem;border-top:1px solid var(--border);font-size:.88rem;vertical-align:middle;}
tr:hover td{background:rgba(255,255,255,.02);}

/* ALERTS */
.alert{padding:1rem 1.2rem;border-radius:10px;margin-bottom:1.2rem;font-size:.88rem;}
.alert-success{background:rgba(76,175,125,.12);border:1px solid var(--green);color:var(--green);}
.alert-error{background:rgba(224,82,82,.12);border:1px solid var(--red);color:var(--red);}
.alert-info{background:rgba(91,155,213,.12);border:1px solid var(--blue);color:var(--blue);}

/* LAYOUT */
.container{max-width:1300px;margin:0 auto;padding:1.5rem;}
.page-header{margin-bottom:1.5rem;}
.page-title{font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--cream);}
.page-sub{color:var(--muted);font-size:.88rem;margin-top:.3rem;}
.section-title{font-family:'Playfair Display',serif;font-size:1.2rem;color:var(--cream);margin-bottom:1rem;}
.grid-2{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.2rem;}
.grid-4{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;}
.flex{display:flex;align-items:center;gap:.8rem;flex-wrap:wrap;}
.flex-between{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.8rem;}
.text-gold{color:var(--gold2);}
.text-muted{color:var(--muted);}
.text-green{color:var(--green);}
.text-red{color:var(--red);}
.mb-2{margin-bottom:1.5rem;}
.mt-2{margin-top:1.5rem;}
.empty{text-align:center;padding:3rem;color:var(--muted);}
.empty-icon{font-size:3rem;margin-bottom:1rem;}

/* MODAL */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:500;display:none;align-items:center;justify-content:center;padding:1rem;}
.modal-overlay.open{display:flex;}
.modal{background:var(--bg2);border:1px solid var(--border);border-radius:20px;padding:2rem;max-width:520px;width:100%;max-height:90vh;overflow-y:auto;}
.modal h2{font-family:'Playfair Display',serif;font-size:1.4rem;color:var(--cream);margin-bottom:1.5rem;}

/* TOAST */
#toast-container{position:fixed;bottom:2rem;right:2rem;z-index:9000;display:flex;flex-direction:column;gap:.6rem;}
.toast{background:var(--card);border:1px solid var(--border);color:var(--text);padding:.9rem 1.4rem;border-radius:12px;font-size:.88rem;box-shadow:0 8px 30px rgba(0,0,0,.4);animation:slideIn .3s ease;max-width:320px;}
.toast-success{border-color:var(--green);}
.toast-error{border-color:var(--red);}
@keyframes slideIn{from{transform:translateX(60px);opacity:0}to{transform:translateX(0);opacity:1}}
</style>
</head>
<body>
@yield('content')
<div id="toast-container"></div>
<script>
function toast(msg, type='success') {
  const c = document.getElementById('toast-container');
  const t = document.createElement('div');
  t.className = 'toast toast-' + type;
  t.textContent = msg;
  c.appendChild(t);
  setTimeout(() => t.style.opacity = '0', 2500);
  setTimeout(() => t.remove(), 3000);
}
// CSRF token for fetch
window.CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
async function apiFetch(url, opts={}) {
  const res = await fetch(url, {
    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':window.CSRF,'X-Requested-With':'XMLHttpRequest',...(opts.headers||{})},
    ...opts,
  });
  return res.json();
}
</script>
@stack('scripts')
</body>
</html>
