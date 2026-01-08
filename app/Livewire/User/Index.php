<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

#[Title('Manajemen Admin Gudang')]
#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $user_id;
    // Tambahkan properti $no_hp
    public $name, $email, $no_hp, $password, $peran; 
    
    public $showPasswordModal = false;
    public $passwordUserId;
    public $passwordUserName;
    public $new_password;
    public $userIdToDelete;
    public $userNameToDelete;
    public $search = '';

    // Rules global (opsional, karena kita override di simpanPengguna)
    protected $rules = [
        'name' => 'required|string|min:3',
        'email' => 'required|email|unique:users,email',
        'no_hp' => 'nullable|string|max:15', // Validasi No HP
        'password' => 'required|string|min:8',
        'peran' => 'required|in:kepala_gudang,penjaga_gudang,bendahara',
    ];

    public function openModal()
    {
        // Reset no_hp juga
        $this->reset(['name', 'email', 'no_hp', 'password', 'peran', 'user_id']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showPasswordModal = false;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function simpanPengguna()
    {
        $rules = [
            'name' => 'required|string|min:3',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user_id)],
            // Tambahkan validasi No HP
            'no_hp' => 'nullable|string|max:15', 
            'peran' => 'required|in:kepala_gudang,penjaga_gudang,bendahara',
        ];

        if (!$this->user_id) {
            $rules['password'] = 'required|string|min:8';
        }

        $validatedData = $this->validate($rules);

        // Jika No HP kosong, set null agar rapi di DB
        if(empty($validatedData['no_hp'])) {
            $validatedData['no_hp'] = null;
        }

        if ($this->user_id) {
            // Update User
            $user = User::find($this->user_id);
            $user->update($validatedData);
            session()->flash('message', 'Data pengguna berhasil diperbarui.');
        } else {
            // Create User
            $validatedData['password'] = Hash::make($this->password);
            User::create($validatedData);
            session()->flash('message', 'Pengguna baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->no_hp = $user->no_hp; // Ambil data No HP
        $this->peran = $user->peran;
        $this->password = ''; 
        $this->showModal = true;
    }

    // Method ganti password dll tetap sama...
    public function openPasswordModal($id)
    {
        $user = User::findOrFail($id);
        $this->passwordUserId = $id;
        $this->passwordUserName = $user->name;
        $this->new_password = '';
        $this->showPasswordModal = true;
    }

    public function updatePassword()
    {
        $this->validate(['new_password' => 'required|string|min:8']);

        $user = User::find($this->passwordUserId);
        $user->update([
            'password' => Hash::make($this->new_password)
        ]);

        session()->flash('message', 'Password untuk ' . $this->passwordUserName . ' berhasil diperbarui.');
        $this->closeModal();
    }

    public function konfirmasiHapus($id)
    {
        if ($id == Auth::id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }
        $user = User::find($id);
        $this->userIdToDelete = $id;
        $this->userNameToDelete = $user->name;
    }

    public function hapusPengguna()
    {
        $user = User::find($this->userIdToDelete);
        if ($user) {
            $user->delete();
            session()->flash('message', 'Pengguna berhasil dihapus.');
        }
        $this->userIdToDelete = null;
    }

    public function render()
    {
        return view('livewire.user.index', [
            'users' => User::where('name','like', '%'. $this->search . '%')->latest()->paginate(10),
        ]);
    }
}