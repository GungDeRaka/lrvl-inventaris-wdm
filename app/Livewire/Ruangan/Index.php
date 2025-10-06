<?php

namespace App\Livewire\Ruangan;

use App\Models\Ruangan;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $ruangan_id;
    public $nama_ruangan;
    public $ruanganIdToDelete;
    public $search = '';

    public function openModal()
    {
        $this->reset(['ruangan_id', 'nama_ruangan']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function updatingSearch(){
        $this->resetPage();
    }

    public function simpanRuangan()
    {
        $validatedData = $this->validate([
            'nama_ruangan' => ['required', 'string', 'min:3', Rule::unique('ruangans')->ignore($this->ruangan_id)],
        ]);

        Ruangan::updateOrCreate(['id' => $this->ruangan_id], $validatedData);

        session()->flash('message', $this->ruangan_id ? 'Ruangan berhasil diperbarui.' : 'Ruangan baru berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $this->ruangan_id = $id;
        $this->nama_ruangan = $ruangan->nama_ruangan;
        $this->showModal = true;
    }

    public function konfirmasiHapus($id)
    {
        $this->ruanganIdToDelete = $id;
    }

    public function hapusRuangan()
    {
        $ruangan = Ruangan::find($this->ruanganIdToDelete);

        // Cek apakah ruangan sedang digunakan oleh barang
        if ($ruangan && $ruangan->barangs()->exists()) {
            session()->flash('error', 'Gagal! Ruangan ini sedang digunakan sebagai lokasi barang.');
            $this->ruanganIdToDelete = null;
            return;
        }

        if ($ruangan) {
            $ruangan->delete();
            session()->flash('message', 'Ruangan berhasil dihapus.');
        }

        $this->ruanganIdToDelete = null;
    }

    public function render()
    {
        return view('livewire.ruangan.index', [
            'ruangans' => Ruangan::where('nama_ruangan', 'like', '%'.$this->search.'%')->latest()->paginate(10),
        ]);
    }
}