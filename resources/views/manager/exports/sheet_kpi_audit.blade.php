<table>
    <tr>
        <td colspan="4" style="font-size: 16px; font-weight: bold; text-align: center; color: #BE123C;">DAFTAR AUDIT
            PENGURANGAN POIN KPI</td>
    </tr>
    <tr>
        <td colspan="4"></td>
    </tr>

    @forelse($auditData as $data)
        <tr>
            <td colspan="4" style="background-color: #BE123C; color: #FFFFFF; font-weight: bold;">
                STAFF: {{ strtoupper($data['user']->nama_lengkap) }}
            </td>
        </tr>
        <tr>
            <th style="border: 1px solid #000; background-color: #FFF1F2; font-weight: bold;">TANGGAL</th>
            <th style="border: 1px solid #000; background-color: #FFF1F2; font-weight: bold;">ASPEK PENILAIAN</th>
            <th style="border: 1px solid #000; background-color: #FFF1F2; font-weight: bold;">DETAIL KEKURANGAN</th>
            <th style="border: 1px solid #000; background-color: #FFF1F2; font-weight: bold;">DAMPAK POIN</th>
        </tr>
        @foreach ($data['fails'] as $f)
            <tr>
                <td style="border: 1px solid #000;">{{ $f['tanggal'] }}</td>
                <td style="border: 1px solid #000; font-weight: bold;">{{ $f['aspek'] }}</td>
                <td style="border: 1px solid #000;">{{ $f['keterangan'] }}</td>
                <td style="border: 1px solid #000; color: #BE123C;">{{ $f['impact'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4"></td>
        </tr>
    @empty
        <tr>
            <td colspan="4" style="text-align: center; font-style: italic; color: #64748B;">Tidak ada catatan audit
                (Performa Sempurna/100%).</td>
        </tr>
    @endforelse
</table>
