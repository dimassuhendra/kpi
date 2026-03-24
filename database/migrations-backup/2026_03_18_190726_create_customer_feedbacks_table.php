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
            // Menghubungkan feedback langsung dengan case spesifik yang ditangani
            $table->foreignId('kegiatan_detail_id')->constrained('kegiatan_detail')->onDelete('cascade');
            $table->decimal('rating', 3, 1)->default(0)->comment('Rating 1.0 - 5.0');
            $table->text('ulasan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_feedbacks');
    }
};
