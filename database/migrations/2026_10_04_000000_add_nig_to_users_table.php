<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            /*
             * NIG disimpan sebagai string, bukan unsignedBigInteger.
             * Sebagian besar NIG diawali nol (mis. 0170194016); kalau
             * dikonversi ke angka, nolnya hilang dan NIG yang diketik
             * guru tidak akan cocok dengan yang tersimpan.
             *
             * Nullable karena belum semua akun punya NIG: akun walas
             * per-kelas dan admin masih memakai email.
             */
            $table->string('nig', 20)->nullable()->unique()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nig']);

            $table->dropColumn('nig');
        });
    }
};
