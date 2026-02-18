<!DOCTYPE html>
<html>

<head>
    <title>Laporan Kinerja Bulanan</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
        }

        .section-title {
            background: #f4f4f4;
            padding: 8px;
            font-weight: bold;
            margin-top: 20px;
            border-left: 4px solid #2d336b;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data th,
        table.data td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        table.data th {
            background-color: #f8f9fa;
        }

        .chart-container {
            text-align: center;
            margin-top: 20px;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>LAPORAN KINERJA STAFF</h2>
        <p>Periode: {{ $start->translatedFormat('F Y') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Nama Staff</strong></td>
            <td>: {{ $user->nama_lengkap }}</td>
            <td width="15%"><strong>Divisi</strong></td>
            <td>: {{ $user->divisi->nama_divisi }}</td>
        </tr>
    </table>

    <div class="section-title">RINGKASAN ANALISA</div>
    <table class="data">
        <thead>
            <tr>
                <th>Total Kegiatan</th>
                <th>Total Case</th>
                <th>Rata-rata Respon (Menit)</th>
                <th>Penyelesaian Mandiri</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $stats['total_kegiatan'] }}</td>
                <td>{{ $stats['total_case'] }}</td>
                <td>{{ $stats['avg_time'] }}</td>
                <td>{{ $stats['mandiri'] }}</td>
            </tr>
        </tbody>
    </table>

    @if ($chartImage)
        <div class="section-title">GRAFIK PERFORMA</div>
        <div class="chart-container">
            <img src="{{ $chartImage }}" style="width: 100%; max-height: 300px;">
        </div>
    @endif

    <div class="section-title">DETAIL AKTIVITAS</div>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori/Tipe</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($details->take(15) as $item)
                <tr>
                    <td>{{ Carbon\Carbon::parse($item->dailyReport->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($item->tipe_kegiatan) }} ({{ $item->kategori ?? '-' }})</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
        <br><br><br>
        <p>( __________________________ )<br>Manager Operasional</p>
    </div>

</body>

</html>
