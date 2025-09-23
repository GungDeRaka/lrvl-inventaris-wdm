<?php 

namespace App\Livewire\Barang;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Ruangan;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    // Properti untuk form modal
    public $showModal = false;
    public $kode_barang, $nama_barang, $kategori_id, $ruangan_id, $jumlah_total;
    
    // Aturan validasi
    protected $rules = [
        'kode_barang'   => 'required|string|unique:barangs,kode_barang',
        'nama_barang'   => 'required|string|min:3',
        'kategori_id'   => 'required|exists:kategoris,id',
        'ruangan_id'    => 'required|exists:ruangans,id',
        'jumlah_total'  => 'required|integer|min:1',
    ];

    // Method untuk membuka modal tambah barang
    public function openModal()
    {
        $this->resetInput();
        $this->showModal = true;
    }
    
    // Method untuk menutup modal
    public function closeModal()
    {
        $this->showModal = false;
    }
    
    // Method untuk mereset input form
    public function resetInput()
    {
        $this->kode_barang = '';
        $this->nama_barang = '';
        $this->kategori_id = '';
        $this->ruangan_id = '';
        $this->jumlah_total = '';
    }

    // Method untuk menyimpan barang baru
    public function simpanBarang()
    {
        $this->validate();

        Barang::create([
            'kode_barang' => $this->kode_barang,
            'nama_barang' => $this->nama_barang,
            'kategori_id' => $this->kategori_id,
            'ruangan_id' => $this->ruangan_id,
            'jumlah_total' => $this->jumlah_total,
            'jumlah_saat_ini' => $this->jumlah_total, // Stok awal sama dengan jumlah total
        ]);

        session()->flash('message', 'Barang baru berhasil ditambahkan.');
        $this->closeModal();
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