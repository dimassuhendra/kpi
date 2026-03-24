<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('kegiatan_detail', function (Blueprint $table) {
            $table->enum('tipe_kegiatan', ['case', 'activity'])->default('case')->after('variabel_kpi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan_detail', function (Blueprint $table) {
            //
        });
    }
};
