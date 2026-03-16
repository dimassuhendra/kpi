<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: a4 portrait;
            /* Diubah ke A4 agar tabel rincian muat banyak data */
            margin: 15mm;
        }

        body {
            font-family: sans-serif;
            color: #334;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .grid-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin-bottom: 15px;
        }

        .card {
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 10px;
            vertical-align: top;
        }

        .full-card {
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 12px;
            margin-bottom: 10px;
        }

        .label {
            font-size: 8px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
        }

        .value {
            font-size: 16px;
            font-weight: bold;
            display: block;
            margin-top: 3px;
        }

        /* Styling Tabel Rincian */
        .detail-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e293b;
            margin-top: 20px;
            margin-bottom: 8px;
            border-left: 3px solid #10b981;
            padding-left: 8px;
        }

        .table-report {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .table-report th {
            background: #f1f5f9;
            text-align: left;
            padding: 6px;
            font-size: 8px;
            text-transform: uppercase;
            border: 1px solid #e2e8f0;
        }

        .table-report td {
            padding: 6px;
            font-size: 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .badge {
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="header" style="margin-bottom: 10px; border-bottom: 3px solid #10b981; padding-bottom: 15px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 15%; vertical-align: middle;">
                    <img src="{{ public_path('img/logo-new.png') }}" alt="Logo" style="width: 100%; max-width: 80px;">
                </td>

                <td style="width: 40%; vertical-align: middle; padding-left: 15px; border-left: 1px solid #e2e8f0;">
                    <strong style="color: #1e293b; font-size: 10px; text-transform: uppercase;">MyBolo</strong><br>
                    <span style="font-size: 8px; color: #64748b; line-height: 1.2;">
                        Jl. Ikan Kakap No. 64-66, Teluk Betung<br>
                        Kota Bandar Lampung<br>
                        Telp: +62721-5602-633<br>
                        Email: support@mybolo.id
                    </span>
                </td>

                <td style="width: 45%; text-align: right; vertical-align: middle;">
                    <h3
                        style="margin: 0; text-transform: uppercase; font-size: 11px; color: #1e293b; letter-spacing: 1px;">
                        Laporan Bulanan Performa Staff
                    </h3>
                    <p style="margin: 2px 0 0 0; font-size: 10px; color: #10b981; font-weight: bold;">
                        {{ strtoupper($user->nama_lengkap) }}
                    </p>
                    <p style="margin: 1px 0 0 0; font-size: 8px; color: #64748b;">
                        {{ $user->divisi->nama_divisi }} | {{ $periode }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    @if ($divisi_id == 2)
        {{-- ========================================== --}}
        {{-- VIEW INFRA                                 --}}
        {{-- ========================================== --}}
        <div class="full-card" style="background: #ecfdf5;">
            <span class="label">Total Akumulasi Task</span>
            <span class="value">{{ $total_case }} Task</span>
        </div>

        <table class="grid-table">
            @php $chunks = collect($infraStats)->chunk(2); @endphp
            @foreach ($chunks as $chunk)
                <tr>
                    @foreach ($chunk as $kategori => $count)
                        <td class="card" width="50%">
                            <span class="label">{{ $kategori }}</span>
                            <span class="value">{{ $count }}</span>
                        </td>
                    @endforeach
                    @if ($chunk->count() < 2)
                        <td width="50%"></td>
                    @endif
                </tr>
            @endforeach
        </table>
    @else
        {{-- ========================================== --}}
        {{-- VIEW TAC (Network & GPS)                   --}}
        {{-- ========================================== --}}

        {{-- Row 1: Network Stats --}}
        <div class="detail-title">Network Metrics</div>
        <table class="grid-table">
            <tr>
                <td class="card" width="33%">
                    <span class="label">Network Cases</span>
                    <span class="value">{{ $tacStats['net_count'] }}</span>
                </td>
                <td class="card" width="33%">
                    <span class="label">Temuan Mandiri</span>
                    <span class="value">{{ $tacStats['inisiatif_count'] }}</span>
                </td>
                <td class="card" width="34%">
                    <span class="label">Penyelesaian Mandiri</span>
                    <span class="value">{{ $tacStats['mandiri_count'] }}</span>
                </td>
            </tr>
        </table>

        <div class="full-card"
            style="border-left: 4px solid {{ $tacStats['avg_time'] > 15 ? '#f43f5e' : '#10b981' }};">
            <span class="label">Avg Response Time</span>
            <span class="value">{{ $tacStats['avg_time'] }} Menit</span>
            <p style="font-size: 7px; color: #64748b; margin-top: 3px;">Target: < 15 Menit</p>
        </div>

        {{-- Row 2: GPS Stats --}}
        <div class="detail-title">GPS Analytics</div>
        <div class="full-card" style="background: #f0f9ff; border: 1px solid #bae6fd;">
            <span class="label" style="color: #0369a1;">Total Kendaraan dalam Reporting</span>
            <span class="value" style="color: #0c4a6e;">{{ $tacStats['gps_count'] }} <small
                    style="font-size: 10px;">Unit</small></span>
        </div>
    @endif

    {{-- ========================================== --}}
    {{-- TABEL RINCIAN LAPORAN (SEMUA DIVISI)       --}}
    {{-- ========================================== --}}
    <div class="detail-title">Rincian Laporan Harian</div>
    <table class="table-report">
        <thead>
            <tr style="background: #f1f5f9;">
                <th width="15%" style="text-align: center; padding: 6px; border: 1px solid #e2e8f0;">Tanggal</th>
                <th width="10%" style="text-align: center; padding: 6px; border: 1px solid #e2e8f0;">Tipe</th>
                <th width="10%" style="text-align: center; padding: 6px; border: 1px solid #e2e8f0;">Kategori</th>
                <th style="text-align: center; padding: 6px; border: 1px solid #e2e8f0;">Deskripsi Pekerjaan</th>
                @if ($user->divisi_id != 2)
                    <th width="15%" style="text-align: center; padding: 6px; border: 1px solid #e2e8f0;">Keterangan
                    </th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $item)
                <tr>
                    <td style="text-align: center;">
                        {{ \Carbon\Carbon::parse($item->dailyReport->tanggal)->locale('id')->translatedFormat('l, d/m/Y') }}
                    </td>
                    <td style="text-align: center;">
                        <span class="badge"
                            style="background: {{ $item->tipe_kegiatan == 'case' ? '#dcfce7' : '#fef9c3' }};">
                            {{ $item->tipe_kegiatan }}
                        </span>
                    </td>
                    <td style="text-align: center;">{{ $item->kategori ?? '-' }}</td>
                    <td>{{ $item->deskripsi_kegiatan }}</td>

                    {{-- Logika Khusus Divisi TAC (Bukan Divisi 2) --}}
                    @if ($user->divisi_id != 2)
                        <td style="text-align: center; font-weight: bold;">
                            @if ($item->tipe_kegiatan == 'activity')
                                {{-- Jika tipe activity -> Tampilkan strip --}}
                                -
                            @elseif($item->kategori == 'Network')
                                {{-- Jika Network: Cek jika 0 tampilkan 'Temuan Sendiri', jika > 0 tambah 'Menit' --}}
                                @if ($item->value_raw == 0 || $item->value_raw == null)
                                    <span style="font-size: 7px; color: #10b981;">Temuan Sendiri</span>
                                @else
                                    {{ $item->value_raw }} <span
                                        style="font-size: 7px; font-weight: normal;">Menit</span>
                                @endif
                            @elseif($item->kategori == 'GPS')
                                {{-- Jika GPS: Tambah 'Kendaraan' --}}
                                {{ $item->value_raw ?? '0' }} <span
                                    style="font-size: 7px; font-weight: normal;">Kendaraan</span>
                            @else
                                {{ $item->value_raw ?? '-' }}
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $user->divisi_id == 2 ? '4' : '5' }}" style="text-align: center; color: #94a3b8;">
                        Tidak ada data laporan pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div
        style="margin-top: 30px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 8px;">
        Dokumen ini dibuat otomatis oleh sistem MyBolo KPI pada {{ $date }}.
    </div>
</body>

</html>
