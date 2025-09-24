<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class Bell extends Component
{
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function render()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();

        $unreadCount = Notification::where('user_id', Auth::id())->whereNull('read_at')->count();

        return view('livewire.notifications.bell', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
