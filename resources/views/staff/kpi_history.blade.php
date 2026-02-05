@extends('layouts.staff')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="font-header text-4xl text-primary">Riwayat Laporan</h2>
            <p class="font-body text-secondary opacity-70">Pantau status pengerjaan dan skor KPI Anda.</p>
        </div>
        <a href="{{ route('staff.kpi.create') }}" class="bg-primary text-white px-6 py-3 rounded-2xl font-header hover:bg-secondary transition shadow-lg">
            <i class="fas fa-plus mr-2"></i> Input Baru
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-primary/5 text-secondary opacity-70 text-xs uppercase tracking-wider">
                        <th class="py-5 px-8">Tanggal Laporan</th>
                        <th class="py-5 px-8">Jumlah Tiket</th>
                        <th class="py-5 px-8">Status</th>
                        <th class="py-5 px-8">Skor Akhir</th>
                        <th class="py-5 px-8">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($submissions as $data)
                    <tr class="hover:bg-background/50 transition">
                        <td class="py-5 px-8 font-bold text-primary">
                            {{ \Carbon\Carbon::parse($data->assessment_date)->translatedFormat('d F Y') }}
                        </td>
                        <td class="py-5 px-8">
                            <span class="bg-accent/10 text-accent px-3 py-1 rounded-lg text-sm font-bold">
                                {{ $data->caseLogs->count() }} Case
                            </span>
                        </td>
                        <td class="py-5 px-8">
                            @if($data->status == 'pending')
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-600 rounded-full text-xs font-bold uppercase">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                            @elseif($data->status == 'approved')
                            <span class="px-4 py-2 bg-green-100 text-green-600 rounded-full text-xs font-bold uppercase">
                                <i class="fas fa-check-circle mr-1"></i> Approved
                            </span>
                            @else
                            <span class="px-4 py-2 bg-red-100 text-red-600 rounded-full text-xs font-bold uppercase">
                                <i class="fas fa-times-circle mr-1"></i> Rejected
                            </span>
                            @endif
                        </td>
                        <td class="py-5 px-8">
                            <div class="text-xl font-header {{ $data->total_final_score >= 80 ? 'text-green-600' : 'text-primary' }}">
                                {{ number_format($data->total_final_score, 2) }}
                            </div>
                        </td>
                        <td class="py-5 px-8">
                            <button class="text-secondary hover:text-primary transition p-2 bg-background rounded-xl">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="opacity-30 mb-4">
                                <i class="fas fa-folder-open text-6xl"></i>
                            </div>
                            <p class="text-secondary opacity-50 font-body">Belum ada riwayat laporan pengerjaan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $submissions->links() }}
    </div>
</div>
@endsection