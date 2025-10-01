<?php

namespace App\Livewire\Siswa;

use App\Models\Barang;
use App\Models\Transaksi;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.siswa')] // Kita akan buat layout siswa nanti
class Dashboard extends Component
{
    public $showRequestModal = false;
    public $searchBarang = '';
    public $barangDitemukan = [];
    public $selectedBarangId;
    public $selectedBarangNama;
    public $waktu_pinjam, $waktu_kembali, $ruang_pemakaian;

    public function updatedSearchBarang($value)
    {
        if (strlen($this->searchBarang) >= 2) {
            $this->barangDitemukan = Barang::where('nama_barang', 'like', '%' . $value . '%')
                ->where('jumlah_saat_ini', '>', 0)
                ->limit(5)->get();
        } else {
            $this->barangDitemukan = [];
        }
    }

    public function selectBarang($id, $nama)
    {
        $this->selectedBarangId = $id;
        $this->selectedBarangNama = $nama;
        $this->barangDitemukan = [];
        $this->searchBarang = '';
    }

    public function ajukanPeminjaman()
    {
        $validated = $this->validate([
            'selectedBarangId' => 'required',
            'ruang_pemakaian' => 'required|string|min:3',
            'waktu_pinjam' => 'required|date|after_or_equal:now',
            'waktu_kembali' => 'required|date|after:waktu_pinjam',
        ]);

        Transaksi::create([
            'siswa_id' => auth()->guard('siswa')->id(),
            'barang_id' => $this->selectedBarangId,
            'user_id' => 1, // Diisi admin saat approval, beri nilai default sementara
            'kuantitas' => 1,
            'ruang_pemakaian' => $this->ruang_pemakaian,
            'waktu_pinjam' => $this->waktu_pinjam,
            'waktu_kembali' => $this->waktu_kembali,
            'status' => 'diajukan',
        ]);

        session()->flash('message', 'Permintaan peminjaman berhasil diajukan.');
        $this->showRequestModal = false;
    }

    public function render()
    {
        $riwayat = Transaksi::with('barang')
            ->where('siswa_id', auth()->guard('siswa')->id())
            ->latest()->get();

        return view('livewire.siswa.dashboard', ['riwayat' => $riwayat]);
    }
}