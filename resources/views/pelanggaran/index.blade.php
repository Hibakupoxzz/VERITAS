@extends('layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Data Pelanggaran Siswa</h2>

        <a href="{{ route('pelanggaran.create') }}"
           class="btn btn-success">
            + Tambah Pelanggaran
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered table-striped">

        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Tanggal</th>
                <th>Pelanggaran</th>
                <th>Poin</th>
                <th>Catatan</th>
                <th>Bukti</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

        @forelse($pelanggarans as $item)

            <tr>
                <td>{{ $loop->iteration }}</td>

                <td>{{ $item->siswa->nama ?? '-' }}</td>

                <td>{{ $item->siswa->kelas ?? '-' }}</td>

                <td>{{ $item->tanggal }}</td>

                <td>{{ $item->jenis_pelanggaran }}</td>

                <td>{{ $item->poin }}</td>

                <td>{{ $item->catatan }}</td>

                <td>
                    @if($item->foto_bukti)
                        <img
                            src="{{ asset('storage/' . $item->foto_bukti) }}"
                            width="100">
                    @else
                        Tidak ada foto
                    @endif
                </td>

                <td>

                    <a href="{{ route('pelanggaran.edit', $item->id) }}"
                       class="btn btn-warning btn-sm">
                        Edit
                    </a>

                    <form
                        action="{{ route('pelanggaran.destroy', $item->id) }}"
                        method="POST"
                        style="display:inline-block">

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus data?')">
                            Hapus
                        </button>

                    </form>

                </td>
            </tr>

        @empty

            <tr>
                <td colspan="9" class="text-center">
                    Belum ada data pelanggaran.
                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

</div>

@endsection
