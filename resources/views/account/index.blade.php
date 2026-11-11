@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('page_title', 'Manajemen Akun')

@section('styles')
@include('partials.ui')

/* =========================================================
   MANAJEMEN AKUN
   ========================================================= */

.ac-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: var(--sp-6);
    margin-bottom: var(--sp-8);
}

.ac-stat {
    padding: var(--sp-7) var(--sp-8);
    border: 1px solid var(--c-border);
    border-radius: var(--r-lg);
    background: var(--c-surface);
    box-shadow: 0 4px 12px rgba(0, 0, 0, .03);
}

.ac-stat .num {
    color: var(--c-text);
    font-size: 22px;
    font-weight: var(--fw-bold);
    line-height: 1;
}

.ac-stat .lbl {
    margin-top: var(--sp-2);
    color: var(--c-text-muted);
    font-size: var(--fs-xs);
    font-weight: var(--fw-semibold);
    letter-spacing: .03em;
    text-transform: uppercase;
}

.ac-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sp-5);
    margin-bottom: var(--sp-7);
}

.ac-search {
    flex: 1 1 240px;
    min-width: 0;
    padding: 10px var(--sp-5);
    border: 1px solid var(--c-border);
    border-radius: var(--r-md);
    background: var(--c-surface);
    font-family: inherit;
    font-size: var(--fs-md);
    outline: none;
}

.ac-search:focus {
    border-color: var(--primary);
    box-shadow: var(--focus-ring);
}

.ac-select {
    padding: 10px var(--sp-5);
    border: 1px solid var(--c-border);
    border-radius: var(--r-md);
    background: var(--c-surface);
    font-family: inherit;
    font-size: var(--fs-md);
    cursor: pointer;
}

.ac-name {
    display: flex;
    align-items: center;
    gap: var(--sp-5);
}

.ac-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    flex: 0 0 36px;
    border-radius: var(--r-full);
    background: rgba(109, 20, 8, .08);
    color: var(--primary);
    font-size: var(--fs-sm);
    font-weight: var(--fw-bold);
}

.ac-name strong {
    display: block;
    color: var(--c-text);
    font-size: var(--fs-base);
    font-weight: var(--fw-semibold);
}

.ac-name small {
    color: var(--c-text-muted);
    font-size: var(--fs-xs);
    font-family: monospace;
}

.ac-actions {
    display: flex;
    gap: var(--sp-3);
    flex-wrap: nowrap;
}

.ac-actions form { margin: 0; }

.ac-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--sp-2);
    padding: 8px 12px;
    border-radius: var(--r-md);
    border: 1px solid transparent;
    font-family: inherit;
    font-size: var(--fs-sm);
    font-weight: var(--fw-semibold);
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: background var(--dur) ease;
}

.ac-btn-edit {
    border-color: #FCD34D;
    background: #FEF3C7;
    color: #92400E;
}

