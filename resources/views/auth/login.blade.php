<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - VERITAS</title>

@include('partials.favicon')

<script src="https://kit.fontawesome.com/e16c014aae.js" crossorigin="anonymous"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
    --primary:#6D1408;
    --primary-dark:#4f0e05;
    --primary-light:#922012;
    --text-light:#F9F6F2;
    --gray-900:#111827;
    --gray-800:#1F2937;
    --gray-600:#4B5563;
    --gray-400:#9CA3AF;
    --gray-200:#E5E7EB;
    --gray-50:#F9FAFB;
    --danger:#DC2626;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html,body{
    max-width:100%;
}

body{
    font-family:'Inter',system-ui,sans-serif;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:24px;
    overflow-x:hidden;

    background:
        radial-gradient(circle at 15% 20%, rgba(146,32,18,.35), transparent 45%),
        radial-gradient(circle at 85% 85%, rgba(109,20,8,.30), transparent 45%),
        linear-gradient(135deg, var(--gray-800), var(--gray-900));
}

a{ text-decoration:none; }


/* =====================================================
   WRAPPER & CARD
===================================================== */

.login-wrapper{
    width:100%;
    max-width:880px;
}

.login-card{
    display:grid;
    grid-template-columns:1fr 1fr;

    background:#fff;
    border-radius:24px;
    overflow:hidden;

    box-shadow:0 30px 70px rgba(0,0,0,.45);
    animation:rise .5s ease both;
}

@keyframes rise{
    from{ opacity:0; transform:translateY(16px); }
    to  { opacity:1; transform:none; }
}


/* =====================================================
   PANEL KIRI (BRAND)
===================================================== */

.brand-panel{
    position:relative;
    display:flex;
    flex-direction:column;
    justify-content:center;
    gap:28px;

    padding:48px 40px;

    background:linear-gradient(150deg, var(--primary-light), var(--primary) 55%, var(--primary-dark));
    color:var(--text-light);
    overflow:hidden;
}

/* hiasan lingkaran */
.brand-panel::before,
.brand-panel::after{
    content:"";
    position:absolute;
    border-radius:50%;
    background:rgba(255,255,255,.06);
    pointer-events:none;
}

.brand-panel::before{
    width:260px;
    height:260px;
    top:-90px;
    right:-90px;
}

.brand-panel::after{
    width:200px;
    height:200px;
    bottom:-70px;
    left:-60px;
}

.brand-logo{
    position:relative;
    z-index:1;

    display:flex;
    align-items:center;
    justify-content:center;

    width:60px;
    height:60px;

    border-radius:16px;
    background:rgba(255,255,255,.14);
    border:1px solid rgba(255,255,255,.22);

    font-size:26px;
}

.brand-text{
    position:relative;
    z-index:1;
}

.brand-text h1{
    font-size:32px;
    font-weight:800;
    letter-spacing:3px;
}

.brand-text p{
    margin-top:8px;
    font-size:13px;
    line-height:1.65;
    opacity:.85;
}

.brand-points{
    position:relative;
    z-index:1;

    list-style:none;
    display:flex;
    flex-direction:column;
    gap:12px;
}

.brand-points li{
    display:flex;
    align-items:center;
    gap:12px;

    font-size:13px;
    font-weight:500;
}

.brand-points i{
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;

    width:30px;
    height:30px;

    border-radius:9px;
    background:rgba(255,255,255,.14);

    font-size:12px;
}


/* =====================================================
   PANEL KANAN (FORM)
===================================================== */

.form-panel{
    display:flex;
    flex-direction:column;
    justify-content:center;

    padding:48px 42px;
}

.form-head{
    margin-bottom:26px;
}

.form-head h2{
    font-size:24px;
    font-weight:800;
    color:var(--gray-900);
    letter-spacing:-.3px;
}

.form-head p{
    margin-top:5px;
    font-size:13.5px;
    color:var(--gray-600);
}

/* alert */
.alert{
    display:flex;
    align-items:flex-start;
    gap:10px;

    margin-bottom:20px;
    padding:12px 14px;

    border-radius:12px;
    font-size:13px;
    line-height:1.5;
}

.alert i{
    margin-top:2px;
}

.alert-danger{
    background:#FEF2F2;
    color:#B91C1C;
    border:1px solid #FECACA;
}

.alert-info{
    background:#EFF6FF;
    color:#1D4ED8;
    border:1px solid #BFDBFE;
}

