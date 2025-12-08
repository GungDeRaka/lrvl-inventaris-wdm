<div>
    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm flex items-center"
            role="alert">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
            </svg>
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm flex items-center"
            role="alert">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm" role="alert">
            <div class="flex items-center mb-1">
                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <strong class="font-bold">Harap perbaiki error berikut:</strong>
            </div>
            <ul class="mt-1 list-disc list-inside text-sm ml-7">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header Selamat Datang --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Transaksi</h1>
        <p class="text-gray-500 mt-1">Selamat Datang, <span
                class="font-semibold text-primary">{{ Auth::user()->name }}</span>!</p>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Card Total Unit --}}
        <div
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition duration-200 flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Unit Barang</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalUnitBarang }}</p>
            </div>
        </div>
        {{-- Card Sedang Dipinjam --}}
        <div
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition duration-200 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Sedang Dipinjam</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalDipinjam }}</p>
            </div>
        </div>
        {{-- Card Barang Rusak --}}
        <div
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition duration-200 flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Barang Rusak</p>
                <p class="text-3xl font-bold text-red-600">{{ $totalRusak }}</p>
            </div>
        </div>
        {{-- Card Jatuh Tempo --}}
        <div
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition duration-200 flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Jatuh Tempo</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $jatuhTempo }}</p>
            </div>
        </div>
        
    </div>

    <div class="mt-7 mb-5 bg-gradient-to-r from-indigo-600 to-primary rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
    {{-- Hiasan Background --}}
    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-bold flex items-center gap-2">
                <svg class="w-8 h-8 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                AI Assistant: Prediksi Peminjaman
            </h2>
            <p class="text-indigo-100 mt-2 max-w-xl">
                Gunakan kecerdasan buatan untuk memperkirakan total peminjaman barang untuk <strong>besok hari</strong> berdasarkan pola data historis.
            </p>
        </div>

        <div class="flex flex-col items-end gap-3">
            {{-- Area Hasil --}}
            <div id="dashboard-prediction-result" class="hidden text-right">
                <span class="block text-xs text-indigo-200 uppercase tracking-wider font-bold">Hasil Prediksi</span>
                <span id="dashboard-prediction-text" class="text-3xl font-bold text-white tracking-tight">0 Unit</span>
            </div>

            {{-- Tombol --}}
            <button onclick="fetchDashboardPrediction()" id="btn-dashboard-predict" 
                class="px-6 py-3 bg-white text-indigo-700 font-bold rounded-xl shadow-md hover:bg-indigo-50 hover:scale-105 transition-all flex items-center gap-2">
                <span>Mulai Analisa</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</div>

{{-- SCRIPT KHUSUS DASHBOARD --}}
<script>
    async function fetchDashboardPrediction() {
        const btn = document.getElementById('btn-dashboard-predict');
        const resultArea = document.getElementById('dashboard-prediction-result');
        const resultText = document.getElementById('dashboard-prediction-text');
        const originalBtnText = btn.innerHTML;

        // Loading State
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menganalisa...';
        
        try {
            // Panggil route global
            const response = await fetch("{{ route('prediksi.check') }}");
            const data = await response.json();

            if (data.status === 'success') {
                resultArea.classList.remove('hidden');
                resultText.innerText = data.prediction + " Transaksi";
                // Ubah tombol jadi "Cek Lagi"
                btn.innerHTML = '<span>Cek Lagi</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>';
            } else {
                alert('Gagal: ' + data.message);
                btn.innerHTML = originalBtnText;
            }
        } catch (error) {
            console.error(error);
            alert('Gagal menghubungi server AI.');
            btn.innerHTML = originalBtnText;
        } finally {
            btn.disabled = false;
        }
    }
