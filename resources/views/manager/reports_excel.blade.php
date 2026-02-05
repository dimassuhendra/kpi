<!DOCTYPE html>
<html>

<head>
    <title>KPI Report - {{ $month }}/{{ $year }}</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            text-transform: uppercase;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .score {
            font-weight: bold;
            color: #6366f1;
        }
    </style>
</head>

<body onload="{{ isset($isPdf) ? 'window.print()' : '' }}">
    <div class="header">
        <h2>LAPORAN KPI BULANAN</h2>
        <p>Periode: {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</p>
        <p>Divisi: {{ Auth::user()->division->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Staff</th>
                <th>Total Laporan</th>
                <th>Tepat Waktu</th>
                <th>Terlambat</th>
                <th>Rata-rata Skor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['total_reports'] }} Laporan</td>
                <td>{{ $row['on_time_count'] }}</td>
                <td>{{ $row['late_count'] }}</td>
                <td class="score">{{ number_format($row['avg_score'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right;">
        <p>Dicetak pada: {{ date('d M Y H:i') }}</p>
        <br><br><br>
        <p><b>{{ Auth::user()->name }}</b></p>
        <p>Manager {{ Auth::user()->division->name }}</p>
    </div>
</body>

</html>