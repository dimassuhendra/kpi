<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom integrasi Google Sheets di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('spreadsheet_id')->nullable()->after('password')->comment('ID File Google Sheets utama');
            $table->string('sheet_name')->nullable()->after('spreadsheet_id')->comment('Nama Tab/Sheet untuk user ini');
        });

        // 2. Tambah kolom bobot di tabel variabel_kpis
        Schema::table('variabel_kpis', function (Blueprint $table) {
            $table->integer('bobot')->default(0)->after('nama_variabel')->comment('Bobot persentase KPI');
        });

        // 3. Tambah metrik penilaian di tabel kegiatan_detail
        Schema::table('kegiatan_detail', function (Blueprint $table) {
            $table->integer('waktu_respon_menit')->nullable()->after('value_raw');
            $table->boolean('is_mandiri')->default(false)->after('waktu_respon_menit');
            $table->boolean('is_temuan_sendiri')->default(false)->after('is_mandiri');
            $table->text('dokumentasi_kegiatan')->nullable()->after('is_temuan_sendiri');
            $table->string('bukti_foto')->nullable()->after('dokumentasi_kegiatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['spreadsheet_id', 'sheet_name']);
        });

        Schema::table('variabel_kpis', function (Blueprint $table) {
            $table->dropColumn('bobot');
        });

        Schema::table('kegiatan_detail', function (Blueprint $table) {
            $table->dropColumn([
                'waktu_respon_menit',
                'is_mandiri',
                'is_temuan_sendiri',
                'dokumentasi_kegiatan',
                'bukti_foto'
            ]);
        });
    }
};
