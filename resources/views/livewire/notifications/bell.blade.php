<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div x-data="{ dropdownOpen: false }" class="relative">
        <button @click="dropdownOpen = !dropdownOpen"
            class="relative flex items-center justify-center h-12 w-12 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 focus:outline-none">
            {{-- Ikon Lonceng --}}
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            {{-- Badge Jumlah Notifikasi --}}
            @if ($unreadCount > 0)
                <span
                    class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">{{ $unreadCount }}</span>
            @endif
        </button>

        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
            class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-20" x-transition>
            <div class="px-4 py-2 text-sm font-bold text-gray-700">Notifikasi</div>
            @forelse($notifications as $notification)
                <div
                    class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ !$notification->read_at ? 'font-bold' : '' }}">
                    {{ $notification->message }}
                    <div class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <div class="px-4 py-2 text-sm text-gray-500">Tidak ada notifikasi baru.</div>
            @endforelse
            @if ($unreadCount > 0)
                <div class="border-t mt-1">
                    <button wire:click="markAllAsRead"
                        class="block w-full text-center px-4 py-2 text-sm text-indigo-600 hover:bg-gray-100">Tandai
                        semua sudah dibaca</button>
                </div>
            @endif
        </div>
    </div>
</div>
