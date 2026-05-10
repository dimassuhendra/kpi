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
                    LINK BUKTI</td>
            </tr>
            @foreach ($data['reports'] as $report)
                @foreach ($data['details']->where('daily_report_id', $report->id) as $detail)
                    <tr>
                        <td style="border: 1px solid #000;">
                            {{ \Carbon\Carbon::parse($report->tanggal)->format('d/m/Y') }}</td>
                        <td style="border: 1px solid #000;">{{ strtoupper($detail->kategori ?? 'UMUM') }}</td>
                        <td style="border: 1px solid #000;">{{ $detail->deskripsi_kegiatan }}</td>
                        <td style="border: 1px solid #000; text-align: center;">
                            {{ $detail->kategori == 'GPS' && $detail->value_raw > 0 ? $detail->value_raw . ' Unit' : '-' }}
                        </td>
                        <td style="border: 1px solid #000; text-align: center;">
                            @if ($detail->temuan_sendiri)
                                Temuan Sendiri
                            @elseif($detail->kategori == 'Network' && !str_contains(strtolower($detail->deskripsi_kegiatan), 'monitoring'))
                                {{ $detail->waktu_respon_menit }} Menit
                            @else
                                -
                            @endif
                        </td>
                        <td style="border: 1px solid #000; text-align: center;">
                            @if ($detail->kategori == 'Network' && !str_contains(strtolower($detail->deskripsi_kegiatan), 'monitoring'))
                                {{ $detail->is_mandiri ? 'Mandiri' : 'Eskalasi (' . $detail->pic_name . ')' }}
                            @else
                                -
                            @endif
                        </td>
                        {{-- LINK BUKTI TAC (Diubah dari Gambar ke Link) --}}
                        <td style="border: 1px solid #000; text-align: left; font-size: 9px; color: #1e40af;">
                            @if ($detail->bukti_respon_time)
                                {{ $detail->bukti_respon_time }}
                            @elseif($detail->bukti_deteksi_dini)
                                {{ $detail->bukti_deteksi_dini }}
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
                {{-- Colspan dikurangi jadi 4 agar ada sisa 1 kolom untuk Link --}}
                <td colspan="4" style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000;">
                    DESKRIPSI DETAIL</td>
                <td style="background-color: #E2E8F0; font-weight: bold; border: 1px solid #000; text-align: center;">
                    LINK BUKTI</td>
            </tr>
            @foreach ($data['reports'] as $report)
                @foreach ($data['details']->where('daily_report_id', $report->id) as $detail)
                    <tr>
                        <td style="border: 1px solid #000;">
                            {{ \Carbon\Carbon::parse($report->tanggal)->format('d/m/Y') }}</td>
                        <td style="border: 1px solid #000;">{{ strtoupper($detail->kategori ?? 'UMUM') }}</td>
                        <td colspan="4" style="border: 1px solid #000;">{{ $detail->deskripsi_kegiatan }}</td>
                        {{-- LINK BUKTI INFRA --}}
                        <td style="border: 1px solid #000; text-align: left; font-size: 9px; color: #1e40af;">
                            {{ $detail->foto_dokumentasi ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
        @endif

        <tr>
            <td colspan="7"></td>
        </tr>
    @endforeach
</table>
