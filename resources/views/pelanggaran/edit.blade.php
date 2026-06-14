@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Edit Pelanggaran</h2>

    <form action="{{ route('pelanggaran.update', $pelanggaran->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Siswa</label>

            <select name="siswa_id" class="form-control">

                @foreach($siswas as $siswa)

                    <option
                        value="{{ $siswa->id }}"
                        {{ $pelanggaran->siswa_id == $siswa->id ? 'selected' : '' }}>

                        {{ $siswa->nama }} - {{ $siswa->kelas }}

                    </option>

                @endforeach

            </select>
        </div>

        <div class="mb-3">
            <label>Tanggal</label>

            <input type="date"
                   name="tanggal"
                   class="form-control"
                   value="{{ $pelanggaran->tanggal }}">
        </div>

        <div class="mb-3">
            <label>Jenis Pelanggaran</label>

            <input type="text"
                   name="jenis_pelanggaran"
                   class="form-control"
                   value="{{ $pelanggaran->jenis_pelanggaran }}">
        </div>

        <div class="mb-3">
            <label>Poin</label>

            <input type="number"
                   name="poin"
                   class="form-control"
                   value="{{ $pelanggaran->poin }}">
        </div>

        <div class="mb-3">
            <label>Catatan</label>

            <textarea name="catatan"
                      class="form-control"
                      rows="4">{{ $pelanggaran->catatan }}</textarea>
        </div>

        <div class="mb-3">

            <label>Foto Bukti Saat Ini</label>

            <br>

            @if($pelanggaran->foto_bukti)

                <img
                    src="{{ asset('storage/'.$pelanggaran->foto_bukti) }}"
                    width="150"
                    class="mb-2">

            @else

                <p>Tidak ada foto.</p>

            @endif

        </div>

        <div class="mb-3">

            <label>Ganti Foto Bukti</label>

            <input
                type="file"
                name="foto_bukti"
                class="form-control">

        </div>

        <button type="submit"
                class="btn btn-primary">

            Update

        </button>

        <a href="{{ route('pelanggaran.index') }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </form>

</div>

@endsection
