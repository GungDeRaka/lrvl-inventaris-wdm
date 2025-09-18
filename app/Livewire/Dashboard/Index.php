<?php

namespace App\Livewire\Dashboard;

use App\Models\Siswa;
use App\Models\Barang;
use App\Models\Transaksi;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Properti untuk form peminjaman
    public $nis = '';
    public $siswaDitemukan; // Untuk menampung data siswa jika ditemukan

    public $searchBarang = '';
    public $barangDitemukan = []; // Untuk menampung hasil pencarian barang
    public $selectedBarangId;
    public $selectedBarangNama;

    public $ruang_pemakaian = '';
    public $waktu_kembali;

    // Fungsi yang akan dijalankan saat properti $nis diperbarui
    public function updatedNis($value)
    {
        if (!empty($value)) {
            $this->siswaDitemukan = Siswa::where('nis', $value)->first();
        } else {
            $this->siswaDitemukan = null;
        }
    }

    // Fungsi yang akan dijalankan saat properti $searchBarang diperbarui
    public function updatedSearchBarang($value)
    {
        if (strlen($this->searchBarang) >= 2) {
            $this->barangDitemukan = Barang::where('nama_barang', 'like', '%' . $value . '%')
                ->orWhere('kode_barang', 'like', '%' . $value . '%')
                ->where('jumlah_saat_ini', '>', 0) // Hanya cari barang yang stoknya ada
                ->limit(5)
                ->get();
        } else {
            $this->barangDitemukan = [];
        }
    }

    // Fungsi untuk memilih barang dari hasil pencarian
    public function selectBarang($barangId, $barangNama)
    {
        $this->selectedBarangId = $barangId;
        $this->selectedBarangNama = $barangNama;
        $this->barangDitemukan = []; // Sembunyikan hasil pencarian
        $this->searchBarang = ''; // Kosongkan search bar
    }

    // Metode untuk menyimpan data peminjaman
    public function simpanPeminjaman()
    {
        // Logika penyimpanan akan kita tambahkan di sini nanti
        // Validasi, simpan ke database, dll.

        session()->flash('message', 'Peminjaman berhasil disimpan!');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->nis = '';
        $this->siswaDitemukan = null;
        $this->searchBarang = '';
        $this->barangDitemukan = [];
        $this->selectedBarangId = null;
        $this->selectedBarangNama = null;
        $this->ruang_pemakaian = '';
        $this->waktu_kembali = null;
    }


    public function render()
    {
        $transaksis = Transaksi::with(['siswa', 'barang'])
            ->latest()
            ->paginate(10);

         return view('livewire.dashboard.index', [
            'transaksis' => $transaksis
        ]);
    }
}
