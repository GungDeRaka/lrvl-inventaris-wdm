<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use App\Models\Kategori;
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
    public $barang_id;
    public $kode_barang, $nama_barang, $kategori_id, $ruangan_id, $jumlah_total;
    public $barangIdToDelete;
    public $barangNamaToDelete;

    public function openModal()
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function resetInput()
    {
        $this->barang_id = null;
        $this->kode_barang = '';
        $this->nama_barang = '';
        $this->kategori_id = '';
        $this->ruangan_id = '';
        $this->jumlah_total = '';
    }

    public function simpanBarang()
    {
        $validatedData = $this->validate([
            'kode_barang'   => ['required', 'string', Rule::unique('barangs', 'kode_barang')->ignore($this->barang_id)],
            'nama_barang'   => 'required|string|min:3',
            'kategori_id'   => 'required|exists:kategoris,id',
            'ruangan_id'    => 'required|exists:ruangans,id',
            'jumlah_total'  => 'required|integer|min:0',
        ], [
            'kode_barang.unique' => 'Kode barang ini sudah digunakan.'
        ]);

        if ($this->barang_id) {
            // Logic untuk UPDATE
            $barang = Barang::find($this->barang_id);
            $jumlahTotalLama = $barang->jumlah_total;
            // Update data barang dengan data tervalidasi
            $barang->update($validatedData);

            // Hitung selisih antara jumlah baru dan lama
            $selisih = $this->jumlah_total - $jumlahTotalLama;

            // Sesuaikan jumlah saat ini berdasarkan selisih
            // method increment() bisa menangani nilai positif (menambah) dan negatif (mengurangi)
            $barang->increment('jumlah_saat_ini', $selisih);

            session()->flash('message', 'Data barang berhasil diperbarui.');
        } else {
            // Logic untuk CREATE
            $validatedData['jumlah_saat_ini'] = $this->jumlah_total;
            Barang::create($validatedData);
            session()->flash('message', 'Barang baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $this->barang_id = $id;
        $this->kode_barang = $barang->kode_barang;
        $this->nama_barang = $barang->nama_barang;
        $this->kategori_id = $barang->kategori_id;
        $this->ruangan_id = $barang->ruangan_id;
        $this->jumlah_total = $barang->jumlah_total;
        $this->showModal = true;
    }

     public function konfirmasiHapus($id)
    {
        $barang = Barang::find($id);
        $this->barangIdToDelete = $id;
        $this->barangNamaToDelete = $barang->nama_barang;
    }

    public function hapusBarang()
    {
        $barang = Barang::find($this->barangIdToDelete);

        // Pengecekan penting: apakah barang sedang dipinjam?
        if ($barang && $barang->transaksis()->where('status', 'dipinjam')->exists()) {
            session()->flash('error', 'Gagal! Barang "' . $barang->nama_barang . '" tidak bisa dihapus karena sedang ada yang meminjam.');
            $this->barangIdToDelete = null; // Tutup modal
            return;
        }

        // Jika tidak sedang dipinjam, lanjutkan proses hapus
        if ($barang) {
            $barang->delete();
            session()->flash('message', 'Barang berhasil dihapus.');
        }

        $this->barangIdToDelete = null; // Tutup modal
    }

    public function render()
    {
        $barangs = Barang::with('kategori', 'ruangan')->latest()->paginate(10);
        $kategoris = Kategori::all();
        $ruangans = Ruangan::all();

        return view('livewire.barang.index', [
            'barangs' => $barangs,
            'kategoris' => $kategoris,
            'ruangans' => $ruangans,
        ]);
    }
}
