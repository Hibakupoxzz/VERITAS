@extends('layouts.app')

@section('title', 'Edit Akun')
@section('page_title', 'Edit Akun')

@section('styles')
@include('partials.ui')
@endsection

@section('content')

<div class="pv-page-header">
    <div class="pv-page-icon">
        <i class="fa-solid fa-user-pen"></i>
    </div>
    <div class="pv-page-headtext">
        <h1 class="pv-page-title">Edit Akun</h1>
        <p class="pv-page-sub">{{ $account->name }}</p>
    </div>
</div>

@include('partials.account-form', [
    'account' => $account,
    'action' => route('account.update', $account->id),
    'method' => 'PUT',
    'submitLabel' => 'Simpan Perubahan',
])

@endsection
