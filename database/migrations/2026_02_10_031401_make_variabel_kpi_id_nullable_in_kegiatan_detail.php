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
            $table->bigInteger('variabel_kpi_id')->unsigned()->nullable()->change();

            $table->string('value_raw')->nullable()->change();
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
