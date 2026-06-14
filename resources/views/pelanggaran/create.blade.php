@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">Tambah Pelanggaran</h2>

    <form action="{{ route('pelanggaran.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <!-- Cari Siswa -->
        <div class="mb-3">

            <label class="form-label">
                Cari Siswa
            </label>

            <input
                type="text"
                id="searchSiswa"
                class="form-control"
                placeholder="Ketik nama atau NISN siswa">

        </div>

        <!-- Hasil Pencarian -->
        <div id="hasilSiswa"></div>

        <!-- siswa_id yang akan disimpan -->
        <input
            type="hidden"
            name="siswa_id"
            id="siswa_id">

        <!-- Detail siswa -->
        <div class="card mb-3">

            <div class="card-body">

                <div id="detailSiswa">

                    Belum memilih siswa

                </div>

            </div>

        </div>

        <!-- Tanggal -->
        <div class="mb-3">

            <label class="form-label">
                Tanggal
            </label>

            <input
                type="date"
                name="tanggal"
                class="form-control"
                required>

        </div>

        <!-- Jenis Pelanggaran -->
        <div class="mb-3">

            <label class="form-label">
                Jenis Pelanggaran
            </label>

            <input
                type="text"
                name="jenis_pelanggaran"
                class="form-control"
                placeholder="Contoh: Terlambat, Tidak Memakai Dasi"
                required>

        </div>

        <!-- Poin -->
        <div class="mb-3">

            <label class="form-label">
                Poin keterlambatan
            </label>

            <input
                type="number"
                name="poin"
                class="form-control"
                placeholder="Masukkan poin"
                required>

        </div>

        <!-- Keterangan -->
        <div class="mb-3">

            <label class="form-label">
                Keterangan
            </label>

            <textarea
                name="keterangan"
                class="form-control"
                rows="4"
                placeholder="Contoh: Terlambat masuk kelas 20 menit"></textarea>

        </div>

        <!-- Foto -->
        <div class="mb-3">

            <label class="form-label">
                Foto Bukti
            </label>

            <input
                type="file"
                name="foto_bukti"
                class="form-control">

        </div>

        <button
            type="submit"
            class="btn btn-primary">

            Simpan

        </button>

    </form>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>

$('#searchSiswa').keyup(function(){

    let q = $(this).val();

    if(q.length < 2){
        $('#hasilSiswa').html('');
        return;
    }

    $.get('/search-siswa', { q:q }, function(data){

        let html = '';

        data.forEach(function(item){

            html += `
            <div class="card mb-2 pilihSiswa"
                 style="cursor:pointer"
                 data-id="${item.id}"
                 data-nama="${item.nama}"
                 data-nisn="${item.nisn}"
                 data-kelas="${item.kelas}">

                <div class="card-body p-2">

                    <strong>${item.nama}</strong><br>

                    NISN : ${item.nisn}<br>

                    Kelas : ${item.kelas}

                </div>

            </div>
            `;

        });

        $('#hasilSiswa').html(html);

    });

});

$(document).on('click', '.pilihSiswa', function(){

    $('#siswa_id').val($(this).data('id'));

    $('#searchSiswa').val($(this).data('nama'));

    $('#hasilSiswa').html('');

    $('#detailSiswa').html(`
        <strong>Nama:</strong> ${$(this).data('nama')}<br>
        <strong>NISN:</strong> ${$(this).data('nisn')}<br>
        <strong>Kelas:</strong> ${$(this).data('kelas')}
    `);

});

</script>

@endsection
