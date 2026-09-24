<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggarans', function (Blueprint $table) {
            if (! Schema::hasColumn('pelanggarans', 'pelapor_id')) {
                $table->foreignId('pelapor_id')
                    ->nullable()
                    ->after('siswa_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('pelanggarans', 'status')) {
                $table->enum('status', ['pending', 'diverifikasi', 'ditolak'])
                    ->default('diverifikasi')
                    ->after('kategori');
            }

            if (! Schema::hasColumn('pelanggarans', 'diverifikasi_oleh')) {
                $table->foreignId('diverifikasi_oleh')
                    ->nullable()
                    ->after('status')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('pelanggarans', 'catatan_verifikasi')) {
                $table->text('catatan_verifikasi')
                    ->nullable()
                    ->after('diverifikasi_oleh');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pelanggarans', function (Blueprint $table) {
            if (Schema::hasColumn('pelanggarans', 'catatan_verifikasi')) {
                $table->dropColumn('catatan_verifikasi');
            }

            if (Schema::hasColumn('pelanggarans', 'diverifikasi_oleh')) {
                $table->dropForeign(['diverifikasi_oleh']);
                $table->dropColumn('diverifikasi_oleh');
            }

            if (Schema::hasColumn('pelanggarans', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('pelanggarans', 'pelapor_id')) {
                $table->dropForeign(['pelapor_id']);
                $table->dropColumn('pelapor_id');
            }
        });
    }
};
