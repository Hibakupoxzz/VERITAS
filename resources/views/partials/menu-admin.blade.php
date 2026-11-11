{{--
    =========================================================
    MENU SUPER ADMIN / AKSES PENUH
    =========================================================
    Dipakai dua tempat di layout:
      - Super Admin (role admin)
      - Pemegang "akses penuh" (access_level = full), mis. Waka
        Kesiswaan — menu sama, tetapi Manajemen Akun disembunyikan.

    Variabel yang dipakai dari induk: $pendingCount (sudah dihitung
    sekali di @php layout).
--}}

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
