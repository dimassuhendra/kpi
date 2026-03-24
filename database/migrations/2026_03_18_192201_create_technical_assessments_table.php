<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technical_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('periode_bulan');
            $table->integer('periode_tahun');
            $table->integer('jumlah_soal')->default(30);
            $table->integer('jumlah_benar')->default(0);
            $table->string('bukti_kuis')->nullable(); // Gambar screenshot hasil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technical_assessments');
    }
};
