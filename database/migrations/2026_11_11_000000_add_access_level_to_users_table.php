<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyimpan hak akses tambahan yang tidak bisa diwakili oleh role.
 *
 * Role di aplikasi ini sudah dipakai bersama (mis. unsur pimpinan dan
 * guru biasa sama-sama ber-role "guru"), sehingga dua permintaan
 * khusus berikut butuh tanda sendiri:
 *
 *   full               -> Wakil Kepala Kesiswaan: semua fitur,
 *                         KECUALI Manajemen Akun.
 *   pelanggaran_writer -> Wakil Kepala Humas: boleh menambah
 *                         pelanggaran langsung lewat menu
 *                         "Tambah Pelanggaran".
 *
 * Kolom dibiarkan nullable: mayoritas akun tidak punya hak tambahan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('access_level')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('access_level');
        });
    }
};
