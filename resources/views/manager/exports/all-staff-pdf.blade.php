<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: a5 portrait;
            margin: 10mm;
        }

        body {
            font-family: sans-serif;
            color: #334;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        /* Layout Table untuk kestabilan A5 */
        .grid-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            table-layout: fixed;
        }

        .card {
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 10px;
            vertical-align: top;
        }

        .full-card {
            background: #ecfdf5;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 12px;
            margin-bottom: 5px;
            width: 100%;
            box-sizing: border-box;
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

        .progress-bg {
            background: #e2e8f0;
            height: 8px;
            border-radius: 4px;
            margin-top: 8px;
            width: 100%;
        }

        .progress-fill {
            height: 8px;
            border-radius: 4px;
            background: #10b981;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 8px;
        }
    </style>
</head>

<body>
    @foreach ($allUserData as $data)
        <div class="header">
            <h3 style="margin: 0; text-transform: uppercase; font-size: 12px;">
                REPORT: {{ strtoupper($data['user']->divisi->nama_divisi) }}
            </h3>
            <p style="margin: 3px 0;">{{ $data['user']->nama_lengkap }} | {{ $periode }}</p>
        </div>

        @if ($data['divisi_id'] == 2)
            {{-- VIEW INFRA --}}
            <div class="full-card">
                <span class="label">Total Akumulasi Pekerjaan</span>
                <span class="value">{{ $data['total_case'] }} Task</span>
                <p style="font-size: 7px; color: #64748b; margin: 4px 0 0 0;">(CCTV, Network, GPS, Lainnya)</p>
            </div>

            <table class="grid-table">
                {{-- Membagi data infraStats menjadi baris berisi 2 kolom --}}
                @php $infraChunks = collect($data['infraStats'])->chunk(2); @endphp
                @foreach ($infraChunks as $chunk)
                    <tr>
                        @foreach ($chunk as $kategori => $count)
                            <td class="card" width="50%">
                                <span class="label">{{ $kategori }}</span>
                                <span class="value">{{ $count }}</span>
                                <div class="progress-bg">
                                    <div class="progress-fill"
                                        style="width: {{ $data['total_case'] > 0 ? ($count / $data['total_case']) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </td>
                        @endforeach
                        @if ($chunk->count() < 2)
                            <td width="50%"></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @else
            {{-- VIEW TAC --}}
            <table class="grid-table">
                <tr>
                    <td class="card" width="50%">
                        <span class="label">Total Case</span>
                        <span class="value">{{ $data['tacStats']['total_case'] }}</span>
                    </td>
                    <td class="card" width="50%">
                        <span class="label">Total Activity</span>
                        <span class="value">{{ $data['tacStats']['total_activity'] }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="card" width="50%">
                        <span class="label">Temuan Proaktif</span>
                        <span class="value">{{ $data['tacStats']['temuan_sendiri'] }}</span>
                    </td>
                    <td class="card" width="50%">
                        <span class="label">Selesai Mandiri</span>
                        <span class="value">{{ $data['tacStats']['mandiri_count'] }}</span>
                    </td>
                </tr>
            </table>

            <div class="full-card"
                style="background: #f8fafc; border-left: 4px solid {{ $data['tacStats']['avg_time'] > 15 ? '#f43f5e' : '#10b981' }}; margin-top: 5px;">
                <span class="label">Average Response Time</span>
                <span class="value">{{ $data['tacStats']['avg_time'] }} Menit</span>
                <p style="font-size: 7px; color: #64748b; margin: 3px 0 0 0;">Target: Di bawah 15 Menit</p>
            </div>
        @endif

        <div class="footer">
            Generated by System | {{ $date }}
        </div>

        {{-- Memberi pemisah halaman kecuali pada data terakhir --}}
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>