</script>

    {{-- Tabel Permintaan Masuk --}}
    @if ($permintaanMasuk->isNotEmpty())
        <div class="mb-10 bg-white shadow-lg rounded-xl border border-orange-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-orange-50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-orange-700 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    PERMINTAAN PEMINJAMAN MASUK
                </h2>
                <span
                    class="bg-orange-200 text-orange-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $permintaanMasuk->count() }}
                    Baru</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Siswa</th>
                            <th class="py-3 px-6 text-left">Barang</th>
                            <th class="py-3 px-6 text-left">Waktu Pinjam</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @foreach ($permintaanMasuk as $permintaan)
                            <tr class="border-b border-gray-200 hover:bg-orange-50 transition duration-150">
                                <td class="py-3 px-6 font-medium">{{ $permintaan->siswa->nama }}</td>
                                <td class="py-3 px-6">
                                    @foreach ($permintaan->barangs as $barang)
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                                            <span>{{ $barang->nama_barang }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="py-3 px-6">
                                    {{ \Carbon\Carbon::parse($permintaan->waktu_pinjam)->format('d M, H:i') }}</td>
                                <td class="py-3 px-6 text-center whitespace-nowrap">
                                    <div class="flex item-center justify-center gap-2">
                                        <button wire:click="setujuiPermintaan({{ $permintaan->id }})"
                                            class="bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-800 px-3 py-1 rounded-md text-xs font-bold transition shadow-sm border border-green-200">
                                            Setujui
                                        </button>
                                        <button wire:click="tolakPermintaan({{ $permintaan->id }})"
                                            class="bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-800 px-3 py-1 rounded-md text-xs font-bold transition shadow-sm border border-red-200">
                                            Tolak
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($permohonanPerpanjangan->isNotEmpty())
        <div class="mb-10 bg-white shadow-lg rounded-xl border border-cyan-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-cyan-50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-cyan-700 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    PERMOHONAN PERPANJANGAN
                </h2>
                <span
                    class="bg-cyan-200 text-cyan-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $permohonanPerpanjangan->count() }}
                    Pengajuan</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Siswa</th>
                            <th class="py-3 px-6 text-left">Barang</th>
                            <th class="py-3 px-6 text-left">Waktu Kembali Awal</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @foreach ($permohonanPerpanjangan as $permohonan)
                            <tr class="border-b border-gray-200 hover:bg-cyan-50 transition duration-150">
                                <td class="py-3 px-6 font-medium">{{ $permohonan->siswa->nama }}</td>
                                <td class="py-3 px-6">
                                    @foreach ($permohonan->barangs as $barang)
                                        <span class="block">{{ $barang->nama_barang }}</span>
                                    @endforeach
                                </td>
                                <td class="py-3 px-6">
                                    {{ \Carbon\Carbon::parse($permohonan->waktu_kembali)->format('d M, H:i') }}</td>
                                <td class="py-3 px-6 text-center whitespace-nowrap">
                                    <div class="flex item-center justify-center gap-2">
                                        <button wire:click="bukaModalPerpanjangan({{ $permohonan->id }})"
                                            class="bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-800 px-3 py-1 rounded-md text-xs font-bold transition shadow-sm border border-green-200">Setujui</button>
                                        <button wire:click="tolakPerpanjangan({{ $permohonan->id }})"
                                            wire:confirm="Anda yakin ingin menolak permohonan ini?"
                                            class="bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-800 px-3 py-1 rounded-md text-xs font-bold transition shadow-sm border border-red-200">Tolak</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Panel Konfirmasi Pengembalian --}}
    @if ($menungguKonfirmasi->isNotEmpty())
        <div class="mb-10 bg-white shadow-lg rounded-xl border border-indigo-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-indigo-700 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    MENUNGGU KONFIRMASI PENGEMBALIAN
                </h2>
                <span
                    class="bg-indigo-200 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $menungguKonfirmasi->count() }}
                    Menunggu</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Siswa</th>
                            <th class="py-3 px-6 text-left">Barang</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @foreach ($menungguKonfirmasi as $transaksi)
                            <tr class="border-b border-gray-200 hover:bg-indigo-50 transition duration-150">
                                <td class="py-3 px-6 font-medium">{{ $transaksi->siswa->nama }}</td>
                                <td class="py-3 px-6">
                                    @foreach ($transaksi->barangs as $barang)
                                        <div class="flex items-center justify-between mb-1">
                                            <span>{{ $barang->nama_barang }}</span>
                                            <span
                                                class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded">{{ $barang->pivot->kuantitas }}
                                                unit</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <button wire:click="bukaModalKonfirmasi({{ $transaksi->id }})"
                                        class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-md text-xs font-bold transition shadow-sm flex items-center justify-center mx-auto">
                                        Proses Pengembalian
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Grup Tombol Aksi & Pencarian --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6 top-0 z-20 bg-gray-100 py-2">
        <h2 class="text-2xl font-bold text-gray-800">Riwayat Peminjaman</h2>
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            {{-- Search --}}
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari peminjaman..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
            </div>

            {{-- Tombol Cetak --}}
            <button wire:click="openReportModal"
                class="w-full sm:w-auto flex items-center justify-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m8-4V3a1 1 0 00-1-1H8a1 1 0 00-1 1v10a1 1 0 001 1h8a1 1 0 001-1zM7 10a1 1 0 011-1h8a1 1 0 011 1v10H7V10z">
                    </path>
                </svg>
                Laporan
            </button>
        </div>
    </div>

    {{-- Tabel Riwayat Peminjaman --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100 relative">
        {{-- Loading Overlay --}}
        <div wire:loading.flex wire:target="search, nextPage, previousPage, simpanPeminjaman"
            class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-20">
            <div class="flex flex-col items-center">
                <svg class="animate-spin h-10 w-10 text-primary mb-2" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-sm font-medium text-gray-500">Memuat data...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead
                    class="bg-purple-50 text-purple-900 uppercase text-xs font-bold tracking-wider border-b border-purple-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Barang</th>
                        <th class="px-6 py-4 text-left">Peminjam</th>
                        <th class="px-6 py-4 text-left">Lokasi</th>
                        <th class="px-6 py-4 text-left">Waktu</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse ($transaksis as $transaksi)
                        @php
                            $waktuKembali = \Carbon\Carbon::parse($transaksi->waktu_kembali);
                            $isJatuhTempo =
                                $transaksi->status == 'dipinjam' &&
                                \Carbon\Carbon::parse($transaksi->waktu_kembali)->isPast();

                            $statusClass = match ($transaksi->status) {
                                'dipinjam', 'disetujui' => $isJatuhTempo
                                    ? 'bg-red-100 text-red-700 border border-red-200'
                                    : 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                                'dikembalikan' => 'bg-green-100 text-green-700 border border-green-200',
                                'ditolak' => 'bg-gray-100 text-gray-600 border border-gray-200',
                                default => 'bg-blue-100 text-blue-700 border border-blue-200',
                            };

                            $isMendekatiBatas =
                                ($transaksi->status == 'dipinjam' || $transaksi->status == 'disetujui') &&
                                $waktuKembali->between(now(), now()->addMinutes(30));
                        @endphp

                        <tr
                            class="hover:bg-purple-50/50 transition duration-150 {{ $isJatuhTempo ? 'bg-red-50/30' : '' }}">
                            {{-- Barang --}}
                            <td class="px-6 py-4 align-top">
                                <ul class="space-y-1">
                                    @foreach ($transaksi->barangs as $barang)
                                        <li class="flex items-start">
                                            <span
                                                class="font-bold text-gray-800 mr-1">({{ $barang->pivot->kuantitas }})</span>
                                            <div>
                                                <span
                                                    class="block font-medium text-gray-900">{{ $barang->nama_barang }}</span>
                                                <span
                                                    class="text-xs text-gray-500 block">{{ $barang->ruangan->nama_ruangan }}</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            {{-- Peminjam --}}
                            <td class="px-6 py-4 align-top">
                                <button wire:click="showSiswaDetail({{ $transaksi->siswa->id }})"
                                    class="font-semibold text-primary hover:text-purple-800 hover:underline text-left">
                                    {{ $transaksi->siswa->nama }}
                                </button>
                            </td>

                            {{-- Lokasi --}}
                            <td class="px-6 py-4 align-top">
                                <span
                                    class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium">{{ $transaksi->ruang_pemakaian }}</span>
                            </td>

                            {{-- Waktu --}}
                            <td class="px-6 py-4 align-top text-xs">
                                <div class="flex flex-col gap-1">
                                    <span class="text-gray-500">Pinjam:</span>
                                    <span
                                        class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($transaksi->waktu_pinjam)->format('d M Y, H:i') }}</span>

                                    <span class="text-gray-500 mt-1">Kembali:</span>
                                    @if ($transaksi->waktu_pengembalian_aktual)
                                        <span
                                            class="font-bold text-green-600">{{ \Carbon\Carbon::parse($transaksi->waktu_pengembalian_aktual)->format('d M Y, H:i') }}</span>
                                    @else
                                        <span
                                            class="font-medium {{ $isJatuhTempo ? 'text-red-600 animate-pulse' : 'text-gray-900' }}">
                                            {{ \Carbon\Carbon::parse($transaksi->waktu_kembali)->format('d M Y, H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 align-top text-center">
                                <span
                                    class="px-3 py-1 text-xs font-bold uppercase rounded-full {{ $statusClass }} inline-block shadow-sm">
                                    {{ $isJatuhTempo ? 'TERLAMBAT' : ucfirst($transaksi->status) }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 align-top text-center">
                                <div class="flex flex-col gap-2 items-center">
                                    @if ($isMendekatiBatas || $isJatuhTempo)
                                        @php
                                            $barangList = $transaksi->barangs->pluck('nama_barang')->join(', ');
                                            $pesan =
                                                "PENGINGAT: Peminjaman barang '{$barangList}' " .
                                                ($isJatuhTempo ? 'telah TERLAMBAT' : 'akan berakhir segera') .
                                                '. Mohon segera dikembalikan. Terima kasih.';
                                        @endphp
                                        <a href="https://api.whatsapp.com/send?phone={{ $transaksi->siswa->formatted_no_hp }}&text={{ urlencode($pesan) }}"
                                            target="_blank"
                                            class="w-full text-orange-600 hover:text-orange-800 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded text-xs font-bold transition border border-orange-200 flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                            </svg>
                                            WhatsApp
                                        </a>
                                    @endif

                                    <a href="{{ route('transaksi.cetak', $transaksi->id) }}" target="_blank"
                                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-xs font-bold transition border border-gray-300 flex items-center justify-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m8-4V3a1 1 0 00-1-1H8a1 1 0 00-1 1v10a1 1 0 001 1h8a1 1 0 001-1zM7 10a1 1 0 011-1h8a1 1 0 011 1v10H7V10z">
                                            </path>
                                        </svg>
                                        Cetak
                                    </a>

                                    @if ($transaksi->status == 'disetujui')
                                        <button wire:click="konfirmasiAmbil({{ $transaksi->id }})"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">
                                            Konfirmasi Ambil
                                        </button>
                                        <button wire:click="batalkanPeminjaman({{ $transaksi->id }})"
                                            wire:confirm="Anda yakin ingin membatalkan booking ini?"
                                            class="w-full text-red-600 hover:text-red-800 hover:bg-red-50 px-3 py-1.5 rounded text-xs font-bold transition">
                                            Batalkan
                                        </button>
                                    @endif

                                    @if ($transaksi->status == 'dipinjam')
                                        <button wire:click="konfirmasiPengembalian({{ $transaksi->id }})"
                                            class="w-full bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded text-xs font-bold transition shadow-sm">
                                            Kembalikan
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 bg-gray-50">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <p>Belum ada riwayat peminjaman.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
    {{-- Pagination --}}
    @if ($transaksis->hasPages())
        <div class="bg-gray-50 px-6 py-4 border items-start border-gray-300 shadow-lg w-2/3 z-50 rounded-md">
            {{ $transaksis->links() }}
        </div>
    @endif


    {{-- FAB: Tambah Peminjaman --}}
    {{-- <button wire:click="openTransactionModal"
        class="fixed bottom-8 right-8 w-14 h-14 bg-purple-700 text-white rounded-full shadow-lg hover:bg-purple-800 hover:shadow-xl transition transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-purple-300 flex items-center justify-center z-40"
        title="Tambah Peminjaman Baru">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
    </button> --}}

    <button wire:click="openTransactionModal"
        class="fixed bottom-8 right-8 bg-primary text-white p-4 rounded-full shadow-lg hover:bg-purple-800 transition transform hover:scale-110 focus:outline-none z-50 flex items-center justify-center"
        title="Buat Pengajuan RAB Baru">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span class="mx-2 text-sm font-semibold">Transaksi Peminjaman</span>
    </button>

    {{-- Modal Form Peminjaman --}}
    @if ($showTransactionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-transition>
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col">
                <div class="flex justify-between items-center p-6 border-b bg-gray-50 rounded-t-xl flex-shrink-0">
                    <h3 class="text-xl font-bold text-gray-800">Form Peminjaman Barang</h3>
                    <button wire:click="closeTransactionModal"
                        class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
                </div>

                <form wire:submit.prevent="simpanPeminjaman" class="flex flex-col flex-1 min-h-0">
                    <div class="p-6 overflow-y-auto flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Kolom Kiri --}}
                            <div class="space-y-4">
                                <div>
                                    <label for="nis" class="block text-sm font-medium text-gray-700 mb-1">Cari
                                        NIS Siswa</label>
                                    <div class="relative">
                                        <input type="text" id="nis" wire:model.live.debounce.300ms="nis"
                                            class="w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                            placeholder="Contoh: 4774">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    @error('siswaDitemukan')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if ($siswaDitemukan)
                                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 flex items-start">
                                        <div class="bg-blue-100 rounded-full p-2 mr-3 text-blue-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-blue-900">{{ $siswaDitemukan->nama }}</p>
                                            <p class="text-xs text-blue-700 mt-1">Kelas: {{ $siswaDitemukan->kelas }}
                                                | {{ $siswaDitemukan->no_hp }}</p>
                                        </div>
                                    </div>
                                @elseif(!empty($nis))
                                    <div
                                        class="bg-red-50 p-3 rounded-lg border border-red-100 text-red-600 text-sm flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Siswa tidak ditemukan.
                                    </div>
                                @endif

                                @if (!empty($keranjang))
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                                        <div
                                            class="px-3 py-2 bg-gray-100 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase">
                                            Keranjang Barang ({{ count($keranjang) }})
                                        </div>
                                        <div class="max-h-48 overflow-y-auto p-2 space-y-2">
                                            @foreach ($keranjang as $index => $item)
                                                <div
                                                    class="bg-white p-3 rounded shadow-sm border border-gray-100 flex justify-between items-center">
                                                    <div>
                                                        <span
                                                            class="text-sm font-bold text-gray-800 block">{{ $item['nama'] }}</span>
                                                        <span class="text-xs text-gray-500 block">Asal:
                                                            {{ $item['asal'] }}</span>
                                                    </div>
                                                    <div class="flex items-center space-x-1">
                                                        <button type="button"
                                                            wire:click="decrementKuantitas({{ $index }})"
                                                            class="w-6 h-6 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs">-</button>
                                                        <span
                                                            class="text-sm w-6 text-center font-semibold">{{ $item['kuantitas'] }}</span>
                                                        <button type="button"
                                                            wire:click="incrementKuantitas({{ $index }})"
                                                            class="w-6 h-6 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs">+</button>
                                                        <button type="button"
                                                            wire:click="hapusDariKeranjang({{ $index }})"
                                                            class="ml-2 text-red-500 hover:text-red-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @error('keranjang')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Kolom Kanan --}}
                            <div class="space-y-4">
                                <div class="relative">
                                    <label for="searchBarang"
                                        class="block text-sm font-medium text-gray-700 mb-1">Cari & Tambah
                                        Barang</label>
                                    <input type="text" id="searchBarang"
                                        wire:model.live.debounce.300ms="searchBarang"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                        placeholder="Ketik nama barang..." autocomplete="off">
                                    @if (!empty($barangDitemukan))
                                        <div
                                            class="absolute z-10 w-full mt-1 bg-white rounded-lg shadow-xl border border-gray-200 max-h-60 overflow-y-auto">
                                            <ul class="py-1">
                                                @forelse($barangDitemukan as $barang)
                                                    @if ($barang->jumlah_saat_ini > 0)
                                                        <li wire:click="tambahKeKeranjang({{ $barang->id }}, '{{ addslashes($barang->nama_barang) }}', '{{ $barang->ruangan->nama_ruangan }}')"
                                                            class="px-4 py-3 cursor-pointer hover:bg-purple-50 border-b border-gray-100 last:border-0 transition">
                                                            <div class="flex justify-between items-center">
                                                                <div>
                                                                    <span
                                                                        class="font-medium text-gray-900 block">{{ $barang->nama_barang }}</span>
                                                                    <span
                                                                        class="text-xs text-gray-500 block">{{ $barang->ruangan->nama_ruangan }}</span>
                                                                </div>
                                                                <span
                                                                    class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-bold">{{ $barang->jumlah_saat_ini }}
                                                                    Stok</span>
                                                            </div>
                                                        </li>
                                                    @else
                                                        <li
                                                            class="px-4 py-3 text-gray-400 cursor-not-allowed bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                                                            <span>{{ $barang->nama_barang }}</span>
                                                            <span class="text-xs font-bold text-red-400">Habis</span>
                                                        </li>
                                                    @endif
                                                @empty
                                                    <li class="px-4 py-3 text-gray-500 text-sm text-center">Barang
                                                        tidak ditemukan.</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label for="ruang_pemakaian"
                                        class="block text-sm font-medium text-gray-700 mb-1">Ruang Pemakaian</label>
                                    <select wire:model="ruang_pemakaian" id="ruang_pemakaian"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                        <option value="">Pilih Ruangan</option>
                                        @foreach ($ruangans as $ruangan)
                                            @php
                                                $isAsalRuangan = isset($this->asalRuangan[$ruangan->nama_ruangan]);
                                            @endphp
                                            <option value="{{ $ruangan->nama_ruangan }}"
                                                {{ $isAsalRuangan ? 'disabled' : '' }}
                                                class="{{ $isAsalRuangan ? 'text-gray-400 bg-gray-50' : '' }}">
                                                {{ $ruangan->nama_ruangan }}
                                                @if ($isAsalRuangan)
                                                    (Asal:
                                                    {{ $this->asalRuangan[$ruangan->nama_ruangan]->pluck('nama_barang')->join(', ') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ruang_pemakaian')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="waktu_kembali"
                                        class="block text-sm font-medium text-gray-700 mb-1">Waktu Pengembalian</label>
                                    <input type="datetime-local" id="waktu_kembali" wire:model="waktu_kembali"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                    @error('waktu_kembali')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t bg-gray-50 rounded-b-xl flex justify-end space-x-3 flex-shrink-0">
                        <button type="button" wire:click="closeTransactionModal"
                            class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 font-medium transition">Batal</button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2.5 bg-purple-700 text-white rounded-lg shadow-md hover:bg-purple-800 font-bold transition flex items-center">
                            <svg wire:loading wire:target="simpanPeminjaman"
                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Simpan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Pengembalian & Detail Siswa --}}
    {{-- (Gunakan kode modal pengembalian dan detail siswa yang sudah ada, sesuaikan styling tombol dan border-radius agar konsisten) --}}
    @if ($transaksiIdUntukDikembalikan)
        {{-- ... (Kode Modal Pengembalian dengan styling serupa di atas) ... --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Konfirmasi Pengembalian</h3>
                <p class="text-sm text-gray-600 mb-4">Peminjam: <strong
                        class="text-gray-900">{{ $transaksiTerpilih->siswa->nama }}</strong></p>
                <div class="space-y-3 mb-6 max-h-60 overflow-y-auto pr-1">
                    @foreach ($transaksiTerpilih->barangs as $barang)
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded border border-gray-100">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $barang->nama_barang }}</p>
                                <p class="text-xs text-gray-500">Dipinjam: {{ $barang->pivot->kuantitas }} unit</p>
                            </div>
                            <div class="flex items-center">
                                <label class="text-xs text-gray-500 mr-2">Rusak:</label>
                                <input type="number" wire:model="kerusakanItems.{{ $barang->id }}"
                                    class="w-16 text-sm border-gray-300 rounded focus:ring-purple-500 focus:border-purple-500 p-1"
                                    min="0" max="{{ $barang->pivot->kuantitas }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-end space-x-2">
                    <button wire:click="$set('transaksiIdUntukDikembalikan', null)"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-medium text-sm">Batal</button>
                    <button wire:click="prosesPengembalian"
                        class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 font-bold text-sm shadow-sm">Konfirmasi</button>
                </div>
            </div>
        </div>
    @endif

    @if ($siswaDetail)
        <div wire:click="closeModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 cursor-pointer">
            <div @click.stop class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 cursor-default relative">
                <button wire:click="closeModal"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Detail Peminjam</h3>
                <div class="space-y-3">
                    <div class="grid grid-cols-3 text-sm"><span class="text-gray-500">Nama</span><span
                            class="col-span-2 font-medium text-gray-900">{{ $siswaDetail->nama }}</span></div>
                    <div class="grid grid-cols-3 text-sm"><span class="text-gray-500">NIS</span><span
                            class="col-span-2 font-medium text-gray-900">{{ $siswaDetail->nis }}</span></div>
                    <div class="grid grid-cols-3 text-sm"><span class="text-gray-500">Kelas</span><span
                            class="col-span-2 font-medium text-gray-900">{{ $siswaDetail->kelas }}</span></div>
                    <div class="grid grid-cols-3 text-sm"><span class="text-gray-500">No. HP</span><span
                            class="col-span-2 font-medium text-gray-900">{{ $siswaDetail->no_hp }}</span></div>
                    <div class="grid grid-cols-3 text-sm"><span class="text-gray-500">Status</span>
                        <span class="col-span-2">
                            @if ($siswaDetail->is_ditangguhkan)
                                <span
                                    class="text-red-600 font-bold bg-red-50 px-2 py-0.5 rounded-full text-xs">Ditangguhkan</span>
                            @else
                                <span
                                    class="text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded-full text-xs">Aktif</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Filter Laporan --}}
    @if ($showReportModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
                {{-- Form sekarang ada di dalam modal --}}
                <form action="{{ route('laporan.transaksi') }}" method="GET" target="_blank">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cetak Laporan Transaksi</h3>
                    <p class="text-sm text-gray-600 mb-4">Pilih rentang tanggal untuk mencetak laporan. Kosongkan untuk
                        mencetak semua riwayat transaksi.</p>

                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Dari
                                Tanggal</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" wire:model="tanggal_mulai"
                                class="mt-1 text-sm border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Sampai
                                Tanggal</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" wire:model="tanggal_akhir"
                                class="mt-1 text-sm border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" wire:click="closeReportModal"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Cetak</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Alasan Penolakan --}}
    @if ($showTolakModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="prosesPenolakan">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Alasan Penolakan</h3>
                    <div>
                        <label for="alasan_penolakan" class="sr-only">Alasan Penolakan</label>
                        <textarea wire:model="alasan_penolakan" id="alasan_penolakan" rows="4"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Contoh: Barang tidak tersedia pada jadwal tersebut."></textarea>
                        @error('alasan_penolakan')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showTolakModal', false)"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>

                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Tolak
                            Permintaan</button>
                    </div>
            </div>
            </form>
        </div>
</div>
@endif

{{-- Modal Konfirmasi Final Pengembalian --}}
@if ($showKonfirmasiModal && $konfirmasiTransaksi)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Final Pengembalian</h3>
            <p class="text-sm text-gray-600 mb-4">Peminjam: <strong>{{ $konfirmasiTransaksi->siswa->nama }}</strong>
            </p>

            <div class="space-y-4 max-h-60 overflow-auto">
                <p class="text-sm font-medium">Periksa kondisi barang dan konfirmasi jumlah yang rusak (jika ada):</p>
                @foreach ($konfirmasiTransaksi->barangs as $barang)
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-md">
                        <div>
                            <p class="text-sm">{{ $barang->nama_barang }} (Dipinjam: {{ $barang->pivot->kuantitas }})
                            </p>
                            @if ($barang->pivot->jumlah_rusak_dilaporkan > 0)
                                <small class="text-red-600 font-semibold">Siswa melaporkan
                                    {{ $barang->pivot->jumlah_rusak_dilaporkan }} unit rusak.</small>
                            @endif
                        </div>
                        <div>
                            <label for="final_rusak_{{ $barang->id }}" class="text-sm mr-2">Jumlah Rusak
                                (Final)
                                :</label>
                            <input type="number" id="final_rusak_{{ $barang->id }}"
                                wire:model="kerusakanItems.{{ $barang->id }}"
                                class="w-20 text-sm border-gray-300 rounded-md" min="0"
                                max="{{ $barang->pivot->kuantitas }}">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" wire:click="$set('showKonfirmasiModal', false)"
                    class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                <button wire:click="finalisasiPengembalian" class="px-4 py-2 bg-primary text-white rounded">Selesaikan
                    Pengembalian</button>
            </div>
        </div>
    </div>
@endif

{{-- Modal Persetujuan Perpanjangan --}}
@if ($showPerpanjanganModal && $perpanjanganTransaksi)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Setujui Perpanjangan Peminjaman</h3>
            <p class="text-sm">Siswa: <strong>{{ $perpanjanganTransaksi->siswa->nama }}</strong></p>
            <p class="text-sm mb-4">Waktu Kembali Saat Ini:
                <strong>{{ \Carbon\Carbon::parse($perpanjanganTransaksi->waktu_kembali)->format('d M Y, H:i') }}</strong>
            </p>

            <div>
                <label for="waktu_kembali_baru" class="block text-sm font-medium text-gray-700">Set Waktu Kembali
                    Baru</label>
                <input type="datetime-local" id="waktu_kembali_baru" wire:model="waktu_kembali_baru"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('waktu_kembali_baru')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" wire:click="$set('showPerpanjanganModal', false)"
                    class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                <button wire:click="setujuiPerpanjangan" class="px-4 py-2 bg-primary text-white rounded">Setujui
                    Perpanjangan</button>
            </div>
        </div>
    </div>
@endif
</div>
