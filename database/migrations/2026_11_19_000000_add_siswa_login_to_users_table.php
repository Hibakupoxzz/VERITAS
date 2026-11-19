<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Akun siswa memakai NIS (diambil dari kolom nisn data siswa lama)
     * sebagai identitas login. Kolom siswa_id menghubungkan akun tersebut
     * ke data siswa supaya nama/NIS ikut tersinkron saat data diubah.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nis')->nullable()->unique()->after('nig');
            $table->foreignId('siswa_id')
                ->nullable()
                ->unique()
                ->after('nis')
                ->constrained('siswas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('siswa_id');
            $table->dropUnique(['nis']);
            $table->dropColumn('nis');
        });
    }
};
