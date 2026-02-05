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
        Schema::create('kpi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_submission_id')->constrained()->onDelete('cascade');
            $table->foreignId('kpi_variable_id')->constrained();
            $table->text('staff_value');
            $table->text('manager_correction')->nullable();
            $table->decimal('calculated_score', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_details');
    }
};
