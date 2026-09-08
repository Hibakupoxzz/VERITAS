<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pelanggarans', 'aturan_pelanggaran_id')) {
            Schema::table('pelanggarans', function (Blueprint $table) {
                $table->unsignedBigInteger('aturan_pelanggaran_id')
                    ->nullable()
                    ->after('siswa_id');
            });
        }

        Schema::table('pelanggarans', function (Blueprint $table) {
            $table->foreign('aturan_pelanggaran_id')
                ->references('id')
                ->on('aturan_pelanggarans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('pelanggarans', 'aturan_pelanggaran_id')) {
            Schema::table('pelanggarans', function (Blueprint $table) {
                $table->dropForeign(['aturan_pelanggaran_id']);
                $table->dropColumn('aturan_pelanggaran_id');
            });
        }
    }
};