.ac-btn-edit:hover { background: #FDE68A; }

.ac-btn-pass {
    border-color: #BFDBFE;
    background: #EFF6FF;
    color: #1D4ED8;
}

.ac-btn-pass:hover { background: #DBEAFE; }

.ac-btn-delete {
    border-color: #FCA5A5;
    background: #FEE2E2;
    color: #B91C1C;
}

.ac-btn-delete:hover { background: #FECACA; }

@media (max-width: 640px) {
    .ac-toolbar { flex-direction: column; }
    .ac-search,
    .ac-select { width: 100%; min-height: 42px; font-size: 16px; }
}
@endsection

@section('content')

@php
    $inits = function ($nama) {
        return collect(explode(' ', (string) $nama))
            ->filter()
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->take(2)
            ->join('');
    };

    $accessLabel = [
        'full' => ['Akses Penuh', 'badge-info'],
        'pelanggaran_writer' => ['Tambah Pelanggaran', 'badge-warning'],
    ];
@endphp

<div class="page-header">
    <div class="page-heading">
        <h1>Manajemen Akun</h1>
        <p>Kelola akun pengguna, role, dan hak akses.</p>
    </div>

    <a href="{{ route('account.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-plus"></i>
        Tambah Akun
    </a>
</div>

{{-- Statistik --}}
<div class="ac-stats">
    <div class="ac-stat">
        <div class="num">{{ $stats['total'] }}</div>
        <div class="lbl">Total Akun</div>
    </div>
    <div class="ac-stat">
        <div class="num">{{ $stats['admin'] }}</div>
        <div class="lbl">Super Admin</div>
    </div>
    <div class="ac-stat">
        <div class="num">{{ $stats['pds'] }}</div>
        <div class="lbl">Guru PDS</div>
    </div>
    <div class="ac-stat">
        <div class="num">{{ $stats['bk'] }}</div>
        <div class="lbl">Guru BK</div>
    </div>
    <div class="ac-stat">
        <div class="num">{{ $stats['walas'] }}</div>
        <div class="lbl">Wali Kelas</div>
    </div>
    <div class="ac-stat">
        <div class="num">{{ $stats['guru'] }}</div>
        <div class="lbl">Guru</div>
    </div>
</div>

{{-- Toolbar --}}
<div class="ac-toolbar">
    <input type="text"
           id="acSearch"
           class="ac-search"
           placeholder="Cari nama, NIG, atau email..."
           autocomplete="off">

    <select id="acRole" class="ac-select" aria-label="Filter role">
        <option value="">Semua role</option>
        @foreach($roleOptions as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>

{{-- Tabel --}}
<div class="table-card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Jabatan</th>
                    <th>Kelas</th>
                    <th>Hak Akses</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody id="acBody">
                @forelse($users as $user)
                    <tr data-nama="{{ mb_strtolower($user->name) }}"
                        data-ident="{{ mb_strtolower($user->nig ?? $user->email) }}"
                        data-role="{{ $user->role }}">

                        <td>{{ $loop->iteration }}</td>

                        <td>
                            <div class="ac-name">
                                <div class="ac-avatar">{{ $inits($user->name) }}</div>
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    <small>{{ $user->nig ?: $user->email }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge badge-gray">
                                {{ $roleOptions[$user->role] ?? $user->role }}
                            </span>
                        </td>

                        <td>{{ $user->jabatan ?: '-' }}</td>

                        <td>{{ $user->kelas ?: '-' }}</td>

                        <td>
                            @if($user->access_level && isset($accessLabel[$user->access_level]))
                                <span class="badge {{ $accessLabel[$user->access_level][1] }}">
                                    {{ $accessLabel[$user->access_level][0] }}
                                </span>
                            @else
                                <span class="badge badge-gray">Normal</span>
                            @endif
                        </td>

                        <td>
                            <div class="ac-actions">
                                <a href="{{ route('account.edit', $user->id) }}" class="ac-btn ac-btn-edit">
                                    <i class="fa-solid fa-pen"></i>
                                    Edit
                                </a>

                                <a href="{{ route('account.password.edit', $user->id) }}" class="ac-btn ac-btn-pass">
                                    <i class="fa-solid fa-key"></i>
                                    Password
                                </a>

                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('account.destroy', $user->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="ac-btn ac-btn-delete"
                                                onclick="return confirm('Yakin ingin menghapus akun {{ $user->name }}?')">
                                            <i class="fa-solid fa-trash"></i>
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa-solid fa-users-slash"></i></div>
                                Belum ada akun.
                            </div>
                        </td>
                    </tr>
                @endforelse

                <tr id="acNoResult" hidden>
                    <td colspan="7">
                        <div class="empty-state">Tidak ada akun yang cocok dengan pencarian.</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    'use strict';

    var search   = document.getElementById('acSearch');
    var role     = document.getElementById('acRole');
    var body     = document.getElementById('acBody');
    var noResult = document.getElementById('acNoResult');
    var rows     = body.querySelectorAll('tr[data-nama]');

    function apply() {
        var q    = search.value.toLowerCase().trim();
        var r    = role.value;
        var show = 0;

        rows.forEach(function (row) {
            var cocokQ = row.dataset.nama.indexOf(q) !== -1 || row.dataset.ident.indexOf(q) !== -1;
            var cocokR = !r || row.dataset.role === r;
            var tampil = cocokQ && cocokR;

            row.hidden = !tampil;
            if (tampil) { show++; }
        });

        noResult.hidden = !(rows.length && show === 0);
    }

    search.addEventListener('input', apply);
    role.addEventListener('change', apply);
})();
</script>
@endsection
