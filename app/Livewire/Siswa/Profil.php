<?php

namespace App\Livewire\Siswa;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.siswa')]
class Profil extends Component
{
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    public function updatePassword()
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string', 'current_password:siswa'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini tidak cocok.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Ambil user yang sedang login ke dalam variabel
        /** @var \App\Models\Siswa $siswa */
        $siswa = auth()->guard('siswa')->user();

        // Panggil update() dari variabel tersebut
        $siswa->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset();
        session()->flash('message', 'Password berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.siswa.profil');
    }
}
