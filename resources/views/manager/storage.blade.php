@extends('layouts.manager')

@section('content')
    <div class="p-6" x-data="storageManager()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Storage Management</h1>
                <p class="text-slate-500 text-sm">Total penggunaan memori: <span
                        class="font-bold text-primary">{{ $totalSizeFormatted }}</span></p>
            </div>

            <div class="flex gap-2">
                <button @click="deleteSelected()" x-show="selected.length > 0"
                    class="px-4 py-2 bg-red-500 text-white rounded-xl text-xs font-bold uppercase hover:bg-red-600 transition-all">
                    Hapus <span x-text="selected.length"></span> File Terpilih
                </button>
            </div>
        </div>

        <div class="bg-white p-4 rounded-t-2xl border border-slate-200 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Tampilkan:</label>
                <select onchange="window.location.href=this.value"
                    class="text-xs border-slate-200 rounded-lg focus:ring-primary">
                    <option value="{{ request()->fullUrlWithQuery(['show' => 50]) }}"
                        {{ request('show') == 50 ? 'selected' : '' }}>50</option>
                    <option value="{{ request()->fullUrlWithQuery(['show' => 100]) }}"
                        {{ request('show') == 100 ? 'selected' : '' }}>100</option>
                    <option value="{{ request()->fullUrlWithQuery(['show' => 200]) }}"
                        {{ request('show') == 200 ? 'selected' : '' }}>200</option>
                    <option value="{{ request()->fullUrlWithQuery(['show' => 'all']) }}"
                        {{ request('show') == 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>

            <div class="text-xs text-slate-400">
                Klik judul kolom untuk mengurutkan data
            </div>
        </div>

        <div class="bg-white border-x border-b border-slate-200 rounded-b-2xl overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="p-4 w-10">
                            <input type="checkbox" @change="toggleAll()" x-model="allSelected"
                                class="rounded border-slate-300 text-primary focus:ring-primary">
                        </th>
                        <th class="p-4 text-[10px] font-black uppercase text-slate-500 tracking-wider">
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'nama_pengunggah', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                Pengunggah <i class="fas fa-sort ml-1"></i>
                            </a>
                        </th>
                        <th class="p-4 text-[10px] font-black uppercase text-slate-500 tracking-wider">
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'folder', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                Kategori <i class="fas fa-sort ml-1"></i>
                            </a>
                        </th>
                        <th class="p-4 text-[10px] font-black uppercase text-slate-500 tracking-wider">
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'size_bytes', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                Ukuran <i class="fas fa-sort ml-1"></i>
                            </a>
                        </th>
                        <th class="p-4 text-[10px] font-black uppercase text-slate-500 tracking-wider">
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'tanggal', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                Tanggal <i class="fas fa-sort ml-1"></i>
                            </a>
                        </th>
                        <th class="p-4 text-[10px] font-black uppercase text-slate-500 tracking-wider text-center">Preview
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $file)
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                            <td class="p-4">
                                <input type="checkbox"
                                    value="{{ json_encode(['id' => $file->id, 'type' => $file->type, 'field' => $file->field]) }}"
                                    x-model="selected"
                                    class="file-checkbox rounded border-slate-300 text-primary focus:ring-primary">
                            </td>
                            <td class="p-4">
                                <span class="text-sm font-bold text-slate-700 block">{{ $file->nama_pengunggah }}</span>
                            </td>
                            <td class="p-4">
                                <span
                                    class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-[9px] font-bold uppercase">{{ $file->folder }}</span>
                            </td>
                            <td class="p-4 text-xs font-medium text-slate-600">{{ $file->size_human }}</td>
                            <td class="p-4 text-xs text-slate-500">{{ $file->tanggal }}</td>
                            <td class="p-4 text-center">
                                <a href="{{ asset('storage/' . $file->path) }}" target="_blank"
                                    class="text-primary hover:text-blue-700 transition-colors">
                                    <i class="fas fa-external-link-alt text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $items->links() }}
        </div>

        <form id="bulkDeleteForm" action="{{ route('manager.storage.bulk_delete') }}" method="POST" class="hidden"> @csrf
            <input type="hidden" name="selected_files" id="selectedFilesInput">
        </form>
    </div>

    <script>
        function storageManager() {
            return {
                selected: [],
                allSelected: false,
                toggleAll() {
                    // Jika dicentang, ambil semua value dari elemen yang punya class .file-checkbox
                    if (this.allSelected) {
                        const checkboxes = document.querySelectorAll('.file-checkbox');
                        this.selected = Array.from(checkboxes).map(cb => cb.value);
                    } else {
                        // Jika uncheck, kosongkan array
                        this.selected = [];
                    }
                },
                deleteSelected() {
                    if (confirm(
                        `Anda yakin ingin menghapus ${this.selected.length} file ini secara permanen dari server?`)) {
                        document.getElementById('selectedFilesInput').value = `[${this.selected.join(',')}]`;
                        document.getElementById('bulkDeleteForm').submit();
                    }
                }
            }
        }
    </script>
@endsection
