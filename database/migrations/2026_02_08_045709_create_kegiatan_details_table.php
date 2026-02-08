<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kegiatan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->foreignId('variabel_kpi_id')->constrained('variabel_kpi')->onDelete('cascade');
            $table->text('deskripsi_kegiatan');
            $table->string('value_raw');

            $table->boolean('temuan_sendiri')->default(false);
            $table->boolean('is_mandiri')->default(true);
            $table->string('pic_name')->nullable();

            $table->decimal('nilai_akhir', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_details');
    }
};
