<?php

namespace App\Livewire\Siswa;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.siswa')]
class Dashboard extends Component
{
    public $showRequestModal = false;
    public $searchBarang = '';
    public $barangDitemukan = [];

    // Properti untuk form
    public $waktu_pinjam, $waktu_kembali, $ruang_pemakaian;

    // Keranjang untuk menampung barang yang akan dipinjam
    public $keranjang = [];

    public function updatedSearchBarang($value)
    {
        if (strlen($this->searchBarang) >= 2) {
            $this->barangDitemukan = Barang::where('nama_barang', 'like', '%' . $value . '%')->limit(5)->get();
        } else {
            $this->barangDitemukan = [];
        }
    }

    public function tambahKeKeranjang($id, $nama)
    {
        // Cek agar barang yang sama tidak masuk dua kali
        foreach ($this->keranjang as $item) {
            if ($item['id'] == $id) {
                return; // Jika sudah ada, hentikan fungsi
            }
        }

        // Tambahkan barang ke keranjang
        $this->keranjang[] = ['id' => $id, 'nama' => $nama];
        $this->barangDitemukan = [];
        $this->searchBarang = '';
    }

    public function hapusDariKeranjang($index)
    {
        unset($this->keranjang[$index]);
        $this->keranjang = array_values($this->keranjang); // Re-index array
    }

    public function ajukanPeminjaman()
    {
        $validated = $this->validate([
            'keranjang' => 'required|array|min:1',
            'ruang_pemakaian' => 'required|string|min:3',
            'waktu_pinjam' => 'required|date|after_or_equal:now',
            'waktu_kembali' => 'required|date|after:waktu_pinjam',
        ], [
            'keranjang.required' => 'Keranjang peminjaman tidak boleh kosong.',
        ]);

        try {
            DB::transaction(function () {
                // 1. Buat satu record transaksi utama
                $transaksi = Transaksi::create([
                    'siswa_id' => auth()->guard('siswa')->id(),
                    'user_id' => 1, // Akan diisi admin saat approval
                    'ruang_pemakaian' => $this->ruang_pemakaian,
                    'waktu_pinjam' => $this->waktu_pinjam,
                    'waktu_kembali' => $this->waktu_kembali,
                    'status' => 'diajukan',
                ]);

                // 2. Lampirkan semua barang dari keranjang ke transaksi
                foreach ($this->keranjang as $item) {
                    $transaksi->barangs()->attach($item['id'], ['kuantitas' => 1]);
                }
            });

            session()->flash('message', 'Permintaan peminjaman berhasil diajukan.');
            $this->showRequestModal = false;
            $this->keranjang = []; // Kosongkan keranjang

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengajukan permintaan.');
        }
    }

    public function render()
    {
        $riwayat = Transaksi::with('barangs')
            ->where('siswa_id', auth()->guard('siswa')->id())
            ->latest()->get();

        return view('livewire.siswa.dashboard', ['riwayat' => $riwayat, 'ruangans' => Ruangan::all()]);
    }
}
