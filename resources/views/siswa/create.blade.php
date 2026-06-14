@extends('layouts.app')

@section('content')

<form action="{{ route('siswa.store') }}"
      method="POST">

    @csrf

    <input type="text"
           name="nis"
           placeholder="NIS"
           class="form-control mb-2">

    <input type="text"
           name="nama"
           placeholder="Nama"
           class="form-control mb-2">

    <input type="text"
           name="kelas"
           placeholder="Kelas"
           class="form-control mb-2">

    <button class="btn btn-primary">
        Simpan
    </button>

</form>

@endsection
