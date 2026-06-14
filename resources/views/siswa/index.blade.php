@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Siswa</h2>

    <a href="{{ route('siswa.create') }}"
       class="btn btn-success">
        + Tambah Siswa
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form method="GET"
      action="{{ route('siswa.index') }}"
      class="mb-3">

    <div class="input-group">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Cari nama, NIS, atau kelas..."
               value="{{ request('search') }}">

        <button class="btn btn-primary">
            Cari
        </button>
    </div>

</form>

<table class="table table-bordered table-striped">

    <thead>
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th width="200">Aksi</th>
        </tr>
    </thead>

    <tbody>

    @forelse($siswas as $siswa)

        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $siswa->nis }}</td>
            <td>{{ $siswa->nama }}</td>
            <td>{{ $siswa->kelas }}</td>

            <td>
                <a href="{{ route('siswa.edit', $siswa->id) }}"
                   class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="{{ route('siswa.destroy', $siswa->id) }}"
                      method="POST"
                      style="display:inline-block">

                    @csrf
                    @method('DELETE')

                    <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin hapus data?')">
                        Hapus
                    </button>

                </form>
            </td>
        </tr>

    @empty

        <tr>
            <td colspan="5" class="text-center">
                Data siswa belum ada
            </td>
        </tr>

    @endforelse

    </tbody>

</table>

@endsection
