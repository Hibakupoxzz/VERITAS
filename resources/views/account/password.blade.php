@extends('layouts.app')

@section('title', 'Ubah Password')
@section('page_title', 'Ubah Password')

@section('styles')
@include('partials.ui')
@endsection

@section('content')

<div class="pv-page-header">
    <div class="pv-page-icon">
        <i class="fa-solid fa-key"></i>
    </div>
    <div class="pv-page-headtext">
        <h1 class="pv-page-title">Ubah Password</h1>
        <p class="pv-page-sub">
            {{ $account->name }} &middot; {{ $account->nig ?: $account->email }}
        </p>
    </div>
</div>

<div class="pv-card">
    <div style="padding: var(--sp-9);">

        <div class="pv-section-label">
            <i class="fa-solid fa-lock"></i>
            Password Baru
        </div>

        <p style="margin: 0 0 var(--sp-8); color: var(--c-text-muted); font-size: var(--fs-md);">
            Super Admin dapat mengganti password semua akun.
            Password lama tidak perlu diketahui.
        </p>

        <form action="{{ route('account.password.update', $account->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="pv-field" style="max-width: 420px;">
                <label class="pv-label" for="password">Password Baru <span class="req">*</span></label>
                <input type="password" id="password" name="password" class="pv-input"
                       required minlength="6" autocomplete="new-password">
                <p class="form-help">Minimal 6 karakter.</p>
            </div>

            <div class="pv-field" style="max-width: 420px;">
                <label class="pv-label" for="password_confirmation">Konfirmasi Password Baru <span class="req">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="pv-input"
                       required minlength="6" autocomplete="new-password">
            </div>

            <div class="form-footer">
                <a href="{{ route('account.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Password
                </button>
            </div>
        </form>

    </div>
</div>

@endsection
