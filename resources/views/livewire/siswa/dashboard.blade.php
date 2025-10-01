<div>
    {{-- Tombol Aksi --}}
    <div class="mb-6">
        <button wire:click="$set('showRequestModal', true)"
            class="w-full bg-primary text-white font-bold py-3 px-6 rounded-lg shadow-md">
            Buat Permintaan Peminjaman
        </button>
    </div>

    {{-- Riwayat Peminjaman --}}
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Riwayat Permintaan Anda</h2>
    <div class="space-y-4">
        @forelse($riwayat as $item)
            @php
                // Tentukan class untuk border kiri berdasarkan status
                $borderColorClass = match ($item->status) {
                    'dipinjam', 'disetujui' => 'border-blue-500',
                    'dikembalikan' => 'border-green-500',
                    'ditolak' => 'border-red-500',
                    default => 'border-gray-400',
                };

                // Tentukan class untuk badge status
                $statusBadgeClass = match ($item->status) {
                    'dipinjam', 'disetujui' => 'bg-blue-100 text-blue-800',
                    'dikembalikan' => 'bg-green-100 text-green-800',
                    'ditolak' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-800',
                };
            @endphp

            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 {{ $borderColorClass }}">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-gray-800">{{ $item->barang->nama_barang }}</span>
                    <span class="text-sm font-semibold px-2 py-1 rounded-full {{ $statusBadgeClass }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    Booking: {{ \Carbon\Carbon::parse($item->waktu_pinjam)->format('d M Y, H:i') }} -
                    {{ \Carbon\Carbon::parse($item->waktu_kembali)->format('H:i') }}
                </p>
            </div>
        @empty
            <p class="text-gray-500">Anda belum memiliki riwayat permintaan.</p>
        @endforelse
    </div>

    {{-- Modal Form Permintaan --}}
    @if ($showRequestModal)
        {{-- (Kode modal akan ditambahkan di sini, mirip dengan modal admin) --}}
    @endif
</div>
