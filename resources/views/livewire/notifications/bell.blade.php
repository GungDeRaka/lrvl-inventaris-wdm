<div x-data="{ dropdownOpen: false }" class="relative">
    
    {{-- Tombol Lonceng (Bell Icon) --}}
    <button @click="dropdownOpen = !dropdownOpen" class="relative p-2 rounded-full text-white hover:bg-white/20 focus:outline-none">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        {{-- Badge Merah (Jumlah Belum Dibaca) --}}
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="dropdownOpen" 
         @click.away="dropdownOpen = false"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-50 border border-gray-100"
         style="display: none;">
        
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-700">Notifikasi</h3>
            <span class="text-xs text-gray-500">{{ $unreadCount }} Baru</span>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notif)
                <div class="relative group border-b border-gray-100 last:border-0 hover:bg-gray-50 transition duration-150 ease-in-out">
                    
                    {{-- Container Flex: Teks Notifikasi & Tombol Hapus --}}
                    <div class="flex items-start justify-between p-4">
                        
                        {{-- Bagian Kiri: Teks Notifikasi (Klik untuk tandai baca) --}}
                        <div class="flex-1 cursor-pointer pr-3" wire:click="markAsRead({{ $notif->id }})">
                            <p class="text-sm text-gray-800 {{ is_null($notif->read_at) ? 'font-bold' : '' }}">
                                {{ $notif->message }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $notif->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Bagian Kanan: Tombol Hapus (Sampah) --}}
                        <button wire:click.stop="deleteNotification({{ $notif->id }})" 
                                class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 focus:outline-none"
                                title="Hapus Notifikasi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>

                    {{-- Indikator Belum Dibaca (Garis Biru di Kiri) --}}
                    @if(is_null($notif->read_at))
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-gray-500 text-sm">
                    Tidak ada notifikasi baru.
                </div>
            @endforelse
        </div>
        
        {{-- Opsional: Footer Dropdown --}}
        @if($notifications->count() > 0)
        <div class="bg-gray-50 px-4 py-2 border-t border-gray-200 text-center">
            <a href="#" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
        </div>
        @endif
    </div>
</div>