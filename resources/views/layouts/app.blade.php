<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>@yield('title','VERITAS')</title>

<script src="https://kit.fontawesome.com/e16c014aae.js" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>

:root{
    --color-primary-text:#F9F6F2;
    --color-secondary-red:#6D1408;
    --color-primary-gray:#1F2937;
    --sidebar-width:270px;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Inter',sans-serif;
    background:#f4f6f8;
}

.app{
    display:flex;
    min-height:100vh;
}

/* =========================
   SIDEBAR
========================= */

.sidebar{
    width:var(--sidebar-width);
    background:linear-gradient(
        180deg,
        #1F2937,
        #111827
    );
    color:var(--color-primary-text);

    display:flex;
    flex-direction:column;

    min-height:100vh;

    border-right:1px solid rgba(255,255,255,.08);
    box-shadow:8px 0 25px rgba(0,0,0,.2);
}

.logo{
    padding:30px;
    border-bottom:1px solid rgba(255,255,255,.08);
}

.logo h2{
    font-size:30px;
    font-weight:800;
    letter-spacing:2px;
}

.logo p{
    color:#9CA3AF;
    font-size: small;
}

.menu{
    padding:20px;
    border-top:1px solid rgba(255,255,255,.08);
}

.menu-title{
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:2px;
    opacity:.6;
    margin-bottom:15px;
}

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    color:#D5D5D5;
    text-decoration:none;
    padding:14px 16px;
    border-radius:14px;
    margin-bottom:8px;
    transition:.25s;
}

.menu a:hover{
    background:rgba(255,255,255,.06);
    transform:translateX(4px);
}

.menu a.active{
    background:var(--color-secondary-red);
}

.sidebar-footer{
    padding:20px;
    border-top:1px solid rgba(255,255,255,.08);
}

.user-box{
    display:flex;
    align-items:center;
    gap:12px;
}

.avatar{
    width:48px;
    height:48px;
    border-radius:50%;
    background:linear-gradient(
        135deg,
        var(--color-secondary-red),
        #922012
    );

    display:flex;
    align-items:center;
    justify-content:center;

    color:white;
    font-weight:700;
}

/* =========================
   CONTENT
========================= */

.content{
    flex:1;
    display:flex;
    flex-direction:column;
}

/* =========================
   TOPBAR
========================= */

.topbar{
    height:80px;
    background:#1F2937;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 35px;
    position:sticky;
    top:0;
    z-index:50;
}

.topbar-title{
    color:white;
    background:#6D1408;
    padding:10px 22px;
    border-radius:8px;
}

.topbar-date{
    color:white;
    background:#6D1408;
    padding:10px 22px;
    border-radius:8px;
}

.page{
    padding:30px;
    flex:1;
}

/* =========================
   FOOTER
========================= */

.footer{
    margin-top:auto;
    padding:15px;
    text-align:center;
    font-size:12px;
    color:#9CA3AF;
    border-top:1px solid rgba(255,255,255,.08);
    line-height:1.6;
}

.footer a{
    color:#8b1607;
    text-decoration:none;
    font-weight:600;
}

.footer a:hover{
    text-decoration:underline;
}

/* =========================
   BURGER MENU
========================= */

.burger-btn{
    display:none;
    background:none;
    border:none;
    color:white;
    font-size:28px;
    cursor:pointer;
}

.sidebar-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.5);
    z-index:90;
    opacity:0;
    visibility:hidden;
    transition:.3s;
}

.sidebar-overlay.show{
    opacity:1;
    visibility:visible;
}

/* =========================
   TABLET
========================= */

@media(max-width:1024px){

    .page{
        padding:20px;
    }

}

/* =========================
   MOBILE
========================= */

@media(max-width:768px){

    .burger-btn{
        display:block;
    }

    .sidebar{
        position:fixed;
        top:0;
        left:-280px;
        width:270px;
        height:100vh;
        z-index:100;
        transition:.3s;
    }

    .sidebar.show{
        left:0;
    }

    .topbar{
        padding:15px;
        height:auto;
    }

    .topbar-date{
        font-size:12px;
        padding:8px 14px;
    }

    .topbar-title{
        font-size:14px;
        padding:8px 14px;
    }

    .page{
        padding:15px;
    }

    .sidebar-footer{
        display:none;
    }

}

/* =========================
   EXTRA SMALL
========================= */

@media(max-width:480px){

    .logo h2{
        font-size:24px;
    }

    .logo p{
        font-size:12px;
    }

    .page{
        padding:25px;
    }

}

@yield('styles')

</style>
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="app">

<aside class="sidebar" id="sidebar">

    <div class="logo">
        <h2>VERITAS</h2>
        <p>Verifikasi Etika, Rekapitulasi, & Integrasi Tracking Aktivitas Siswa.</p>
    </div>

    <div class="menu">

        <div class="menu-title">Dashboard</div>

        <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>Dashboard
        </a>

        <div class="menu-title">Pelanggaran</div>

        <a href="{{ route('pelanggaran.index') }}">
            <i class="fa-solid fa-chart-pie"></i>Data Pelanggaran
        </a>

        <a href="{{ route('pelanggaran.create') }}">
            <i class="fa-solid fa-pen-to-square"></i>Tambah Pelanggaran
        </a>

        <div class="menu-title">Data Master</div>

        <a href="{{ route('siswa.index') }}">
            <i class="fa-solid fa-user"></i>Data Siswa
        </a>

        <a href="{{ route('siswa.create') }}">
            <i class="fa-solid fa-pen-to-square"></i>Tambah Siswa
        </a>

    </div>

    <div class="sidebar-footer">

        <div class="user-box">

            <div class="avatar">
                G
            </div>

            <div>
                <strong>Guru</strong>
                <div style="font-size:13px;opacity:.7">
                    Administrator
                </div>
            </div>

        </div>

    </div>

    <footer class="footer">
        © {{ date('Y') }} SMK Plus Pelita Nusantara.
        <br>
        All rights reserved.
        <br>
        Developed by
        <a href="https://kicauorgspark.my.id" target="_blank">
            KicawOrgspark
        </a>
    </footer>

</aside>

    <main class="content">

        <div class="topbar">

            <div style="display:flex;align-items:center;gap:15px">

                <button class="burger-btn" id="burgerBtn">
                    ☰
                </button>

                <div class="topbar-title">
                    @yield('page_title','Dashboard')
                </div>

            </div>

            <div class="topbar-date">
                {{ now()->translatedFormat('d F Y') }}
            </div>

        </div>

        <div class="page">
            @yield('content')
        </div>

    </main>

</div>

<script>

const burgerBtn = document.getElementById('burgerBtn');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');

if(burgerBtn){

    burgerBtn.addEventListener('click', function(){

        sidebar.classList.add('show');
        overlay.classList.add('show');

    });

}

overlay.addEventListener('click', function(){

    sidebar.classList.remove('show');
    overlay.classList.remove('show');

});

</script>

</body>
</html>
