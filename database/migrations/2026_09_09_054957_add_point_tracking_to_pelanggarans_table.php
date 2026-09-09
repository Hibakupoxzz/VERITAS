<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggarans', function (Blueprint $table) {
            if (!Schema::hasColumn('pelanggarans', 'kategori')) {
                $table->string('kategori', 50)
                    ->nullable()
                    ->after('jenis_pelanggaran');
            }

            if (!Schema::hasColumn('pelanggarans', 'poin_sebelum')) {
                $table->unsignedInteger('poin_sebelum')
                    ->nullable()
                    ->after('poin');
            }

            if (!Schema::hasColumn('pelanggarans', 'poin_sesudah')) {
                $table->unsignedInteger('poin_sesudah')
                    ->nullable()
                    ->after('poin_sebelum');
            }

            if (!Schema::hasColumn('pelanggarans', 'sanksi_tahap')) {
                $table->unsignedTinyInteger('sanksi_tahap')
                    ->nullable()
                    ->after('poin_sesudah');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pelanggarans', function (Blueprint $table) {
            if (Schema::hasColumn('pelanggarans', 'sanksi_tahap')) {
                $table->dropColumn('sanksi_tahap');
            }

            if (Schema::hasColumn('pelanggarans', 'poin_sesudah')) {
                $table->dropColumn('poin_sesudah');
            }

            if (Schema::hasColumn('pelanggarans', 'poin_sebelum')) {
                $table->dropColumn('poin_sebelum');
            }

            if (Schema::hasColumn('pelanggarans', 'kategori')) {
                $table->dropColumn('kategori');
            }
        });
    }
};
