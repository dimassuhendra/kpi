<table>
    <tr>
        <th colspan="5" style="font-weight: bold; font-size: 14px;">LAPORAN AKTIVITAS INFRASTRUKTUR</th>
    </tr>
    <tr>
        <th style="font-weight: bold;">Nama</th>
        <td colspan="4">: {{ $nama }}</td>
    </tr>
    <tr>
        <th style="font-weight: bold;">Divisi</th>
        <td colspan="4">: {{ $divisi }}</td>
    </tr>
    <tr>
        <th style="font-weight: bold;">Periode Laporan</th>
        <td colspan="4">: {{ $periode }}</td>
    </tr>
    <tr>
        <td colspan="5"></td>
    </tr>

    <tr>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">No</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Tanggal</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Kategori</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Judul Kegiatan</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Deskripsi Kegiatan</th>
        <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Bukti Dokumentasi</th>
    </tr>

    @forelse($kegiatans as $index => $kegiatan)
        <tr>
            <td style="text-align: center; border: 1px solid #000000;">{{ $index + 1 }}</td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ !empty($kegiatan->dailyReport->tanggal) ? \Carbon\Carbon::parse($kegiatan->dailyReport->tanggal)->format('d/m/Y') : '-' }}
            </td>
            <td style="border: 1px solid #000000;">{{ $kegiatan->kategori }}</td>

            <td style="border: 1px solid #000000;">{{ $kegiatan->nama_kegiatan ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $kegiatan->deskripsi_kegiatan ?? ($kegiatan->keterangan ?? '-') }}
            </td>

            <td style="border: 1px solid #000000;">
                @if (!empty($kegiatan->foto_dokumentasi))
                    <img src="{{ public_path('storage/' . $kegiatan->foto_dokumentasi) }}" height="70" />
                @else
                    Tidak ada dokumentasi
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" style="text-align: center; border: 1px solid #000000;">Tidak ada aktivitas pada periode
                ini.</td>
        </tr>
    @endforelse
</table>
