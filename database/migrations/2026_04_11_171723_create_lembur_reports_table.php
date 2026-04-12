<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lembur_reports', function (Blueprint $table) {
            $table->id();
            // Berelasi dengan laporan harian agar terikat dengan shift hari tersebut
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');

            // Menggunakan datetime karena jadwal shift bisa lintas hari (malam ke pagi)
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');

            $table->text('detail_pekerjaan');
            $table->string('foto_dokumentasi')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lembur_reports');
    }
};
