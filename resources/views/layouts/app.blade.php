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

    {{-- Tom Select CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

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

        html,
        body {
            max-width: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #F9F6F2;
            color: #1F2937;
            min-height: 100vh;

            /* Cegah scroll horizontal akibat elemen fixed-width.
               Pakai clip (bukan hidden) supaya position:sticky topbar tetap jalan. */
            overflow-x: clip;
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

        img,
        svg,
        video,
        canvas,
        iframe {
            max-width: 100%;
        }

        img,
        video {
            height: auto;
        }

        /* Teks panjang (nama, NISN, keterangan) tidak boleh menyebabkan overflow */
        p,
        span,
        td,
        th,
        h1,
        h2,
        h3,
        h4,
        strong,
        small,
        label {
            overflow-wrap: break-word;
        }

        [hidden] {
            display: none !important;
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
            --topbar-height: 72px;

            --shadow-sm:
                0 2px 8px rgba(0, 0, 0, 0.05);

            --shadow:
                0 8px 24px rgba(0, 0, 0, 0.07);

            --dur: 0.2s;

            --focus-ring: 0 0 0 3px rgba(109, 20, 8, 0.35);

            /* =====================================================
               DESIGN TOKENS
               Satu sumber kebenaran untuk nilai yang sebelumnya
               ditulis ulang-ulang dengan angka berbeda di tiap view.
               Aturan main: kalau sebuah nilai pernah ditulis beda
               di beberapa halaman, pindahkan ke sini, jangan
               menulis angka mentah di view.
               ===================================================== */

            /* --- Typography --- */
            --fs-2xs: 10px;
            --fs-xs: 11px;
            --fs-sm: 12px;
            --fs-base: 13px;
            --fs-md: 14px;
            --fs-lg: 16px;
            --fs-xl: 18px;

            --lh-tight: 1.2;
            --lh-normal: 1.4;
            --lh-relaxed: 1.6;

            --fw-normal: 400;
            --fw-medium: 500;
            --fw-semibold: 600;
            --fw-bold: 700;

            /* --- Radius --- */
            --r-xs: 6px;
            --r-sm: 8px;
            --r-md: 10px;
            --r-lg: 12px;
            --r-xl: 16px;
            --r-2xl: 20px;
            --r-full: 9999px;

            /* --- Spacing --- */
            --sp-1: 4px;
            --sp-2: 6px;
            --sp-3: 8px;
            --sp-4: 10px;
            --sp-5: 12px;
            --sp-6: 14px;
            --sp-7: 16px;
            --sp-8: 20px;
            --sp-9: 24px;

            /* --- Semantic colours --- */
            --c-text: var(--dark);
            --c-text-muted: var(--muted);
            --c-text-soft: #9CA3AF;
            --c-surface: var(--white);
            --c-surface-alt: #F9FAFB;
            --c-border: var(--border);
            --c-border-soft: #F1F1F1;

            /* Badge status */
            --c-badge-danger-bg: #FEF2F2;
            --c-badge-danger-text: #991B1B;
            --c-badge-info-bg: #EFF6FF;
            --c-badge-info-text: #1D4ED8;
            --c-badge-success-bg: #DCFCE7;
            --c-badge-success-text: #166534;
            --c-badge-warning-bg: #FEF3C7;
            --c-badge-warning-text: #92400E;
            --c-badge-neutral-bg: #F3F4F6;
            --c-badge-neutral-text: #4B5563;

            /* Poin: warnanya ditetapkan di sini agar sama di semua halaman. */
            --c-point-bg: var(--primary);
            --c-point-text: #FFFFFF;

            /* Table header: dark surface + white text, konsisten dengan sidebar. */
            --c-thead-bg: var(--dark);
            --c-thead-text: #FFFFFF;
            --c-row-hover: #F9FAFB;
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
            height: var(--topbar-height);

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

        /* Lencana jumlah di samping menu (mis. Pending Verifikasi) */
        .menu-badge {
            margin-left: auto;

            min-width: 20px;

            padding: 2px 7px;

            border-radius: 99px;

            background: #EF4444;

            color: #fff;

            font-size: 10px;
            font-weight: 700;

            text-align: center;
        }

        /* Daftar kelas binaan */
        .menu-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;

            padding: 8px 13px;
        }

        .menu-chip {
            display: inline-block;

            padding: 4px 10px;

            border: 1px solid rgba(109,20,8,0.3);
            border-radius: 6px;

            background: rgba(109,20,8,0.15);

            color: #D1D5DB;

            font-size: 11px;
            font-weight: 600;
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
            position: sticky;
            top: 0;
            z-index: 900;

            height: var(--topbar-height);

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 16px;

            padding: 0 30px;

            background: white;

            border-bottom:
                1px solid var(--border);
        }

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

            flex-shrink: 0;

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

        /* =========================================================
           REMAH ROTI (BREADCRUMB)
        ========================================================= */

        .topbar-crumb {
            display: flex;
            align-items: center;

            gap: 6px;

            min-width: 0;

            font-size: 13px;

            color: var(--muted);
        }

        /* Ikon chevron dipisah agar jaraknya konsisten
           dan tidak ikut hilang bersama teks panjang. */
        .topbar-crumb .crumb-sep {
            flex: 0 0 auto;

            font-size: 9px;

            color: #C4CAD3;
        }

        .crumb-link {
            display: inline-flex;
            align-items: center;

            gap: 6px;

            min-width: 0;

            padding: 4px 8px;

            border-radius: 7px;

            color: var(--muted);

            text-decoration: none;

            white-space: nowrap;

            transition:
                background 0.15s ease,
                color 0.15s ease;
        }

        .crumb-link i {
            font-size: 11px;
            flex: 0 0 auto;
        }

        .crumb-link:hover {
            background: #F7F2EF;
            color: var(--primary);
        }

        .crumb-link:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 1px;
        }

        /* Level terakhir: halaman yang sedang dibuka. */
        .crumb-page {
            display: inline-flex;
            align-items: center;

            gap: 6px;

            min-width: 0;

            font-size: 15px;
            font-weight: 700;

            color: var(--dark);
        }

        .crumb-page i {
            font-size: 12px;
            flex: 0 0 auto;
            color: var(--primary);
        }

        /* Judul panjang dipotong di tengah, bukan di awal, supaya
           ikon dan awal kata tetap terbaca. */
        .crumb-page span,
        .crumb-link span {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Cegah judul sangat panjang menabrak blok tanggal. */
        .topbar-left {
            flex: 1 1 auto;
            min-width: 0;
        }

        .topbar-crumb {
            overflow: hidden;
        }


        /* =========================================================
           TOPBAR RIGHT
        ========================================================= */

        .topbar-right {
            display: flex;
            align-items: center;

            gap: 10px;

            flex-shrink: 0;
        }

        .topbar-date {
            display: flex;
            align-items: center;

            gap: 7px;

            font-size: 11px;

            color: var(--muted);

            padding: 8px 12px;

            background: #F9FAFB;

            border:
                1px solid var(--border);

            border-radius: 8px;

            white-space: nowrap;
        }

        .topbar-date i {
            color: var(--primary);
        }

        .mobile-menu-btn:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
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

            max-width: 100%;

            overflow-x: auto;

            -webkit-overflow-scrolling: touch;
        }

        .table {
            width: 100%;

            max-width: 100%;

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

        @media (max-width: 1200px) {

            .topbar-date {
                display: none;
            }

        }

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
           RESPONSIVE SMALL TABLET
        ========================================================= */

        @media (max-width: 900px) {

            :root {
                --sidebar-width: 215px;
            }

            .sidebar {
                width: 215px;
            }

            .main {
                margin-left: 215px;
            }

            .topbar {
                padding:
                    0 18px;
            }

            .page {
                padding:
                    20px 18px 30px;
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
                height: 66px;

                padding:
                    0 15px;
            }

            .mobile-menu-btn {
                display: flex;
            }

            /* Di layar sempit hanya level terakhir yang tampil: penanda
               posisi sudah jelas dari isi halaman, dan rantai breadcrumb
               pendek lebih mudah dibaca. */
            .topbar-crumb .crumb-link,
            .topbar-crumb .crumb-sep {
                display: none;
            }

            .topbar-crumb .crumb-page {
                font-size: 15px;
            }

            .topbar-right {
                gap: 8px;
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

            /* Target sentuh yang nyaman di mobile */
            .btn {
                min-height: 40px;
            }

            /* Cegah input Safari/iOS auto-zoom saat fokus */
            .form-control,
            .form-label {
                font-size: 16px;
            }

            .form-label {
                margin-bottom: 6px;
            }

            .table {
                min-width: 520px;
            }

            .table th,
            .table td {
                padding:
                    10px 11px;
            }

            .empty-state {
                padding:
                    34px 16px;
            }

        }


        /* =========================================================
           SMALL MOBILE
        ========================================================= */

        @media (max-width: 480px) {

            .topbar {
                height: 62px;

                gap: 10px;
            }

            .mobile-menu-btn {
                width: 36px;
                height: 36px;
            }

            .topbar-crumb .crumb-page {
                font-size: 14px;
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

                min-height: 38px;
            }

            .table {
                min-width: 460px;
            }

            .table th,
            .table td {
                padding:
                    9px 9px;

                font-size: 11px;
            }

            .form-control {
                padding:
                    10px 11px;
            }

            .alert {
                padding:
                    11px 12px;

                font-size: 11.5px;
            }

            .pagination-wrapper {
                overflow-x: auto;
            }

        }


        /* =========================================================
           RESPONSIVE EXTRA SMALL
        ========================================================= */

        @media (max-width: 380px) {

            .page {
                padding:
                    14px 10px 22px;
            }

            .topbar-crumb .crumb-page {
                font-size: 13px;
            }

            .table {
                min-width: 400px;
            }

            .btn {
                padding:
                    8px 10px;

                font-size: 10.5px;
            }

        }


        /* =========================================================
           TOM SELECT (PILIHAN SEARCHABLE)
        ========================================================= */

        .searchable-select {
            width: 100%;

            max-width: 100%;
        }

        .ts-wrapper {
            width: 100% !important;

            max-width: 100%;
        }

        .ts-control {
            max-width: 100%;

            min-height: 38px;
        }

        .ts-dropdown {
            max-width: 100%;

            max-height: 260px;
        }

        @media (max-width: 576px) {

            .ts-control input {
                font-size: 16px !important;
            }

            .ts-dropdown {
                position: static !important;

                width: 100% !important;

                max-height: 220px;

                overflow-y: auto;
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

@php
    /*
     * Data untuk sidebar dan topbar dihitung SEKALI di sini.
     * Sebelumnya jumlah laporan pending di-query tiga kali per halaman
     * (sidebar PDS, sidebar admin, topbar).
     *
     * $canVerify sengaja tetap dihitung walau tidak dipakai topbar:
     * sidebar memakai peran yang sama untuk menentukan menu mana
     * yang perlu lencana jumlah laporan.
     */

    $topUser   = auth()->user();
    $canVerify = (bool) $topUser?->canVerify();
    $isWalas   = (bool) $topUser?->isWalas();

    // Super Admin (role admin) dan pemegang akses penuh memakai menu
    // lengkap. Bedanya, menu "Manajemen Akun" hanya untuk Super Admin.
    $isAdmin       = (bool) $topUser?->isAdmin();
    $hasFullAccess = (bool) $topUser?->hasFullAccess();

    // ---- Label portal (muncul di penunjuk lokasi) ----
    $portalLabel = match (true) {
        (bool) $topUser?->isPimpinan() => 'Pimpinan Sekolah',
        $isWalas                    => 'Wali Kelas',
        (bool) $topUser?->isGuru()  => 'Guru Khusus',
        (bool) $topUser?->isPds()   => 'Guru PDS',
        (bool) $topUser?->isBk()    => 'Guru BK',
        default                     => 'Administrator',
    };

    // ---- Jumlah laporan pending ----
    $pendingCount = ($canVerify || $topUser?->isPds())
        ? \App\Models\Pelanggaran::where('status', 'pending')->count()
        : 0;

    /*
     * =========================================================
     * REMAH ROTI (BREADCRUMB) TIGA LEVEL
     * =========================================================
     *
     * Level: Portal > Grup > Halaman.
     *
     * Peta route ke grup ditulis di sini, bukan di tiap view, supaya
     * semua halaman dalam satu grup pasti punya induk yang sama.
     * Tiap entri: [label grup, route induk, ikon].
     *
     * Route induk TIDAK selalu bisa dibuka user yang sedang masuk —
     * misalnya Wali Kelas tidak boleh membuka /dashboard. Karena itu
     * URL induk divalidasi dulu lewat $parentUrl() sebelum
     * dirender sebagai tautan; kalau tidak boleh, grup ditampilkan
     * sebagai teks biasa agar tidak ada link yang mati.
     */

    $NAV_GRUP = [
        // --- Pelanggaran ---
        'pelanggaran.index' => ['Data Pelanggaran', 'pelanggaran.index', 'fa-shield-halved'],
        'pelanggaran.pending' => ['Data Pelanggaran', 'pelanggaran.index', 'fa-hourglass-half'],
        'pelanggaran.create' => ['Data Pelanggaran', 'pelanggaran.index', 'fa-shield-halved'],
        'pelanggaran.edit' => ['Data Pelanggaran', 'pelanggaran.index', 'fa-shield-halved'],
        'pelanggaran.show' => ['Data Pelanggaran', 'pelanggaran.index', 'fa-shield-halved'],

        // --- Pelaporan (dipakai Walas & Guru) ---
        'lapor.index' => ['Pelaporan', 'lapor.index', 'fa-bullhorn'],
        'lapor.riwayat' => ['Pelaporan', 'lapor.index', 'fa-bullhorn'],

        // --- Data Siswa ---
        'siswa.index' => ['Data Siswa', 'siswa.index', 'fa-users'],
        'siswa.create' => ['Data Siswa', 'siswa.index', 'fa-users'],
        'siswa.edit' => ['Data Siswa', 'siswa.index', 'fa-users'],
        'siswa.show' => ['Data Siswa', 'siswa.index', 'fa-users'],

        // --- Data Prestasi ---
        'prestasi.index' => ['Data Prestasi', 'prestasi.index', 'fa-award'],
        'prestasi.create' => ['Data Prestasi', 'prestasi.index', 'fa-award'],
        'prestasi.edit' => ['Data Prestasi', 'prestasi.index', 'fa-award'],
        'prestasi.show' => ['Data Prestasi', 'prestasi.index', 'fa-award'],

        // --- Sistem & Bantuan ---
        'backup.index' => ['Sistem', 'backup.index', 'fa-file-export'],
        'maintenance.index' => ['Sistem', 'maintenance.index', 'fa-broom'],
        'account.index' => ['Sistem', 'account.index', 'fa-user-shield'],
        'account.create' => ['Sistem', 'account.index', 'fa-user-shield'],
        'account.edit' => ['Sistem', 'account.index', 'fa-user-shield'],
        'account.password.edit' => ['Sistem', 'account.index', 'fa-key'],

        // --- Tanpa induk ---
        'leaderboard' => ['Leaderboard', null, 'fa-ranking-star'],
        'dashboard' => [null, null, 'fa-gauge-high'],
    ];

    /*
     * Route yang boleh dibuka tiap peran untuk link remah roti.
     * Mengikuti aturan yang sama dengan EnsureCanManage supaya
     * crumb tidak pernah mengarah ke halaman yang akan
     * dibatalkan middleware.
     */
    $NAV_BOLEH = [
        'admin' => '*',
        'pds' => ['dashboard', 'pelanggaran.index', 'pelanggaran.pending', 'pelanggaran.show',
            'pelanggaran.create', 'lapor.index', 'lapor.riwayat', 'siswa.index',
            'siswa.show', 'siswa.create', 'siswa.edit', 'prestasi.index',
            'leaderboard', 'prestasi.show'],
        'bk' => ['dashboard', 'pelanggaran.index', 'pelanggaran.pending', 'pelanggaran.show',
            'lapor.index', 'lapor.riwayat', 'siswa.index', 'siswa.show',
            'siswa.create', 'siswa.edit', 'prestasi.index', 'leaderboard', 'prestasi.show'],
        /*
         * Guru biasa TIDAK diberi Data Pelanggaran: daftar pelanggaran
         * dan rinciannya memuat data pribadi siswa.
         */
        'guru' => ['lapor.index', 'lapor.riwayat', 'siswa.index',
            'prestasi.index', 'leaderboard', 'prestasi.show'],

        /*
         * Unsur pimpinan (kepsek + waka) punya role "guru" yang sama,
         * tapi tetap boleh melihat Data Pelanggaran karena tugasnya
         * monitoring seluruh sekolah. User::navKey() yang memilih
         * kuncinya, jadi tidak perlu role baru di tabel users.
         */
        'pimpinan' => ['lapor.index', 'lapor.riwayat', 'siswa.index',
            'pelanggaran.index', 'pelanggaran.show',
            'pelanggaran.pending', 'pelanggaran.create',
            'prestasi.index', 'leaderboard', 'prestasi.show'],
        'walas' => ['lapor.index', 'lapor.riwayat', 'pelanggaran.index', 'pelanggaran.show',
            'prestasi.index', 'leaderboard', 'prestasi.show'],
    ];

    /*
     * Ubah nama route menjadi URL, atau null kalau user yang sedang
     * masuk tidak boleh membuka route tersebut (dan route-nya memang
     * ada).芯片组 null berarti level itu ditampilkan sebagai teks
     * biasa, bukan tautan mati.
     */
    $parentUrl = function (?string $routeName) use ($topUser, $NAV_BOLEH): ?string {
        if (! $routeName || ! \Illuminate\Support\Facades\Route::has($routeName)) {
            return null;
        }

        // Peran tak dikenal (mis. admin) bebas.
        //
        // navKey() bukan role: ia memilih kunci nav yang sesuai untuk
        // user. Unsur pimpinan memakai role "guru" seperti guru biasa,
        // tapi punya daftar route sendiri.
        $boleh = $NAV_BOLEH[$topUser?->navKey()] ?? [];

        if ($boleh !== '*' && ! in_array($routeName, $boleh, true)) {
            return null;
        }

        return route($routeName);
    };

    $routeSekarang = request()->route()?->getName();

    // Judul halaman boleh diganti view lewat section opsional.
    $judulHalaman = trim((string) $__env->yieldContent('page_title')) ?: 'Beranda';

    // Susun remah roti: [label, url|null, ikon|null, apakah ini halaman ini]
    $crumbs = [];

    $portalUrl = match (true) {
        $isWalas, (bool) $topUser?->isGuru() => $parentUrl('lapor.index'),
        default => $parentUrl('dashboard'),
    };

    $crumbs[] = ['label' => $portalLabel, 'url' => $portalUrl, 'icon' => 'fa-house', 'current' => false];

    if (isset($NAV_GRUP[$routeSekarang])) {
        [$labelGrup, $routeInduk, $ikonGrup] = $NAV_GRUP[$routeSekarang];

        if ($labelGrup) {
            $crumbs[] = [
                'label' => $labelGrup,
                'url' => $parentUrl($routeInduk),
                'icon' => $ikonGrup,
                'current' => false,
            ];
        }
    }

    $crumbs[] = [
        'label' => $judulHalaman,
        'url' => null,
        'icon' => $NAV_GRUP[$routeSekarang][2] ?? 'fa-file-lines',
        'current' => true,
    ];

@endphp

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

            @if(auth()->check() && $isWalas)

                {{-- =================================================
                     MENU KHUSUS WALI KELAS (HANYA PELAPORAN & KELAS)
                ================================================== --}}
                <div class="menu-title">
                    Menu Wali Kelas
                </div>

                <a href="{{ route('lapor.index') }}"
                   class="{{ request()->routeIs('lapor.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-bullhorn"></i>
                    <span>Lapor Pelanggaran</span>
                </a>

                <a href="{{ route('lapor.riwayat') }}"
                   class="{{ request()->routeIs('lapor.riwayat') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Riwayat Laporan</span>
                </a>

                {{-- KELAS SAYA --}}
                <div class="menu-title">
                    Kelas Saya ({{ auth()->user()->kelas ?? 'Walas' }})
                </div>

                <a href="{{ route('pelanggaran.index') }}"
                   class="{{ request()->routeIs('pelanggaran.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Pelanggaran Kelas</span>
                </a>

                <a href="{{ route('prestasi.index') }}"
                   class="{{ request()->routeIs('prestasi.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-trophy"></i>
                    <span>Prestasi Kelas</span>
                </a>

                <a href="{{ route('leaderboard') }}"
                   class="{{ request()->routeIs('leaderboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-ranking-star"></i>
                    <span>Leaderboard</span>
                </a>

            @elseif(auth()->check() && auth()->user()->isGuru())

                {{-- =================================================
                     MENU GURU KHUSUS / GURU PIKET
                     - Bisa: Report Pelanggaran SEMUA KELAS & SISWA
                     - Bisa: Lihat Data Siswa & Semua Kelas
                ================================================== --}}
                <div class="menu-title">
                    Menu Guru Khusus
                </div>

                <a href="{{ route('lapor.index') }}"
                   class="{{ request()->routeIs('lapor.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-bullhorn"></i>
                    <span>Lapor Pelanggaran</span>
                </a>

                <a href="{{ route('lapor.riwayat') }}"
                   class="{{ request()->routeIs('lapor.riwayat') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Riwayat Laporan Saya</span>
                </a>

                {{-- DIREKTORI DATA KELAS & SISWA --}}
                <div class="menu-title">
                    Data Kelas &amp; Siswa
                </div>

                <a href="{{ route('siswa.index') }}"
                   class="{{ request()->routeIs('siswa.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Semua Data Siswa</span>
                </a>

                {{-- MONITORING SEKOLAH --}}
                <div class="menu-title">
                    Monitoring
                </div>

                @if(auth()->user()->isPimpinan())
                    {{--
                        Hanya unscursrah pimpinan (kepsek + waka) yang punya
                        role "guru" tetapi tetap boleh melihat Data
                        Pelanggaran. Guru biasa tidak, karena halamannya
                        memuat data pribadi siswa.
                    --}}
                    <a href="{{ route('pelanggaran.index') }}"
                       class="{{ request()->routeIs('pelanggaran.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Pelanggaran Siswa</span>
                    </a>
                @endif

                @if(auth()->user()->canDirectAddPelanggaran())
                    {{--
                        Pimpinan tertentu (mis. Waka Humas) diberi hak
                        menambah pelanggaran langsung.
                    --}}
                    <a href="{{ route('pelanggaran.create') }}"
                       class="{{ request()->routeIs('pelanggaran.create') ? 'active' : '' }}">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Tambah Pelanggaran</span>
                    </a>
                @endif

                @if(auth()->user()->canVerify())
                    {{--
                        Verifikator (mis. Waka Humas) memverifikasi laporan
                        masuk layaknya Guru PDS.
                    --}}
                    <a href="{{ route('pelanggaran.pending') }}"
                       class="{{ request()->routeIs('pelanggaran.pending') ? 'active' : '' }}">
                        <i class="fa-solid fa-hourglass-half"></i>
                        <span>Pending Verifikasi</span>
                        @if($pendingCount > 0)
                            <span class="menu-badge">{{ $pendingCount }}</span>
                        @endif
                    </a>
                @endif

                <a href="{{ route('prestasi.index') }}"
                   class="{{ request()->routeIs('prestasi.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-trophy"></i>
                    <span>Prestasi Siswa</span>
                </a>

                <a href="{{ route('leaderboard') }}"
                   class="{{ request()->routeIs('leaderboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-ranking-star"></i>
                    <span>Leaderboard</span>
                </a>

            @elseif(auth()->check() && auth()->user()->isBk())

                {{-- =================================================
                     MENU GURU BK (Bimbingan Konseling)
                     - Bisa: Report Pelanggaran & Tambah Prestasi
                     - Tidak Bisa: Verifikasi / Acc Pelanggaran
                ================================================== --}}
                <div class="menu-title">
                    Utama
                </div>

                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>

                {{-- PELAPORAN PELANGGARAN --}}
                <div class="menu-title">
                    Pelanggaran Siswa
                </div>

                <a href="{{ route('lapor.index') }}"
                   class="{{ request()->routeIs('lapor.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-bullhorn"></i>
                    <span>Lapor Pelanggaran</span>
                </a>

                <a href="{{ route('lapor.riwayat') }}"
                   class="{{ request()->routeIs('lapor.riwayat') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Riwayat Laporan Saya</span>
                </a>

                <a href="{{ route('pelanggaran.index') }}"
                   class="{{ request()->routeIs('pelanggaran.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Data Pelanggaran</span>
                </a>

                {{-- PRESTASI --}}
                <div class="menu-title">
                    Prestasi Siswa
                </div>

                <a href="{{ route('prestasi.index') }}"
                   class="{{ request()->routeIs('prestasi.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-trophy"></i>
                    <span>Data Prestasi</span>
                </a>

                <a href="{{ route('prestasi.create') }}"
                   class="{{ request()->routeIs('prestasi.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-medal"></i>
                    <span>Tambah Prestasi</span>
                </a>

                <a href="{{ route('leaderboard') }}"
                   class="{{ request()->routeIs('leaderboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-ranking-star"></i>
                    <span>Leaderboard</span>
                </a>

                {{-- DATA SISWA --}}
                <div class="menu-title">
                    Data Siswa
                </div>

                <a href="{{ route('siswa.index') }}"
                   class="{{ request()->routeIs('siswa.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Data Siswa</span>
                </a>

                {{-- KELAS BINAAN BK --}}
                @if(count(auth()->user()->getBinaanKelasList()) > 0)
                    <div class="menu-title">
                        Kelas Binaan BK
                    </div>

                    <div class="menu-chips">
                        @foreach(auth()->user()->getBinaanKelasList() as $kelasBinaan)
                            <span class="menu-chip">{{ $kelasBinaan }}</span>
                        @endforeach
                    </div>
                @endif

            @elseif(auth()->check() && auth()->user()->isPds())

                {{-- =================================================
                     MENU GURU PDS (Penegak Disiplin Sekolah)
                     - Bisa: Verifikasi / Acc Pelanggaran & Tambah Pelanggaran
                     - Tidak Bisa: Tambah Prestasi
                ================================================== --}}
                <div class="menu-title">
                    Utama
                </div>

                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>

                {{-- DISIPLIN & PELANGGARAN --}}
                <div class="menu-title">
                    Disiplin &amp; Pelanggaran
                </div>

                <a href="{{ route('pelanggaran.pending') }}"
                   class="{{ request()->routeIs('pelanggaran.pending') ? 'active' : '' }}">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <span>Pending Verifikasi</span>
                    @if($pendingCount > 0)
                        <span class="menu-badge">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('pelanggaran.index') }}"
                   class="{{ request()->routeIs('pelanggaran.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Data Pelanggaran</span>
                </a>

                <a href="{{ route('pelanggaran.create') }}"
                   class="{{ request()->routeIs('pelanggaran.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Tambah Pelanggaran</span>
                </a>

                {{-- PRESTASI SISWA (READ-ONLY) --}}
                <div class="menu-title">
                    Prestasi &amp; Aktivitas
                </div>

                <a href="{{ route('prestasi.index') }}"
                   class="{{ request()->routeIs('prestasi.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-trophy"></i>
                    <span>Data Prestasi</span>
                </a>

                <a href="{{ route('leaderboard') }}"
                   class="{{ request()->routeIs('leaderboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-ranking-star"></i>
                    <span>Leaderboard</span>
                </a>

                {{-- DATA MASTER --}}
                <div class="menu-title">
                    Data Master
                </div>

                <a href="{{ route('siswa.index') }}"
                   class="{{ request()->routeIs('siswa.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Data Siswa</span>
                </a>

                {{-- KELAS BINAAN PDS --}}
                @if(count(auth()->user()->getBinaanKelasList()) > 0)
                    <div class="menu-title">
                        Kelas Binaan PDS
                    </div>

                    <div class="menu-chips">
                        @foreach(auth()->user()->getBinaanKelasList() as $kelasBinaan)
                            <span class="menu-chip">{{ $kelasBinaan }}</span>
                        @endforeach
                    </div>
                @endif

            @elseif(auth()->check() && $hasFullAccess)

                {{-- =================================================
                     MENU AKSES PENUH (mis. Waka Kesiswaan)
                     Sama seperti Super Admin, TANPA Manajemen Akun.
                ================================================== --}}
                @include('partials.menu-admin')

            @else

                {{-- =================================================
                     MENU SUPER ADMIN (AKSES PENUH)
                ================================================== --}}
                <div class="menu-title">
                    Utama
                </div>

                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>

                {{-- PELANGGARAN --}}
                <div class="menu-title">
                    Pelanggaran
                </div>

                <a href="{{ route('pelanggaran.index') }}"
                   class="{{ request()->routeIs('pelanggaran.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Data Pelanggaran</span>
                </a>

                <a href="{{ route('pelanggaran.pending') }}"
                   class="{{ request()->routeIs('pelanggaran.pending') ? 'active' : '' }}">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <span>Pending Laporan</span>
                    @if($pendingCount > 0)
                        <span class="menu-badge">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('pelanggaran.create') }}"
                   class="{{ request()->routeIs('pelanggaran.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Tambah Pelanggaran</span>
                </a>

                <a href="{{ route('lapor.index') }}"
                   class="{{ request()->routeIs('lapor.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-bullhorn"></i>
                    <span>Form Lapor Pelanggaran</span>
                </a>

                {{-- DATA MASTER --}}
                <div class="menu-title">
                    Data Master
                </div>

                <a href="{{ route('siswa.index') }}"
                   class="{{ request()->routeIs('siswa.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Data Siswa</span>
                </a>

                <a href="{{ route('siswa.create') }}"
                   class="{{ request()->routeIs('siswa.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Tambah Siswa</span>
                </a>

                {{-- PRESTASI --}}
                <div class="menu-title">
                    Prestasi
                </div>

                <a href="{{ route('prestasi.index') }}"
                   class="{{ request()->routeIs('prestasi.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-trophy"></i>
                    <span>Data Prestasi</span>
                </a>

                <a href="{{ route('prestasi.create') }}"
                   class="{{ request()->routeIs('prestasi.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-medal"></i>
                    <span>Tambah Prestasi</span>
                </a>

                <a href="{{ route('leaderboard') }}"
                   class="{{ request()->routeIs('leaderboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-ranking-star"></i>
                    <span>Leaderboard</span>
                </a>

                {{-- SISTEM --}}
                <div class="menu-title">
                    Sistem
                </div>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('account.index') }}"
                       class="{{ request()->routeIs('account.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-shield"></i>
                        <span>Manajemen Akun</span>
                    </a>
                @endif

                <a href="{{ route('backup.index') }}"
                   class="{{ request()->routeIs('backup.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-export"></i>
                    <span>Backup Data Tahunan</span>
                </a>

                <a href="{{ route('maintenance.index') }}"
                   class="{{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-broom"></i>
                    <span>Bersihkan Website</span>
                </a>

            @endif

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
                        {{ auth()->user()->role_label ?? 'Administrator' }}
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


            {{-- KIRI: tombol menu (mobile) + penunjuk lokasi --}}
            <div class="topbar-left">

                <button type="button"
                        class="mobile-menu-btn"
                        id="mobileMenuBtn"
                        aria-label="Buka menu"
                        aria-controls="sidebar">

                    <i class="fa-solid fa-bars"></i>

                </button>

                {{-- Remah roti tiga level: Portal > Grup > Halaman.
                     Level yang punya URL jadi tautan, level terakhir
                     adalah teks tebal penanda halaman ini. --}}
                <nav class="topbar-crumb" aria-label="Remah roti">

                    @foreach ($crumbs as $crumb)
                        @if (! $loop->first)
                            <i class="fa-solid fa-chevron-right crumb-sep" aria-hidden="true"></i>
                        @endif

                        @if ($crumb['url'] && ! $crumb['current'])
                            <a class="crumb-link" href="{{ $crumb['url'] }}">
                                <i class="fa-solid {{ $crumb['icon'] }}" aria-hidden="true"></i>
                                <span>{{ $crumb['label'] }}</span>
                            </a>
                        @else
                            {{-- Halaman ini: bukan tautan, ditandai untuk pembaca layar. --}}
                            <strong class="crumb-page"
                                    @if ($crumb['current']) aria-current="page" @endif>
                                <i class="fa-solid {{ $crumb['icon'] }}" aria-hidden="true"></i>
                                <span>{{ $crumb['label'] }}</span>
                            </strong>
                        @endif
                    @endforeach

                </nav>

            </div>


            {{--
                Topbar sengaja hanya berisi dua hal: penunjuk lokasi
                (navigasi) dan tanggal.

                Pencarian siswa, tombol aksi cepat, dan lonceng
                notifikasi dihapus dari sini. Jumlah laporan yang
                menunggu verifikasi tetap terlihat lewat lencana
                pada menu "Pending Verifikasi" di sidebar.
            --}}
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
     JAVASCRIPT LAYOUT
============================================================= --}}

<script>

    document.addEventListener('DOMContentLoaded', function () {

        var $ = function (id) { return document.getElementById(id); };


        /* =====================================================
           SIDEBAR (MOBILE)
        ====================================================== */

        var sidebar       = $('sidebar');
        var overlay       = $('sidebarOverlay');
        var mobileMenuBtn = $('mobileMenuBtn');

        function openSidebar() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function () {
                if (sidebar.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        // Tutup sidebar setelah memilih menu di layar kecil
        document.querySelectorAll('.menu a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 768) { closeSidebar(); }
            });
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) { closeSidebar(); }
        });


    });
</script>

@yield('scripts')


</body>
</html>
