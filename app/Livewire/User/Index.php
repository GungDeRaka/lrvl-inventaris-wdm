<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $name, $email, $password, $peran;

    protected $rules = [
        'name' => 'required|string|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'peran' => 'required|in:kepala_gudang,penjaga_gudang',
    ];

    public function openModal()
    {
        $this->reset(['name', 'email', 'password', 'peran']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function simpanPengguna()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'peran' => $this->peran,
        ]);

        session()->flash('message', 'Pengguna baru berhasil ditambahkan.');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.user.index', [
            'users' => User::latest()->paginate(10),
        ]);
    }
}