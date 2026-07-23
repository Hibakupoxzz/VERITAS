<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - VERITAS</title>

<script src="https://kit.fontawesome.com/e16c014aae.js" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>

:root{
    --color-primary-text:#F9F6F2;
    --color-secondary-red:#6D1408;
    --color-primary-gray:#1F2937;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Inter',sans-serif;
    background:linear-gradient(135deg,#1F2937,#111827);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
}

.login-wrapper{
    width:100%;
    max-width:420px;
}

.login-card{
    background:#fff;
    border-radius:20px;
    box-shadow:0 20px 50px rgba(0,0,0,.35);
    overflow:hidden;
}

.login-header{
    background:linear-gradient(135deg,var(--color-secondary-red),#922012);
    color:var(--color-primary-text);
    padding:35px 30px;
    text-align:center;
}

.login-header h2{
    font-size:28px;
    font-weight:800;
    letter-spacing:2px;
}

.login-header p{
    font-size:13px;
    opacity:.85;
    margin-top:6px;
}

.login-body{
    padding:35px 30px;
}

.form-group{
    margin-bottom:20px;
}

.form-group label{
    display:block;
    font-size:13px;
    font-weight:600;
    color:var(--color-primary-gray);
    margin-bottom:8px;
}

.input-wrap{
    position:relative;
}

.input-wrap i{
    position:absolute;
    left:16px;
    top:50%;
    transform:translateY(-50%);
    color:#9CA3AF;
    font-size:15px;
}

.form-group input{
    width:100%;
    padding:13px 16px 13px 44px;
    border:1.5px solid #E5E7EB;
    border-radius:12px;
    font-size:14px;
    font-family:'Inter',sans-serif;
    transition:.2s;
    outline:none;
}

.form-group input:focus{
    border-color:var(--color-secondary-red);
    box-shadow:0 0 0 3px rgba(109,20,8,.1);
}

.form-group.has-error input{
    border-color:#DC2626;
}

.error-text{
    color:#DC2626;
    font-size:12.5px;
    margin-top:6px;
}

.remember-row{
    display:flex;
    align-items:center;
    gap:8px;
    margin-bottom:24px;
}

.remember-row input{
    width:16px;
    height:16px;
    accent-color:var(--color-secondary-red);
}

.remember-row label{
    font-size:13px;
    color:#4B5563;
}

.btn-login{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:var(--color-secondary-red);
    color:#fff;
    font-size:15px;
    font-weight:700;
    letter-spacing:.5px;
    cursor:pointer;
    transition:.2s;
}

.btn-login:hover{
    background:#5a1006;
    transform:translateY(-1px);
}

.alert{
    background:#FEE2E2;
    color:#B91C1C;
    padding:12px 16px;
    border-radius:10px;
    font-size:13px;
    margin-bottom:20px;
    border:1px solid #FCA5A5;
}

.login-footer{
    text-align:center;
    margin-top:25px;
    font-size:12px;
    color:#9CA3AF;
    line-height:1.6;
}

.login-footer a{
    color:var(--color-secondary-red);
    text-decoration:none;
    font-weight:600;
}

.login-footer a:hover{
    text-decoration:underline;
}

@media(max-width:480px){
    .login-header{
        padding:28px 20px;
    }
    .login-body{
        padding:28px 20px;
    }
}

</style>
</head>
<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="login-header">
            <h2>VERITAS</h2>
            <p>Verifikasi Etika, Rekapitulasi, & Integrasi Tracking Aktivitas Siswa.</p>
        </div>

        <div class="login-body">

            @if (session('status'))
                <div class="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@sekolah.sch.id"
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                </div>

                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Masuk
                </button>

            </form>

        </div>

    </div>

    <div class="login-footer">
        © {{ date('Y') }} SMK Plus Pelita Nusantara.
        <br>
        Developed by
        <a href="https://kicauorgspark.my.id" target="_blank">KicawOrgspark</a>
    </div>

</div>

</body>
</html>
