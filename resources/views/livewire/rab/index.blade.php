<div class="space-y-6">
    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex justify-between items-center transition-all duration-500">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('message') }}
            </div>
            <button @click="show = false" class="text-green-700 hover:text-green-900">&times;</button>
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b pb-4 border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">
                @if(auth()->user()->peran == 'bendahara')
                    Verifikasi Anggaran
                @elseif(auth()->user()->peran == 'kepala_gudang')
                    Persetujuan & Verifikasi
                @else
                    Pengadaan & RAB
                @endif
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Kelola pengajuan anggaran dan inventarisasi aset sekolah.
            </p>
        </div>
        
        {{-- Tombol Ajukan (Hanya Penjaga Gudang) --}}
        @if(auth()->user()->peran == 'penjaga_gudang')
        <button wire:click="openCreateModal" 
            class="group flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 hover:shadow-md border border-transparent transition-all duration-200">
            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Pengajuan Baru
        </button>
        @endif
    </div>

    {{-- ================= SECTION 1: PRIORITY ACTIONS (YANG BUTUH TINDAKAN) ================= --}}
    
    {{-- LOGIKA: Tampilkan jika ada data yang perlu diproses user --}}
    @php
        $actionItems = collect([]);
        $sectionTitle = '';
        $sectionDesc = '';
        $cardColor = 'border-l-4 border-orange-500'; // Default Warning Color

        if(auth()->user()->peran == 'penjaga_gudang') {
            // Khusus Penjaga Gudang: Ambil RAB yang sudah disetujui bendahara (Siap Belanja & Input Teknis)
            $actionItems = isset($rabSaya) ? $rabSaya->where('status', 'disetujui_bendahara') : collect([]);
            $sectionTitle = 'Siap Belanja & Input Inventaris';
            $sectionDesc = 'RAB ini telah disetujui Bendahara. Silakan belanjakan dana dan input data teknis barang.';
            $cardColor = 'border-l-4 border-green-500';
        } else {
            // Untuk Admin Lain: Ambil dari variabel rabDiajukan
            $actionItems = isset($rabDiajukan) ? $rabDiajukan : collect([]);
            if(auth()->user()->peran == 'kepala_gudang') {
                $sectionTitle = 'Menunggu Persetujuan / Verifikasi Anda';
                $sectionDesc = 'Mohon tinjau pengajuan berikut untuk diproses lebih lanjut.';
            } elseif(auth()->user()->peran == 'bendahara') {
                $sectionTitle = 'Menunggu Persetujuan Anggaran';
                $sectionDesc = 'Cek ketersediaan dana dan setujui anggaran.';
                $cardColor = 'border-l-4 border-blue-500';
            }
        }
    @endphp

    @if($actionItems->isNotEmpty())
        <div class="bg-white rounded-xl shadow-md overflow-hidden {{ $cardColor }} ring-1 ring-gray-900/5">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        {{ $sectionTitle }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $sectionDesc }}</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-white border border-gray-200 text-xs font-bold text-gray-600 shadow-sm">
                    {{ $actionItems->count() }} Item
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Judul Pengajuan</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($actionItems as $rab)
                        <tr wire:click="showDetail({{ $rab->id }})" 
                            class="hover:bg-indigo-50 cursor-pointer transition-colors duration-150 group">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                {{ \Carbon\Carbon::parse($rab->tanggal_pengajuan)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800 group-hover:text-indigo-700">
                                {{ $rab->judul }}
                                @if(auth()->user()->peran == 'penjaga_gudang' && $rab->status == 'disetujui_bendahara')
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Dana Cair
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border shadow-sm
                                    {{ $rab->status == 'menunggu_verifikasi' ? 'bg-purple-100 text-purple-700 border-purple-200' : 
                                      ($rab->status == 'disetujui_bendahara' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-yellow-100 text-yellow-700 border-yellow-200') }}">
                                    {{ strtoupper(str_replace('_', ' ', $rab->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-gray-400 hover:text-indigo-600 group-hover:translate-x-1 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif


    {{-- ================= SECTION 2: HISTORY / MONITORING ================= --}}
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Riwayat & Monitoring
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-500 uppercase text-xs font-semibold tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Judul</th>
                        <th class="px-6 py-3">Status Terkini</th>
                        <th class="px-6 py-3 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php 
                        // Gabungkan Logic Variabel agar tabel reusable
                        $history = collect([]);
                        if(auth()->user()->peran == 'penjaga_gudang' && isset($rabSaya)) {
                            // Tampilkan yg BUKAN 'disetujui_bendahara' (karena itu udah di tabel atas)
                            $history = $rabSaya->where('status', '!=', 'disetujui_bendahara');
                        } elseif (isset($rabDiproses)) {
                            $history = $rabDiproses;
                        }
                    @endphp
                    
                    @forelse($history as $rab)
                    <tr wire:click="showDetail({{ $rab->id }})" class="hover:bg-gray-50 cursor-pointer transition-colors">
                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($rab->tanggal_pengajuan)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-700">
                            {{ $rab->judul }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColor = 'bg-gray-100 text-gray-800 border-gray-200';
                                if($rab->status == 'selesai') $statusColor = 'bg-green-100 text-green-800 border-green-200';
                                elseif($rab->status == 'ditolak') $statusColor = 'bg-red-100 text-red-800 border-red-200';
                                elseif(str_contains($rab->status, 'menunggu')) $statusColor = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                elseif($rab->status == 'disetujui_bendahara') $statusColor = 'bg-blue-100 text-blue-800 border-blue-200';
                            @endphp
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold border {{ $statusColor }}">
                                {{ strtoupper(str_replace('_', ' ', $rab->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <svg class="w-4 h-4 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Tidak ada data riwayat.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($history instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $history->links() }}
            </div>
        @endif
    </div>


    {{-- ================= MODAL DETAIL & PROSES ================= --}}
    
    @if($showDetailModal && $selectedRab)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Content --}}
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-200">
                
                {{-- Header Modal --}}
                <div class="bg-indigo-900 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-white" id="modal-title">
                        Detail Pengajuan RAB <span class="text-indigo-200 font-normal">#{{ $selectedRab->id }}</span>
                    </h3>
                    <button wire:click="closeModal" class="text-indigo-200 hover:text-white transition-colors focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body Modal --}}
                <div class="px-6 py-6 max-h-[70vh] overflow-y-auto bg-gray-50">
                    
                    {{-- Info Card --}}
                    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Judul Pengajuan</label>
                            <p class="text-lg font-bold text-gray-900 mt-1">{{ $selectedRab->judul }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Status Saat Ini</label>
                            <div class="mt-1">
                                <span class="px-3 py-1 rounded-full text-sm font-bold border
                                {{ $selectedRab->status == 'selesai' ? 'bg-green-100 text-green-800 border-green-200' : 
                                   ($selectedRab->status == 'ditolak' ? 'bg-red-100 text-red-800 border-red-200' : 'bg-indigo-100 text-indigo-800 border-indigo-200') }}">
                                    {{ strtoupper(str_replace('_', ' ', $selectedRab->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                             <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Keterangan / Alasan</label>
                             <div class="mt-1 p-3 bg-gray-50 rounded border border-gray-200 text-sm text-gray-700 italic">
                                 "{{ $selectedRab->keterangan ?? 'Tidak ada keterangan tambahan.' }}"
                             </div>
                        </div>
                    </div>

                    {{-- Tabel Item Barang --}}
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-100 border-b border-gray-200">
                             <h4 class="font-bold text-gray-700 text-sm">Daftar Item Barang</h4>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Barang</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Sumber Dana</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Jml</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Harga Satuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Total</th>
                                    @if(auth()->user()->peran == 'bendahara' && $selectedRab->status == 'menunggu_bendahara')
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($selectedRab->items as $item)
                                <tr>
                                    <td class="px-4 py-4 text-sm text-gray-900">
                                        <div class="font-bold">{{ $item->nama_barang_baru }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->spesifikasi }}</div>

                                        {{-- FORM KHUSUS INPUT DATA TEKNIS (Penjaga Gudang) --}}
                                        @if(auth()->user()->peran == 'penjaga_gudang' && $selectedRab->status == 'disetujui_bendahara')
                                            <div class="mt-3 p-3 bg-orange-50 border border-orange-200 rounded-md shadow-inner">
                                                <p class="text-[10px] font-bold text-orange-700 uppercase mb-2 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Wajib Input Data Inventaris
                                                </p>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                                    <input type="text" wire:model="procurementItems.{{ $item->id }}.kode" 
                                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-xs border-gray-300 rounded-md" 
                                                        placeholder="Kode Barang">
                                                    
                                                    <select wire:model="procurementItems.{{ $item->id }}.kategori_id" 
                                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-xs border-gray-300 rounded-md">
                                                        <option value="">Kategori...</option>
                                                        @foreach($kategoris as $k) <option value="{{$k->id}}">{{$k->nama_kategori}}</option> @endforeach
                                                    </select>
                                                    
                                                    <select wire:model="procurementItems.{{ $item->id }}.ruangan_id" 
                                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-xs border-gray-300 rounded-md">
                                                        <option value="">Ruangan...</option>
                                                        @foreach($ruangans as $r) <option value="{{$r->id}}">{{$r->nama_ruangan}}</option> @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- TAMPILKAN DATA JIKA SUDAH ADA --}}
                                        @if($item->kode_barang_fix)
                                            <div class="mt-1 flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-300">
                                                    Kode: {{ $item->kode_barang_fix }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-300">
                                                    {{ \App\Models\Ruangan::find($item->ruangan_id_fix)->nama_ruangan ?? '-' }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-blue-600 font-medium">
                                        {{ \App\Models\SumberDana::find($item->sumber_dana_id)->nama_sumber ?? '-' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-center font-bold">
                                        @if($editingItemId === $item->id)
                                            <div class="flex items-center justify-center space-x-1">
                                                <input type="number" wire:model="editJumlah" class="w-16 p-1 text-center text-xs border rounded focus:ring-indigo-500">
                                                <button wire:click="saveItemBendahara({{ $item->id }})" class="text-green-600 hover:text-green-800">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </div>
                                        @else
                                            {{ $item->jumlah }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-right text-gray-500">
                                        Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-right font-bold text-gray-900">
                                        Rp {{ number_format($item->harga_total, 0, ',', '.') }}
                                    </td>

                                    {{-- Aksi Bendahara (Edit/Hapus Item) --}}
                                    @if(auth()->user()->peran == 'bendahara' && $selectedRab->status == 'menunggu_bendahara')
                                        <td class="px-4 py-4 text-center">
                                            <div class="flex justify-center space-x-2">
                                                <button wire:click="editItemBendahara({{ $item->id }})" class="p-1 text-blue-600 hover:bg-blue-100 rounded border border-transparent hover:border-blue-200 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </button>
                                                <button wire:click="hapusItemBendahara({{ $item->id }})" 
                                                        onclick="return confirm('Yakin hapus item ini?')"
                                                        class="p-1 text-red-600 hover:bg-red-100 rounded border border-transparent hover:border-red-200 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-700 uppercase text-xs">Total Anggaran</td>
                                    <td class="px-4 py-3 text-right font-bold text-indigo-700 text-base">
                                        Rp {{ number_format($selectedRab->items->sum('harga_total'), 0, ',', '.') }}
                                    </td>
                                    @if(auth()->user()->peran == 'bendahara' && $selectedRab->status == 'menunggu_bendahara') <td></td> @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Form Catatan --}}
                    <div class="mt-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Persetujuan / Penolakan</label>
                        <textarea wire:model="catatan_kepala" rows="3" 
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                            placeholder="Tambahkan catatan untuk pengguna lain..."
                            @if(auth()->user()->peran == 'penjaga_gudang') disabled @endif></textarea>
                    </div>

                </div>

                {{-- Footer Modal (Tombol Aksi) --}}
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-end gap-3">
                    <button wire:click="closeModal" 
                        class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm transition">
                        Tutup
                    </button>

                    {{-- TOMBOL KEPALA GUDANG --}}
                    @if(auth()->user()->peran == 'kepala_gudang')
                        @if($selectedRab->status == 'diajukan')
                            <button wire:click="tolakRab" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:text-sm transition">
                                Tolak
                            </button>
                            <button wire:click="teruskanKeBendahara" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:text-sm transition">
                                Setuju & Teruskan ke Bendahara
                            </button>
                        @elseif($selectedRab->status == 'menunggu_verifikasi')
                             <button wire:click="verifikasiAkhir" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:text-sm transition">
                                Verifikasi Fisik & Masukkan Stok
                            </button>
                        @endif
                    
                    {{-- TOMBOL BENDAHARA --}}
                    @elseif(auth()->user()->peran == 'bendahara' && $selectedRab->status == 'menunggu_bendahara')
                         <button wire:click="tolakRab" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:text-sm transition">
                            Tolak Anggaran
                         </button>
                         <button wire:click="setujuiOlehBendahara" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:text-sm transition">
                            Setujui Anggaran
                        </button>

                    {{-- TOMBOL PENJAGA GUDANG --}}
                    @elseif(auth()->user()->peran == 'penjaga_gudang' && $selectedRab->status == 'disetujui_bendahara')
                         <button wire:click="laporBarangDatang" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none sm:text-sm transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Simpan Data & Lapor Barang Datang
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Create (External File) --}}
    @if($showCreateModal)
        @include('livewire.rab.create-modal')
    @endif
</div>