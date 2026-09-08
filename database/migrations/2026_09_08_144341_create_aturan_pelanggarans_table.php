<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aturan_pelanggarans', function (Blueprint $table) {
            $table->id();

            $table->string('kode', 20)->unique();

            $table->enum('kategori', [
                'Ringan',
                'Sedang',
                'Berat',
                'Luar Biasa',
            ])->index();

            $table->string('subkategori')->nullable();

            $table->text('nama');

            $table->unsignedTinyInteger('poin');

            $table->unsignedTinyInteger('tahap_maksimal')->default(4);

            $table->text('sanksi_i')->nullable();
            $table->text('sanksi_ii')->nullable();
            $table->text('sanksi_iii')->nullable();
            $table->text('sanksi_iv')->nullable();

            $table->boolean('langsung_kembali')->default(false);

            $table->boolean('aktif')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aturan_pelanggarans');
    }
};
