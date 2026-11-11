@extends('layouts.app')

@section('title', 'Tambah Akun')
@section('page_title', 'Tambah Akun')

@section('styles')
@include('partials.ui')
@endsection

@section('content')

<div class="pv-page-header">
    <div class="pv-page-icon">
        <i class="fa-solid fa-user-plus"></i>
    </div>
    <div class="pv-page-headtext">
        <h1 class="pv-page-title">Tambah Akun</h1>
        <p class="pv-page-sub">Buat akun pengguna baru untuk aplikasi.</p>
    </div>
</div>

@include('partials.account-form', [
    'account' => null,
    'action' => route('account.store'),
    'method' => null,
    'submitLabel' => 'Simpan Akun',
])

@endsection
