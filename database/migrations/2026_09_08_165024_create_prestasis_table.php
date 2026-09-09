<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnDelete();

            $table->date('tanggal');

            $table->string('jenis_prestasi');

            $table->string('tingkat')->nullable();

            $table->unsignedInteger('poin')->default(0);

            $table->unsignedInteger('poin_sebelum')->default(100);

            $table->unsignedInteger('poin_sesudah')->default(100);

            $table->text('keterangan')->nullable();

            $table->string('bukti')->nullable();

            $table->timestamps();

            $table->index(['siswa_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasis');
    }
};
