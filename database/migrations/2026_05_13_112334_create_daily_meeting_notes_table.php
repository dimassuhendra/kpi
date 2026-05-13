<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_meeting_notes', function (Blueprint $table) {
            $table->id();
            // Relasi ke DailyReport utama
            $table->foreignId('daily_report_id')
                ->constrained('daily_reports')
                ->onDelete('cascade');

            // Relasi ke User (untuk mempermudah query tanpa join ke daily_reports)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('judul_briefing'); // Contoh: Briefing Pagi 13/05/2026
            $table->longText('isi_notulen');  // Tempat poin-poin diskusi disimpan

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_meeting_notes');
    }
};
