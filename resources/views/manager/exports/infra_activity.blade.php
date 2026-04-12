<table>
    <tr>
        {{-- Diubah menjadi colspan="6" karena ada 6 kolom di tabel --}}
        <th colspan="6" style="font-weight: bold; font-size: 14px;">LAPORAN AKTIVITAS INFRASTRUKTUR</th>
    </tr>
    <tr>
        <th style="font-weight: bold;">Nama</th>
        <td colspan="5">: {{ $nama }}</td>
    </tr>
    <tr>
        <th style="font-weight: bold;">Divisi</th>
        <td colspan="5">: {{ $divisi }}</td>
    </tr>
    <tr>
        <th style="font-weight: bold;">Periode Laporan</th>
        <td colspan="5">: {{ $periode }}</td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>

    <tr>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">No</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Tanggal</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Kategori</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Judul Kegiatan</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Deskripsi Kegiatan</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Bukti Dokumentasi</th>
    </tr>

    @php $no = 1; @endphp

    {{-- ============================== --}}
    {{-- 1. LOOPING KEGIATAN REGULER    --}}
    {{-- ============================== --}}
    @foreach ($kegiatans as $kegiatan)
        <tr>
            <td style="text-align: center; border: 1px solid #000000;">{{ $no++ }}</td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ !empty($kegiatan->dailyReport->tanggal) ? \Carbon\Carbon::parse($kegiatan->dailyReport->tanggal)->format('d/m/Y') : '-' }}
            </td>
            <td style="border: 1px solid #000000;">{{ $kegiatan->kategori }}</td>

            <td style="border: 1px solid #000000;">{{ $kegiatan->nama_kegiatan ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $kegiatan->deskripsi_kegiatan ?? ($kegiatan->keterangan ?? '-') }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                @if (!empty($kegiatan->foto_dokumentasi))
                    <img src="{{ public_path('storage/' . $kegiatan->foto_dokumentasi) }}" height="70" />
                @else
                    Tidak ada
                @endif
            </td>
        </tr>
    @endforeach

    {{-- ============================== --}}
    {{-- 2. LOOPING KEGIATAN LEMBUR     --}}
    {{-- ============================== --}}
    @foreach ($lemburs as $lembur)
        @php
            $mulai = \Carbon\Carbon::parse($lembur->waktu_mulai);
            $selesai = \Carbon\Carbon::parse($lembur->waktu_selesai);

            $totalMenit = $mulai->diffInMinutes($selesai);
            $jam = floor($totalMenit / 60);
            $menit = $totalMenit % 60;

            $teksDurasi = '';
            if ($jam > 0) {
                $teksDurasi .= $jam . ' Jam ';
            }
            if ($menit > 0) {
                $teksDurasi .= $menit . ' Menit';
            }

            $teksDurasi = trim($teksDurasi);
            if ($teksDurasi == '') {
                $teksDurasi = '0 Menit';
            }
        @endphp
        <tr>
            <td style="text-align: center; border: 1px solid #000000; background-color: #f1f5f9;">{{ $no++ }}
            </td>

            <td style="border: 1px solid #000000; text-align: center; background-color: #f1f5f9;">
                {{ !empty($lembur->dailyReport->tanggal) ? \Carbon\Carbon::parse($lembur->dailyReport->tanggal)->format('d/m/Y') : '-' }}
            </td>
            <td style="border: 1px solid #000000; font-weight: bold; color: #4f46e5; background-color: #f1f5f9;">
                PEKERJAAN LEMBUR
            </td>

            <td style="border: 1px solid #000000; background-color: #f1f5f9;">
                Pekerjaan Lembur ({{ $mulai->format('H:i') }} - {{ $selesai->format('H:i') }})
            </td>
            <td style="border: 1px solid #000000; background-color: #f1f5f9;">
                {{ $lembur->detail_pekerjaan }}
                <br><br>
                <strong>Durasi Total:</strong> {{ $teksDurasi }}
            </td>

            <td style="border: 1px solid #000000; text-align: center; background-color: #f1f5f9;">
                @if (!empty($lembur->foto_dokumentasi))
                    <img src="{{ public_path('storage/' . $lembur->foto_dokumentasi) }}" height="70" />
                @else
                    Tidak ada
                @endif
            </td>
        </tr>
    @endforeach

    {{-- ============================== --}}
    {{-- JIKA DATA KOSONG KEDUANYA      --}}
    {{-- ============================== --}}
    @if ($kegiatans->isEmpty() && $lemburs->isEmpty())
        <tr>
            <td colspan="6" style="text-align: center; border: 1px solid #000000;">Tidak ada aktivitas reguler maupun
                lembur pada periode ini.</td>
        </tr>
    @endif
</table>
