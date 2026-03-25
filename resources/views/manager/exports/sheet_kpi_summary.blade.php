<table>
    {{-- Header Tetap Sama --}}
    <tr>
        <td colspan="9" style="font-size: 16px; font-weight: bold; text-align: center; background-color: #f8fafc;">
            REKAPITULASI PENILAIAN KPI STAFF</td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: center;">Periode: {{ $periode }}</td>
    </tr>
    <tr>
        <td colspan="9"></td>
    </tr>

    <tr style="height: 30px;">
        <th
            style="background-color: #1E293B; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000;">
            NAMA STAFF</th>
        <th
            style="background-color: #1E293B; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000;">
            DIVISI</th>
        <th
            style="background-color: #059669; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000;">
            RESPON TIME (15%)</th>
        <th
            style="background-color: #059669; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000;">
            MANDIRI (15%)</th>
        <th
            style="background-color: #059669; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000;">
            TEMUAN (15%)</th>
        <th
            style="background-color: #059669; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000;">
            REPORT ONTIME (10%)</th>
        <th
            style="background-color: #059669; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000;">
            NILAI KUIS (25%)</th>
        <th
            style="background-color: #059669; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000;">
            RATING (20%)</th>
        <th
            style="background-color: #DC2626; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #000;">
            TOTAL KPI (100%)</th>
    </tr>

    @foreach ($dataKpi as $d)
        <tr style="height: 60px;"> {{-- Beri tinggi lebih agar tidak berumpukan --}}
            <td style="border: 1px solid #000; vertical-align: center;">{{ $d['user']->nama_lengkap }}</td>
            <td style="border: 1px solid #000; text-align: center; vertical-align: center;">
                {{ $d['user']->divisi->nama_divisi ?? 'INFRA' }}</td>

            @if ($d['user']->divisi_id == 1)
                @php
                    $cols = ['respon', 'mandiri', 'temuan', 'ontime', 'quiz', 'rating'];
                @endphp

                @foreach ($cols as $col)
                    <td style="border: 1px solid #000; text-align: center; vertical-align: center;">
                        {{-- Baris 1: Nilai Konversi Bobot --}}
                        <span style="font-weight: bold; font-size: 11px;">{{ $d['kpi'][$col]['score'] }}%</span><br>

                        {{-- Baris 2: Nilai Murni (Skala 100) --}}
                        <span style="color: #0369a1; font-size: 10px;">{{ $d['kpi'][$col]['pure'] }} / 100</span><br>

                        {{-- Baris 3: Data Mentah --}}
                        <span style="color: #64748b; font-size: 9px;">({{ $d['kpi'][$col]['raw'] }})</span>
                    </td>
                @endforeach

                <td
                    style="border: 1px solid #000; text-align: center; vertical-align: center; font-weight: bold; font-size: 12px; background-color: #FEF2F2; color: #991b1b;">
                    {{ $d['kpi']['total'] }}%
                </td>
            @else
                <td colspan="7"
                    style="border: 1px solid #000; text-align: center; font-style: italic; color: #94A3B8; vertical-align: center;">
                    Tidak Berlaku (Bukan Divisi TAC)
                </td>
            @endif
        </tr>
    @endforeach
</table>
