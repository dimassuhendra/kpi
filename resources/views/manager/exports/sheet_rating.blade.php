<table>
    <tr>
        <td style="background-color: #F59E0B; color: #FFFFFF; font-weight: bold; border: 1px solid #000;">TANGGAL</td>
        <td style="background-color: #F59E0B; color: #FFFFFF; font-weight: bold; border: 1px solid #000;">NAMA STAFF</td>
        <td style="background-color: #F59E0B; color: #FFFFFF; font-weight: bold; border: 1px solid #000;">NOMOR TIKET
        </td>
        <td style="background-color: #F59E0B; color: #FFFFFF; font-weight: bold; border: 1px solid #000;">NAMA PELANGGAN
        </td>
        <td
            style="background-color: #F59E0B; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center;">
            RATING</td>
        <td
            style="background-color: #F59E0B; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center;">
            BUKTI (GAMBAR)</td>
    </tr>
    @foreach ($ratings as $rt)
        <tr>
            <td style="border: 1px solid #000;">{{ \Carbon\Carbon::parse($rt->tanggal_survey)->format('d/m/Y') }}</td>
            <td style="border: 1px solid #000;">{{ $rt->user->nama_lengkap }}</td>
            <td style="border: 1px solid #000;">{{ $rt->nomor_tiket }}</td>
            <td style="border: 1px solid #000;">{{ $rt->nama_pelanggan }}</td>
            <td style="border: 1px solid #000; text-align: center;">Bintang {{ $rt->rating }}</td>
            <td style="border: 1px solid #000; text-align: center; height: 80px;">
                @if ($rt->bukti_survey)
                    <img src="{{ public_path('storage/' . $rt->bukti_survey) }}" height="70" />
                @endif
            </td>
        </tr>
    @endforeach
</table>
