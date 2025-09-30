<?php

namespace App\Livewire\Kategori;

use App\Models\Kategori;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $kategori_id;
    public $nama_kategori;
    public $kategoriIdToDelete;

    public function openModal()
    {
        $this->reset(['kategori_id', 'nama_kategori']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function simpanKategori()
    {
        $validatedData = $this->validate([
            'nama_kategori' => ['required', 'string', 'min:3', Rule::unique('kategoris')->ignore($this->kategori_id)],
        ]);

        Kategori::updateOrCreate(['id' => $this->kategori_id], $validatedData);

        session()->flash('message', $this->kategori_id ? 'Kategori berhasil diperbarui.' : 'Kategori baru berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        $this->kategori_id = $id;
        $this->nama_kategori = $kategori->nama_kategori;
        $this->showModal = true;
    }

    public function konfirmasiHapus($id)
    {
        $this->kategoriIdToDelete = $id;
    }

    public function hapusKategori()
    {
        $kategori = Kategori::find($this->kategoriIdToDelete);

        // Cek apakah kategori sedang digunakan oleh barang
        if ($kategori && $kategori->barangs()->exists()) {
            session()->flash('error', 'Gagal! Kategori ini sedang digunakan oleh beberapa barang.');
            $this->kategoriIdToDelete = null;
            return;
        }

        if ($kategori) {
            $kategori->delete();
            session()->flash('message', 'Kategori berhasil dihapus.');
        }

        $this->kategoriIdToDelete = null;
    }

    public function render()
    {
        return view('livewire.kategori.index', [
            'kategoris' => Kategori::latest()->paginate(10),
        ]);
    }
}
