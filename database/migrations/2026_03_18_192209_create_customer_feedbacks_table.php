<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nomor_tiket')->nullable(); // Opsional, jika survey terikat pada tiket tertentu
            $table->date('tanggal_survey');
            $table->string('nama_pelanggan');
            $table->decimal('rating', 3, 1)->default(0); // Misal: 4.5 atau 5.0
            $table->string('bukti_survey')->nullable(); // Gambar bukti survey/chat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_feedbacks');
    }
};
