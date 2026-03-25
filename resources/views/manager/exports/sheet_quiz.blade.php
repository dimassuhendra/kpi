<table>
    <tr>
        <td style="background-color: #6366F1; color: #FFFFFF; font-weight: bold; border: 1px solid #000;">TANGGAL INPUT
        </td>
        <td style="background-color: #6366F1; color: #FFFFFF; font-weight: bold; border: 1px solid #000;">NAMA STAFF</td>
        <td
            style="background-color: #6366F1; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center;">
            JUMLAH SOAL</td>
        <td
            style="background-color: #6366F1; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center;">
            JAWABAN BENAR</td>
        <td
            style="background-color: #6366F1; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center;">
            PERSENTASE</td>
        <td
            style="background-color: #6366F1; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center;">
            BUKTI (GAMBAR)</td>
    </tr>
    @foreach ($quizzes as $qz)
        <tr>
            <td style="border: 1px solid #000;">{{ \Carbon\Carbon::parse($qz->created_at)->format('d/m/Y') }}</td>
            <td style="border: 1px solid #000;">{{ $qz->user->nama_lengkap }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $qz->jumlah_soal }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $qz->jumlah_benar }}</td>
            <td style="border: 1px solid #000; text-align: center;">
                {{ $qz->jumlah_soal > 0 ? round(($qz->jumlah_benar / $qz->jumlah_soal) * 100) : 0 }}%
            </td>
            <td style="border: 1px solid #000; text-align: center; height: 80px;">
                @if ($qz->bukti_kuis)
                    <img src="{{ public_path('storage/' . $qz->bukti_kuis) }}" height="70" />
                @endif
            </td>
        </tr>
    @endforeach
</table>
