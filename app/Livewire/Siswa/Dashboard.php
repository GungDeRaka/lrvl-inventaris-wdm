<?php

namespace App\Livewire\Siswa;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
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

    // PROPERTI UNTUK MENGATUR TAB
    public $activeTab = 'riwayat';

    public $showReturnModal = false;
    public $returnTransaksi;
    public $kerusakanDilaporkan = [];

    public function updatedSearchBarang($value)
    {
        if (strlen($this->searchBarang) >= 2) {
            // Tambahkan with('ruangan')
            $this->barangDitemukan = Barang::with('ruangan')
                ->where('nama_barang', 'like', '%' . $value . '%')
                ->limit(5)->get();
        } else {
            $this->barangDitemukan = [];
        }
    }

    public function tambahKeKeranjang($id, $nama, $ruanganAsal)
    {
        $barang = Barang::find($id);
        if ($barang->jumlah_saat_ini <= 0) {
            session()->flash('error', 'Stok barang ' . $nama . ' sudah habis.');
            return;
        }

        // Cek apakah barang sudah ada di keranjang
        foreach ($this->keranjang as $index => $item) {
            if ($item['id'] == $id) {
                // Jika kuantitas di keranjang sudah sama dengan stok, jangan tambah lagi
                if ($this->keranjang[$index]['kuantitas'] >= $barang->jumlah_saat_ini) {
                    return;
                }
                // Tambah kuantitas jika barang sudah ada
                $this->keranjang[$index]['kuantitas']++;
                return;
            }
        }

        // Tambahkan barang baru ke keranjang dengan kuantitas 1
        $this->keranjang[] = [
            'id' => $id,
            'nama' => $nama,
            'asal' => $ruanganAsal,
            'kuantitas' => 1, // Kuantitas awal
            'stok_tersedia' => $barang->jumlah_saat_ini, // Simpan info stok
        ];

        $this->barangDitemukan = []; // Mengosongkan hasil pencarian
        $this->searchBarang = '';
    }

    public function incrementKuantitas($index)
    {
        // Cek agar kuantitas tidak melebihi stok
        if ($this->keranjang[$index]['kuantitas'] < $this->keranjang[$index]['stok_tersedia']) {
            $this->keranjang[$index]['kuantitas']++;
        }
    }

    public function decrementKuantitas($index)
    {
        if ($this->keranjang[$index]['kuantitas'] > 1) {
            $this->keranjang[$index]['kuantitas']--;
        }
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
                    $transaksi->barangs()->attach($item['id'], ['kuantitas' => $item['kuantitas']]);

                    // HAPUS BARIS DI BAWAH INI
                    // Barang::find($item['id'])->decrement('jumlah_saat_ini', $item['kuantitas']);
                }
            });

            session()->flash('message', 'Permintaan peminjaman berhasil diajukan.');
            $this->showRequestModal = false;
            $this->keranjang = []; // Kosongkan keranjang

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengajukan permintaan.');
        }
    }

    public function batalkanPermintaan($id)
    {
        $transaksi = Transaksi::where('id', $id)
            ->where('siswa_id', auth()->guard('siswa')->id())
            ->first();

        // Pastikan siswa hanya bisa membatalkan miliknya sendiri
        if (!$transaksi) {
            return;
        }

        // Siswa bisa batal selama statusnya 'diajukan' atau 'disetujui'
        if ($transaksi->status == 'diajukan' || $transaksi->status == 'disetujui') {
            DB::transaction(function () use ($transaksi) {
                // Jika statusnya sudah disetujui, kembalikan stok
                if ($transaksi->status == 'disetujui') {
                    foreach ($transaksi->barangs as $barang) {
                        $barang->increment('jumlah_saat_ini', $barang->pivot->kuantitas);
                    }
                }

                // Ubah status dan beri alasan
                $transaksi->update([
                    'status' => 'ditolak',
                    'alasan_penolakan' => 'Dibatalkan oleh siswa.'
                ]);
            });

            session()->flash('message', 'Permintaan peminjaman berhasil dibatalkan.');
        }
    }

    public function setActiveTab($tabName)
    {
        $this->activeTab = $tabName;
    }

    #[Computed]
    public function asalRuangan()
    {
        if (empty($this->keranjang)) {
            return collect();
        }
        $barangIds = collect($this->keranjang)->pluck('id');
        return Barang::with('ruangan')
            ->whereIn('id', $barangIds)
            ->get()
            ->groupBy('ruangan.nama_ruangan');
    }

    public function bukaModalPengembalian($id)
    {
        $this->returnTransaksi = Transaksi::with('barangs')->find($id);
        $this->kerusakanDilaporkan = [];
        foreach ($this->returnTransaksi->barangs as $barang) {
            $this->kerusakanDilaporkan[$barang->id] = 0;
        }
        $this->showReturnModal = true;
    }

    public function ajukanPengembalian()
    {
        DB::transaction(function () {
            $this->returnTransaksi->update(['status' => 'menunggu-konfirmasi']);

            foreach ($this->kerusakanDilaporkan as $barangId => $jumlahRusak) {
                if ($jumlahRusak > 0) {
                    // Update pivot table dengan jumlah yang rusak
                    $this->returnTransaksi->barangs()->updateExistingPivot($barangId, [
                        'jumlah_rusak_dilaporkan' => $jumlahRusak
                    ]);
                }
            }
        });

        $this->showReturnModal = false;
        session()->flash('message', 'Laporan pengembalian berhasil diajukan dan menunggu konfirmasi admin.');
    }
    public function render()
    {
        $riwayat = Transaksi::with('barangs')
            ->where('siswa_id', auth()->guard('siswa')->id())
            ->latest()->get();
        $ruangansDenganBarang = Ruangan::with(['barangs' => function ($query) {
            $query->where('jumlah_saat_ini', '>', 0);
        }])->get();

        return view('livewire.siswa.dashboard', [
            'riwayat' => $riwayat,
            'ruangans' => Ruangan::all(),
            'ruangansDenganBarang' => $ruangansDenganBarang,
        ]);
    }
}
