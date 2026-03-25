<table>
    @foreach ($dataExport as $data)
        <tr>
            <td colspan="7" style="background-color: #334155; color: #FFFFFF; font-weight: bold; font-size: 14px;">
                NAMA STAFF: {{ strtoupper($data['user']->nama_lengkap) }} | DIVISI:
                {{ strtoupper($data['user']->divisi->nama_divisi ?? 'INFRA') }}
            </td>
        </tr>

        @if ($data['user']->divisi_id == 1)
            {{-- HEADER TABEL TAC --}}
            <tr>
                <td style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000;">TANGGAL</td>
                <td style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000;">KATEGORI</td>
                <td style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000;">DESKRIPSI (ALL
                    ACTIVITY)</td>
                <td style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000; text-align: center;">
                    KENDARAAN</td>
                <td style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000; text-align: center;">
                    WAKTU RESPON</td>
                <td style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000; text-align: center;">
                    PENYELESAIAN</td>
                <td style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000; text-align: center;">
                    GAMBAR BUKTI</td>
            </tr>
            @foreach ($data['reports'] as $report)
                @foreach ($data['details']->where('daily_report_id', $report->id) as $detail)
                    <tr>
                        <td style="border: 1px solid #000;">
                            {{ \Carbon\Carbon::parse($report->tanggal)->format('d/m/Y') }}</td>
                        <td style="border: 1px solid #000;">{{ strtoupper($detail->kategori ?? 'UMUM') }}</td>
                        <td style="border: 1px solid #000;">{{ $detail->deskripsi_kegiatan }}</td>

                        {{-- Kendaraan --}}
                        <td style="border: 1px solid #000; text-align: center;">
                            {{ $detail->kategori == 'GPS' && $detail->value_raw > 0 ? $detail->value_raw . ' Unit' : '-' }}
                        </td>

                        {{-- Waktu Respon --}}
                        <td style="border: 1px solid #000; text-align: center;">
                            @if ($detail->temuan_sendiri)
                                Temuan Sendiri
                            @elseif($detail->kategori == 'Network' && !str_contains(strtolower($detail->deskripsi_kegiatan), 'monitoring'))
                                {{ $detail->waktu_respon_menit }} Menit
                            @else
                                -
                            @endif
                        </td>

                        {{-- Penyelesaian --}}
                        <td style="border: 1px solid #000; text-align: center;">
                            @if ($detail->kategori == 'Network' && !str_contains(strtolower($detail->deskripsi_kegiatan), 'monitoring'))
                                {{ $detail->is_mandiri ? 'Mandiri' : 'Eskalasi (' . $detail->pic_name . ')' }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- Gambar --}}
                        <td style="border: 1px solid #000; text-align: center; height: 80px;">
                            @if ($detail->bukti_respon_time)
                                <img src="{{ public_path('storage/' . $detail->bukti_respon_time) }}" height="70" />
                            @elseif($detail->bukti_deteksi_dini)
                                <img src="{{ public_path('storage/' . $detail->bukti_deteksi_dini) }}" height="70" />
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        @else
            {{-- HEADER TABEL INFRA --}}
            <tr>
                <td style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000;">TANGGAL</td>
                <td style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000;">KATEGORI</td>
                <td colspan="5" style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000;">
                    DESKRIPSI DETAIL</td>
            </tr>
            @foreach ($data['reports'] as $report)
                @foreach ($data['details']->where('daily_report_id', $report->id) as $detail)
                    <tr>
                        <td style="border: 1px solid #000;">
                            {{ \Carbon\Carbon::parse($report->tanggal)->format('d/m/Y') }}</td>
                        <td style="border: 1px solid #000;">{{ strtoupper($detail->kategori ?? 'UMUM') }}</td>
                        <td colspan="5" style="border: 1px solid #000;">{{ $detail->deskripsi_kegiatan }}</td>
                    </tr>
                @endforeach
            @endforeach
        @endif

        <tr>
            <td colspan="7"></td>
        </tr>
    @endforeach
</table>
