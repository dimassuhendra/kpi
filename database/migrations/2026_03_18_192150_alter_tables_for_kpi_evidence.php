<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pengecekan tabel kegiatan_detail
        Schema::table('kegiatan_detail', function (Blueprint $table) {
            if (!Schema::hasColumn('kegiatan_detail', 'nomor_tiket')) {
                $table->string('nomor_tiket')->nullable()->after('value_raw');
            }
            if (!Schema::hasColumn('kegiatan_detail', 'waktu_respon_menit')) {
                $table->integer('waktu_respon_menit')->nullable()->after('nomor_tiket');
            }
            if (!Schema::hasColumn('kegiatan_detail', 'bukti_respon_time')) {
                $table->string('bukti_respon_time')->nullable()->after('is_mandiri');
            }
            if (!Schema::hasColumn('kegiatan_detail', 'bukti_deteksi_dini')) {
                $table->string('bukti_deteksi_dini')->nullable()->after('bukti_respon_time');
            }
        });

        // 2. Pengecekan tabel daily_reports
        Schema::table('daily_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_reports', 'shift_id')) {
                $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete()->after('user_id');
            }
            if (!Schema::hasColumn('daily_reports', 'is_gps_ontime')) {
                $table->boolean('is_gps_ontime')->default(false)->after('tanggal');
            }
            if (!Schema::hasColumn('daily_reports', 'is_dashboard_ontime')) {
                $table->boolean('is_dashboard_ontime')->default(false)->after('is_gps_ontime');
            }
            if (!Schema::hasColumn('daily_reports', 'bukti_report_gps')) {
                $table->string('bukti_report_gps')->nullable()->after('is_dashboard_ontime');
            }
            if (!Schema::hasColumn('daily_reports', 'bukti_report_dashboard')) {
                $table->string('bukti_report_dashboard')->nullable()->after('bukti_report_gps');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kegiatan_detail', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_tiket',
                'waktu_respon_menit',
                'bukti_respon_time',
                'bukti_deteksi_dini'
            ]);
        });

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn([
                'shift_id',
                'is_gps_ontime',
                'is_dashboard_ontime',
                'bukti_report_gps',
                'bukti_report_dashboard'
            ]);
        });
    }
};
