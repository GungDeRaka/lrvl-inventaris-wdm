<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class Bell extends Component
{
    public $notifications;
    public $unreadCount = 0;

    // Listener agar komponen ini bisa di-refresh dari script lain jika perlu
    protected $listeners = ['refreshNotifications' => '$refresh'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        // Ambil notifikasi milik user yang sedang login, urutkan dari yang terbaru
        $this->notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->take(10) // Batasi 10 terakhir agar tidak berat, atau sesuaikan
            ->get();

        $this->unreadCount = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }

    // --- FITUR BARU: HAPUS NOTIFIKASI ---
    public function deleteNotification($id)
    {
        $notification = Notification::find($id);

        if ($notification && $notification->user_id == Auth::id()) {
            $notification->delete();
            
            // Reload data setelah menghapus
            $this->loadNotifications();
            
            // Opsional: Kirim notifikasi toast kecil bahwa berhasil dihapus
            $this->dispatch('notify', message: 'Notifikasi dihapus.', type: 'success');
        }
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        if ($notification && $notification->user_id == Auth::id()) {
            $notification->update(['read_at' => now()]);
            $this->loadNotifications(); // Reload agar status berubah
        }
    }

    public function render()
    {
        return view('livewire.notifications.bell');
    }
}