/* field */
.form-group{
    margin-bottom:18px;
}

.form-group label{
    display:block;
    margin-bottom:8px;

    font-size:13px;
    font-weight:600;
    color:var(--gray-800);
}

.input-wrap{
    position:relative;
}

.input-wrap .icon-left{
    position:absolute;
    left:16px;
    top:50%;
    transform:translateY(-50%);

    color:var(--gray-400);
    font-size:15px;

    pointer-events:none;
    transition:.2s;
}

.form-group input[type="text"],
.form-group input[type="password"]{
    width:100%;
    height:50px;
    padding:0 16px 0 46px;

    background:var(--gray-50);
    border:1.5px solid var(--gray-200);
    border-radius:12px;

    font-size:14px;
    font-family:inherit;
    color:var(--gray-900);

    outline:none;
    transition:.2s;
}

.form-group input::placeholder{
    color:var(--gray-400);
}

.form-group input:focus{
    background:#fff;
    border-color:var(--primary);
    box-shadow:0 0 0 4px rgba(109,20,8,.10);
}

.input-wrap:focus-within .icon-left{
    color:var(--primary);
}

.form-group.has-error input{
    border-color:var(--danger);
    background:#FFFBFB;
}

.form-group.has-error input:focus{
    box-shadow:0 0 0 4px rgba(220,38,38,.12);
}

/* tombol lihat password */
.toggle-pass{
    position:absolute;
    right:6px;
    top:50%;
    transform:translateY(-50%);

    display:flex;
    align-items:center;
    justify-content:center;

    width:38px;
    height:38px;

    border:none;
    border-radius:9px;
    background:transparent;
    color:var(--gray-400);

    font-size:15px;
    cursor:pointer;
    transition:.2s;
}

.toggle-pass:hover{
    color:var(--primary);
    background:rgba(109,20,8,.07);
}

.form-group input#password{
    padding-right:50px;
}

.error-text{
    display:flex;
    align-items:center;
    gap:6px;

    margin-top:7px;

    font-size:12.5px;
    color:var(--danger);
}

.caps-warning{
    display:none;
    align-items:center;
    gap:6px;

    margin-top:7px;

    font-size:12px;
    color:#B45309;
}

.caps-warning.show{
    display:flex;
}

/* remember */
.remember-row{
    display:flex;
    align-items:center;
    gap:9px;
    margin-bottom:22px;
}

.remember-row input{
    width:17px;
    height:17px;
    accent-color:var(--primary);
    cursor:pointer;
}

.remember-row label{
    font-size:13px;
    color:var(--gray-600);
    cursor:pointer;
    user-select:none;
}

/* tombol login */
.btn-login{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;

    width:100%;
    height:50px;

    border:none;
    border-radius:12px;

    background:linear-gradient(135deg, var(--primary-light), var(--primary));
    color:#fff;

    font-size:15px;
    font-weight:700;
    font-family:inherit;
    letter-spacing:.3px;

    cursor:pointer;
    box-shadow:0 8px 20px rgba(109,20,8,.30);
    transition:.2s;
}

.btn-login:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 26px rgba(109,20,8,.38);
    filter:brightness(1.06);
}

.btn-login:active{
    transform:translateY(0);
}

.btn-login:disabled{
    opacity:.75;
    cursor:wait;
    transform:none;
}

.form-note{
    margin-top:22px;
    text-align:center;
    font-size:12px;
    color:var(--gray-400);
    line-height:1.6;
}

.form-note i{
    margin-right:4px;
}


/* =====================================================
   FOOTER
===================================================== */

.login-footer{
    margin-top:22px;
    text-align:center;

    font-size:12px;
    line-height:1.7;
    color:rgba(255,255,255,.55);
}

.login-footer a{
    color:#fff;
    font-weight:600;
}

