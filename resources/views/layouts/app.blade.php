<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>
        @yield('title', 'VERITAS - SMK PLUS PELITA NUSANTARA')
    </title>

    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/e16c014aae.js"
            crossorigin="anonymous"></script>

    {{-- Google Font --}}
    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <style>

        /* =========================================================
           RESET
        ========================================================= */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #F9F6F2;
            color: #1F2937;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        img {
            max-width: 100%;
        }


        /* =========================================================
           VARIABLES
        ========================================================= */

        :root {
            --primary: #6D1408;
            --primary-dark: #4E0D06;
            --primary-light: #8A1C0D;

            --dark: #1F2937;
            --dark-2: #374151;

            --background: #F9F6F2;
            --white: #FFFFFF;

            --border: #E5E7EB;
            --muted: #6B7280;

            --sidebar-width: 270px;

            --shadow-sm:
                0 2px 8px rgba(0, 0, 0, 0.05);

            --shadow:
                0 8px 24px rgba(0, 0, 0, 0.07);
        }


        /* =========================================================
           APP WRAPPER
        ========================================================= */

        .app {
            min-height: 100vh;
        }


        /* =========================================================
           SIDEBAR
        ========================================================= */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;

            width: var(--sidebar-width);
            height: 100vh;

            background: var(--dark);

            color: white;

            z-index: 1000;

            display: flex;
            flex-direction: column;

            transition:
                transform 0.3s ease,
                width 0.3s ease;

            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Scrollbar sidebar */

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
        }


        /* =========================================================
           SIDEBAR BRAND
        ========================================================= */

        .sidebar-brand {
            height: 78px;

            display: flex;
            align-items: center;

            padding: 0 24px;

            border-bottom:
                1px solid rgba(255,255,255,0.08);

            flex-shrink: 0;
        }

        .brand-icon {
            width: 42px;
            height: 42px;

            background: var(--primary);

            border-radius: 11px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-right: 12px;

            box-shadow:
                0 5px 15px rgba(109,20,8,0.3);
        }

        .brand-icon i {
            font-size: 19px;
            color: white;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .brand-subtitle {
            font-size: 10px;
            color: #9CA3AF;
            margin-top: 2px;
            letter-spacing: 0.2px;
        }


        /* =========================================================
           SIDEBAR MENU
        ========================================================= */

        .menu {
            padding: 18px 14px 20px;
            flex: 1;
        }

        .menu-title {
            font-size: 10px;
            font-weight: 700;

            color: #6B7280;

            text-transform: uppercase;

            letter-spacing: 1px;

            padding:
                16px 12px 8px;
        }

        .menu-title:first-child {
            padding-top: 4px;
        }

        .menu a {
            position: relative;

            display: flex;
            align-items: center;

            gap: 12px;

            width: 100%;

            padding: 11px 13px;

            margin-bottom: 4px;

            border-radius: 9px;

            color: #D1D5DB;

            font-size: 13px;
            font-weight: 500;

            transition:
                background 0.2s ease,
                color 0.2s ease,
                transform 0.2s ease;
        }

        .menu a i {
            width: 19px;

            text-align: center;

            font-size: 14px;

            color: #9CA3AF;

            transition: color 0.2s ease;
        }

        .menu a:hover {
            background:
                rgba(255,255,255,0.06);

            color: white;

            transform: translateX(2px);
        }

        .menu a:hover i {
            color: white;
        }

        .menu a.active {
            background: var(--primary);

            color: white;

            box-shadow:
                0 5px 15px rgba(109,20,8,0.22);
        }

        .menu a.active i {
            color: white;
        }

        .menu a.active::before {
            content: "";

            position: absolute;

            left: -14px;

            top: 50%;

            transform: translateY(-50%);

            width: 3px;

            height: 24px;

            background: white;

            border-radius: 0 5px 5px 0;
        }


        /* =========================================================
           USER AREA
        ========================================================= */

        .sidebar-user {
            padding: 14px;

            border-top:
                1px solid rgba(255,255,255,0.08);

            flex-shrink: 0;
        }

        .user-box {
            display: flex;
            align-items: center;

            padding: 11px;

            border-radius: 10px;

            background:
                rgba(255,255,255,0.04);
        }

        .user-avatar {
            width: 36px;
            height: 36px;

            border-radius: 50%;

            background: var(--primary);

            display: flex;
            align-items: center;
            justify-content: center;

            margin-right: 10px;

            flex-shrink: 0;
        }

        .user-avatar i {
            font-size: 14px;
        }

        .user-info {
            min-width: 0;
            flex: 1;
        }

        .user-name {
            font-size: 12px;
            font-weight: 700;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 10px;
            color: #9CA3AF;

            margin-top: 2px;
        }

        .logout-btn {
            border: none;
            background: transparent;

            color: #9CA3AF;

            cursor: pointer;

            width: 30px;
            height: 30px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 7px;

            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background:
                rgba(255,255,255,0.08);

            color: #F87171;
        }


        /* =========================================================
           SIDEBAR FOOTER
        ========================================================= */

        .sidebar-footer {
            padding: 0 18px 15px;

            text-align: center;

            font-size: 9px;

            color: #6B7280;

            flex-shrink: 0;
        }


        /* =========================================================
           MAIN
        ========================================================= */

        .main {
            margin-left: var(--sidebar-width);

            min-height: 100vh;

            transition:
                margin-left 0.3s ease;
        }


        /* =========================================================
           TOPBAR
        ========================================================= */

        .topbar {
            height: 78px;

            background: white;

            border-bottom:
                1px solid var(--border);

            display: flex;
            align-items: center;

            justify-content: space-between;

            padding:
                0 30px;

            position: sticky;

            top: 0;

            z-index: 900;
        }


        /* =========================================================
           TOPBAR LEFT
        ========================================================= */

        .topbar-left {
            display: flex;
            align-items: center;

            gap: 14px;

            min-width: 0;
        }

        .mobile-menu-btn {
            display: none;

            width: 40px;
            height: 40px;

            border: none;

            background: #F3F4F6;

            color: var(--dark);

            border-radius: 9px;

            cursor: pointer;

            align-items: center;
            justify-content: center;
        }

        .mobile-menu-btn i {
            font-size: 16px;
        }

        .page-heading {
            min-width: 0;
        }

        .page-heading h1 {
            font-size: 18px;
            font-weight: 700;

            color: var(--dark);

            white-space: nowrap;
        }

        .page-heading p {
            margin-top: 3px;

            font-size: 11px;

            color: var(--muted);

            white-space: nowrap;
        }


        /* =========================================================
           TOPBAR RIGHT
        ========================================================= */

        .topbar-right {
            display: flex;
            align-items: center;

            gap: 12px;
        }

        .topbar-date {
            display: flex;
            align-items: center;

            gap: 7px;

            font-size: 11px;

            color: var(--muted);

            padding:
                8px 12px;

            background: #F9FAFB;

            border:
                1px solid var(--border);

            border-radius: 8px;
        }

        .topbar-date i {
            color: var(--primary);
        }


        /* =========================================================
           PAGE CONTENT
        ========================================================= */

        .page {
            padding: 28px 30px 40px;

            width: 100%;

            max-width: 1600px;

            margin: 0 auto;
        }


        /* =========================================================
           OVERLAY MOBILE
        ========================================================= */

        .sidebar-overlay {
            display: none;

            position: fixed;

            inset: 0;

            background:
                rgba(0,0,0,0.45);

            z-index: 950;

            opacity: 0;

            transition: opacity 0.3s ease;
        }


        /* =========================================================
           COMMON CARD
        ========================================================= */

        .card {
            background: white;

            border:
                1px solid var(--border);

            border-radius: 14px;

            box-shadow: var(--shadow-sm);
        }


        /* =========================================================
           BUTTON
        ========================================================= */

        .btn {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 8px;

            border: none;

            border-radius: 8px;

            padding:
                10px 15px;

            font-size: 12px;

            font-weight: 600;

            cursor: pointer;

            transition:
                all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);

            transform: translateY(-1px);

            box-shadow:
                0 5px 12px rgba(109,20,8,0.2);
        }

        .btn-secondary {
            background: #F3F4F6;
            color: var(--dark);
        }

        .btn-secondary:hover {
            background: #E5E7EB;
        }

        .btn-danger {
            background: #FEF2F2;
            color: #B91C1C;
        }

        .btn-danger:hover {
            background: #FEE2E2;
        }


        /* =========================================================
           FORM
        ========================================================= */

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;

            margin-bottom: 7px;

            font-size: 12px;

            font-weight: 600;

            color: var(--dark);
        }

        .form-control {
            width: 100%;

            padding:
                10px 12px;

            border:
                1px solid var(--border);

            border-radius: 8px;

            background: white;

            color: var(--dark);

            font-size: 12px;

            outline: none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);

            box-shadow:
                0 0 0 3px rgba(109,20,8,0.08);
        }

        textarea.form-control {
            min-height: 100px;

            resize: vertical;
        }

        .form-help {
            margin-top: 5px;

            font-size: 10px;

            color: var(--muted);
        }


        /* =========================================================
           ALERT
        ========================================================= */

        .alert {
            padding: 12px 14px;

            border-radius: 9px;

            margin-bottom: 18px;

            font-size: 12px;
        }

        .alert-success {
            background: #ECFDF5;
            color: #047857;

            border:
                1px solid #A7F3D0;
        }

        .alert-danger {
            background: #FEF2F2;
            color: #B91C1C;

            border:
                1px solid #FECACA;
        }

        .alert-warning {
            background: #FFFBEB;
            color: #B45309;

            border:
                1px solid #FDE68A;
        }

        .alert-info {
            background: #EFF6FF;
            color: #1D4ED8;

            border:
                1px solid #BFDBFE;
        }


        /* =========================================================
           TABLE
        ========================================================= */

        .table-wrapper {
            width: 100%;

            overflow-x: auto;

            -webkit-overflow-scrolling: touch;
        }

        .table {
            width: 100%;

            border-collapse: collapse;

            min-width: 650px;
        }

        .table th {
            text-align: left;

            padding:
                12px 14px;

            background: #F9FAFB;

            border-bottom:
                1px solid var(--border);

            font-size: 10px;

            text-transform: uppercase;

            letter-spacing: 0.5px;

            color: var(--muted);

            font-weight: 700;
        }

        .table td {
            padding:
                13px 14px;

            border-bottom:
                1px solid #F1F1F1;

            font-size: 12px;

            color: var(--dark);
        }

        .table tbody tr:hover {
            background: #FAFAFA;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }


        /* =========================================================
           BADGE
        ========================================================= */

        .badge {
            display: inline-flex;

            align-items: center;

            padding:
                4px 8px;

            border-radius: 999px;

            font-size: 10px;

            font-weight: 600;
        }

        .badge-success {
            background: #ECFDF5;
            color: #047857;
        }

        .badge-danger {
            background: #FEF2F2;
            color: #B91C1C;
        }

        .badge-warning {
            background: #FFFBEB;
            color: #B45309;
        }

        .badge-info {
            background: #EFF6FF;
            color: #1D4ED8;
        }

        .badge-gray {
            background: #F3F4F6;
            color: #4B5563;
        }


        /* =========================================================
           PAGINATION
        ========================================================= */

        .pagination-wrapper {
            margin-top: 20px;

            display: flex;

            justify-content: center;
        }


        /* =========================================================
           EMPTY STATE
        ========================================================= */

        .empty-state {
            padding: 45px 20px;

            text-align: center;

            color: var(--muted);
        }

        .empty-state-icon {
            width: 55px;
            height: 55px;

            margin:
                0 auto 12px;

            border-radius: 50%;

            background: #F3F4F6;

            display: flex;

            align-items: center;
            justify-content: center;
        }

        .empty-state-icon i {
            font-size: 20px;

            color: #9CA3AF;
        }

        .empty-state h3 {
            font-size: 14px;

            color: var(--dark);

            margin-bottom: 5px;
        }

        .empty-state p {
            font-size: 11px;
        }


        /* =========================================================
           RESPONSIVE TABLET
        ========================================================= */

        @media (max-width: 1100px) {

            :root {
                --sidebar-width: 235px;
            }

            .sidebar {
                width: 235px;
            }

            .main {
                margin-left: 235px;
            }

            .topbar {
                padding:
                    0 22px;
            }

            .page {
                padding:
                    24px 22px 35px;
            }

        }


        /* =========================================================
           RESPONSIVE MOBILE
        ========================================================= */

        @media (max-width: 768px) {

            .sidebar {
                width: 270px;

                transform:
                    translateX(-100%);

                box-shadow:
                    8px 0 30px rgba(0,0,0,0.15);
            }

            .sidebar.show {
                transform:
                    translateX(0);
            }

            .main {
                margin-left: 0;

                width: 100%;
            }

            .topbar {
                height: 68px;

                padding:
                    0 15px;
            }

            .mobile-menu-btn {
                display: flex;
            }

            .page-heading h1 {
                font-size: 15px;
            }

            .page-heading p {
                font-size: 9px;
            }

            .topbar-date {
                display: none;
            }

            .page {
                padding:
                    20px 15px 30px;
            }

            .sidebar-overlay {
                display: block;

                pointer-events: none;
            }

            .sidebar-overlay.show {
                opacity: 1;

                pointer-events: auto;
            }

        }


        /* =========================================================
           SMALL MOBILE
        ========================================================= */

        @media (max-width: 480px) {

            .topbar {
                height: 62px;
            }

            .mobile-menu-btn {
                width: 36px;
                height: 36px;
            }

            .page-heading h1 {
                font-size: 14px;
            }

            .page-heading p {
                display: none;
            }

            .page {
                padding:
                    16px 12px 25px;
            }

            .card {
                border-radius: 11px;
            }

            .btn {
                padding:
                    9px 12px;

                font-size: 11px;
            }

        }


        /* =========================================================
           PRINT
        ========================================================= */

        @media print {

            .sidebar,
            .topbar,
            .sidebar-overlay,
            .mobile-menu-btn {
                display: none !important;
            }

            .main {
                margin-left: 0 !important;
            }

            .page {
                padding: 0 !important;
            }

            body {
                background: white;
            }

        }


        /* =========================================================
           CUSTOM PAGE STYLES
        ========================================================= */

        @yield('styles')

    </style>
</head>


<body>

<div class="app">


    {{-- =========================================================
         SIDEBAR
    ========================================================= --}}

    <aside class="sidebar" id="sidebar">


        {{-- BRAND --}}

        <div class="sidebar-brand">

            <div class="brand-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <div class="brand-text">

                <div class="brand-name">
                    VERITAS
                </div>

                <div class="brand-subtitle">
                    SMK PLUS PELITA NUSANTARA
                </div>

            </div>

        </div>


        {{-- =====================================================
             MENU
        ====================================================== --}}

        <nav class="menu">


            {{-- DASHBOARD --}}

            <div class="menu-title">
                Utama
            </div>

            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

                <i class="fa-solid fa-house"></i>

                <span>
                    Dashboard
                </span>

            </a>


            {{-- =================================================
                 PELANGGARAN
            ================================================== --}}

            <div class="menu-title">
                Pelanggaran
            </div>

            <a href="{{ route('pelanggaran.index') }}"
               class="{{ request()->routeIs('pelanggaran.index') ? 'active' : '' }}">

                <i class="fa-solid fa-list-check"></i>

                <span>
                    Data Pelanggaran
                </span>

            </a>

            <a href="{{ route('pelanggaran.create') }}"
               class="{{ request()->routeIs('pelanggaran.create') ? 'active' : '' }}">

                <i class="fa-solid fa-plus-circle"></i>

                <span>
                    Tambah Pelanggaran
                </span>

            </a>


            {{-- =================================================
                 DATA MASTER
            ================================================== --}}

            <div class="menu-title">
                Data Master
            </div>

            <a href="{{ route('siswa.index') }}"
               class="{{ request()->routeIs('siswa.index') ? 'active' : '' }}">

                <i class="fa-solid fa-users"></i>

                <span>
                    Data Siswa
                </span>

            </a>

            <a href="{{ route('siswa.create') }}"
               class="{{ request()->routeIs('siswa.create') ? 'active' : '' }}">

                <i class="fa-solid fa-user-plus"></i>

                <span>
                    Tambah Siswa
                </span>

            </a>


            {{-- =================================================
                 PRESTASI
            ================================================== --}}

            <div class="menu-title">
                Prestasi
            </div>

            <a href="{{ route('prestasi.index') }}"
               class="{{ request()->routeIs('prestasi.index') ? 'active' : '' }}">

                <i class="fa-solid fa-trophy"></i>

                <span>
                    Data Prestasi
                </span>

            </a>

            <a href="{{ route('prestasi.create') }}"
               class="{{ request()->routeIs('prestasi.create') ? 'active' : '' }}">

                <i class="fa-solid fa-medal"></i>

                <span>
                    Tambah Prestasi
                </span>

            </a>

            <a href="{{ route('leaderboard') }}"
               class="{{ request()->routeIs('leaderboard') ? 'active' : '' }}">

                <i class="fa-solid fa-ranking-star"></i>

                <span>
                    Leaderboard
                </span>

            </a>


        </nav>


        {{-- =====================================================
             USER
        ====================================================== --}}

        <div class="sidebar-user">

            <div class="user-box">


                <div class="user-avatar">

                    <i class="fa-solid fa-user"></i>

                </div>


                <div class="user-info">

                    <div class="user-name">

                        {{ auth()->user()->name ?? 'Administrator' }}

                    </div>

                    <div class="user-role">

                        Administrator

                    </div>

                </div>


                {{-- LOGOUT --}}

                <form method="POST"
                      action="{{ route('logout') }}">

                    @csrf

                    <button type="submit"
                            class="logout-btn"
                            title="Logout">

                        <i class="fa-solid fa-right-from-bracket"></i>

                    </button>

                </form>


            </div>

        </div>


        {{-- =====================================================
             FOOTER
        ====================================================== --}}

        <div class="sidebar-footer">

            VERITAS &copy; {{ date('Y') }}

        </div>


    </aside>


    {{-- =========================================================
         OVERLAY MOBILE
    ========================================================= --}}

    <div class="sidebar-overlay"
         id="sidebarOverlay"></div>


    {{-- =========================================================
         MAIN
    ========================================================= --}}

    <main class="main">


        {{-- =====================================================
             TOPBAR
        ====================================================== --}}

        <header class="topbar">


            <div class="topbar-left">


                {{-- MOBILE MENU --}}

                <button type="button"
                        class="mobile-menu-btn"
                        id="mobileMenuBtn">

                    <i class="fa-solid fa-bars"></i>

                </button>


                {{-- PAGE TITLE --}}

                <div class="page-heading">

                    <h1>
                        @yield('page-title', 'Dashboard')
                    </h1>

                    <p>
                        Sistem Monitoring Siswa
                    </p>

                </div>


            </div>


            {{-- TOPBAR RIGHT --}}

            <div class="topbar-right">

                <div class="topbar-date">

                    <i class="fa-regular fa-calendar"></i>

                    <span>
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>

                </div>

            </div>


        </header>


        {{-- =====================================================
             PAGE CONTENT
        ====================================================== --}}

        <section class="page">


            {{-- SESSION SUCCESS --}}

            @if(session('success'))

                <div class="alert alert-success">

                    <i class="fa-solid fa-circle-check"></i>

                    {{ session('success') }}

                </div>

            @endif


            {{-- SESSION ERROR --}}

            @if(session('error'))

                <div class="alert alert-danger">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    {{ session('error') }}

                </div>

            @endif


            {{-- VALIDATION ERRORS --}}

            @if($errors->any())

                <div class="alert alert-danger">

                    <strong>
                        Terdapat kesalahan:
                    </strong>

                    <ul style="
                        margin: 8px 0 0 18px;
                        line-height: 1.6;
                    ">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- CONTENT DARI HALAMAN --}}

            @yield('content')


        </section>


    </main>


</div>


{{-- =============================================================
     JAVASCRIPT
============================================================= --}}

<script>

    document.addEventListener('DOMContentLoaded', function () {


        const sidebar =
            document.getElementById('sidebar');

        const overlay =
            document.getElementById('sidebarOverlay');

        const mobileMenuBtn =
            document.getElementById('mobileMenuBtn');


        /* =====================================================
           OPEN SIDEBAR
        ====================================================== */

        function openSidebar() {

            sidebar.classList.add('show');

            overlay.classList.add('show');

            document.body.style.overflow = 'hidden';

        }


        /* =====================================================
           CLOSE SIDEBAR
        ====================================================== */

        function closeSidebar() {

            sidebar.classList.remove('show');

            overlay.classList.remove('show');

            document.body.style.overflow = '';

        }


        /* =====================================================
           MOBILE BUTTON
        ====================================================== */

        if (mobileMenuBtn) {

            mobileMenuBtn.addEventListener(
                'click',
                function () {

                    if (
                        sidebar.classList.contains('show')
                    ) {

                        closeSidebar();

                    } else {

                        openSidebar();

                    }

                }
            );

        }


        /* =====================================================
           OVERLAY
        ====================================================== */

        if (overlay) {

            overlay.addEventListener(
                'click',
                closeSidebar
            );

        }


        /* =====================================================
           CLOSE SIDEBAR AFTER CLICK MENU
        ====================================================== */

        const menuLinks =
            document.querySelectorAll('.menu a');

        menuLinks.forEach(function (link) {

            link.addEventListener(
                'click',
                function () {

                    if (
                        window.innerWidth <= 768
                    ) {

                        closeSidebar();

                    }

                }
            );

        });


        /* =====================================================
           RESET SIDEBAR WHEN DESKTOP
        ====================================================== */

        window.addEventListener(
            'resize',
            function () {

                if (
                    window.innerWidth > 768
                ) {

                    closeSidebar();

                }

            }
        );


    });

</script>


{{-- =============================================================
     PAGE SCRIPTS
============================================================= --}}

@yield('scripts')


</body>
</html>
