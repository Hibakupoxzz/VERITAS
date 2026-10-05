<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyimpan jabatan resmi tenaga pendidik pada tabel users.
 *
 * Kenapa perlu: role aplikasi tidak bisa membedakan orangnya, hanya
 * kewenangannya. Kepala Sekolah dan lima Wakil Kepala Sekolah
 * semuanya punya role "guru", sehingga label role-nya muncul sebagai
 * "Guru Khusus / Piket" — yang jelas salah untuk mereka.
 *
 * Jabatan resmi diambil apa adanya dari daftar sekolah, sehingga
 * "Kepala Sekolah" atau "Waka Kurikulum" bisa ditampilkan apa adanya
 * tanpa daftar NIG tambahan yang harus dijaga sinkron.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jabatan')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('jabatan');
        });
    }
};