.login-footer a:hover{
    text-decoration:underline;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media(max-width:820px){

    .login-wrapper{
        max-width:440px;
    }

    .login-card{
        grid-template-columns:1fr;
    }

    /* panel brand jadi header ringkas */
    .brand-panel{
        flex-direction:row;
        align-items:center;
        gap:16px;
        padding:26px 28px;
    }

    .brand-logo{
        width:50px;
        height:50px;
        font-size:22px;
        flex-shrink:0;
    }

    .brand-text h1{
        font-size:24px;
        letter-spacing:2px;
    }

    .brand-text p{
        margin-top:4px;
        font-size:11.5px;
        line-height:1.5;
    }

    .brand-points{
        display:none;
    }

    .form-panel{
        padding:30px 28px 32px;
    }

}

@media(max-width:480px){

    body{
        padding:14px 12px;
        align-items:flex-start;
    }

    .login-card{
        border-radius:18px;
    }

    .brand-panel{
        padding:22px 20px;
    }

    .form-panel{
        padding:26px 20px 28px;
    }

    .form-head h2{
        font-size:21px;
    }

    /* cegah auto-zoom iOS saat input difokuskan */
    .form-group input[type="text"],
    .form-group input[type="password"]{
        font-size:16px;
    }

}

@media(max-width:360px){

    .brand-text p{
        display:none;
    }

}

@media(prefers-reduced-motion:reduce){

    .login-card{
        animation:none;
    }

    *{
        transition:none !important;
    }

}
</style>
</head>
<body>

<div class="login-wrapper">

    <div class="login-card">

        {{-- ================= PANEL BRAND ================= --}}
        <div class="brand-panel">

            <div class="brand-logo">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <div class="brand-text">
                <h1>VERITAS</h1>
                <p>Verifikasi Etika, Rekapitulasi, &amp; Integrasi Tracking Aktivitas Siswa.</p>
            </div>

            <ul class="brand-points">
                <li>
                    <i class="fa-solid fa-clipboard-list"></i>
                    Pencatatan pelanggaran terpusat
                </li>
                <li>
                    <i class="fa-solid fa-trophy"></i>
                    Poin prestasi &amp; leaderboard
                </li>
                <li>
                    <i class="fa-solid fa-chart-line"></i>
                    Rekap harian &amp; mingguan
                </li>
            </ul>

        </div>


        {{-- ================= PANEL FORM ================= --}}
        <div class="form-panel">

            <div class="form-head">
                <h2>Selamat Datang</h2>
                <p>Masuk menggunakan NIG atau email dan password Anda.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-info" role="status">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any() && !$errors->has('nig'))
                <div class="alert alert-danger" role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group {{ $errors->has('nig') ? 'has-error' : '' }}">
                    <label for="nig">NIG / Email</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-id-card icon-left"></i>
                        <input
                            type="text"
                            id="nig"
                            name="nig"
                            value="{{ old('nig') }}"
                            placeholder="NIG atau email"
                            autocomplete="username"
                            maxlength="255"
                            required
                            autofocus
                        >
                    </div>
                    @error('nig')
                        <div class="error-text">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock icon-left"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button"
                                class="toggle-pass"
                                id="togglePass"
                                aria-label="Tampilkan password"
                                aria-pressed="false">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    <div class="caps-warning" id="capsWarning">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Caps Lock aktif</span>
                    </div>
                </div>

                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk</span>
                </button>

            </form>

            <div class="form-note">
                <i class="fa-solid fa-circle-question"></i>
                Lupa password? Hubungi Devaccto RPL.
            </div>

        </div>

    </div>


    <div class="login-footer">
        © {{ date('Y') }} SMK Plus Pelita Nusantara.
        <br>
        Developed by Devaccto RPL &
        <a href="https://kicauorgspark.my.id" target="_blank" rel="noopener">KicawOrgspark</a>
    </div>

</div>


<script>
(function () {
    var form   = document.getElementById('loginForm');
    var pass   = document.getElementById('password');
    var toggle = document.getElementById('togglePass');
    var caps   = document.getElementById('capsWarning');
    var btn    = document.getElementById('btnLogin');

    // Lihat / sembunyikan password
    toggle.addEventListener('click', function () {
        var show = pass.type === 'password';
        pass.type = show ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
        toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        toggle.innerHTML = show
            ? '<i class="fa-regular fa-eye-slash"></i>'
            : '<i class="fa-regular fa-eye"></i>';
        pass.focus();
    });

    // Peringatan Caps Lock
    function checkCaps(e) {
        if (typeof e.getModifierState !== 'function') return;
        caps.classList.toggle('show', e.getModifierState('CapsLock'));
    }
    pass.addEventListener('keydown', checkCaps);
    pass.addEventListener('keyup', checkCaps);
    pass.addEventListener('blur', function () { caps.classList.remove('show'); });

    // Cegah klik ganda + tampilkan loading
    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i><span>Memproses...</span>';
    });

    // Tombol Back di browser: kembalikan tombol ke keadaan normal
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i><span>Masuk</span>';
        }
    });
})();
</script>

</body>
</html>
