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
        Schema::create('kpi_case_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_submission_id')->constrained()->onDelete('cascade');
            $table->string('ticket_number');
            $table->integer('response_time_minutes');
            $table->boolean('is_problem_detected_by_staff');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_case_logs');
    }
};
