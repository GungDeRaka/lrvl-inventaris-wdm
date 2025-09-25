<?php

namespace App\Livewire\Dashboard;

use App\Models\Siswa;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
    public $transaksiIdUntukDikembalikan;
    public $transaksiTerpilih;
    public $search = '';

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
        // dd('method dipanggil');
        $this->validate([
            'nis' => 'required',
            'selectedBarangId' => 'required',
            'ruang_pemakaian' => 'required|string|min:3',
            'waktu_kembali' => 'required|date',
        ], [
            'nis.required' => 'NIS siswa wajib diisi.',
            'selectedBarangId.required' => 'Anda harus memilih barang.',
            'ruang_pemakaian.required' => 'Ruang pemakaian wajib diisi.',
            'waktu_kembali.required' => 'Waktu pengembalian wajib diisi.',
        ]);

        // Pastikan siswa dan barang benar-benar terpilih
        if (!$this->siswaDitemukan || !$this->selectedBarangId) {
            session()->flash('error', 'Data siswa atau barang tidak valid!');
            return;
        }

        $barang = Barang::find($this->selectedBarangId);
        if ($barang->jumlah_saat_ini <= 0) {
            // Kirim pesan error dan hentikan proses
            session()->flash('error', 'Stok barang "' . $barang->nama_barang . '" sedang habis dan tidak bisa dipinjam.');
            return; // Hentikan eksekusi method
        }
        try {
            // Langkah 2 & 3: Lakukan dalam satu transaksi database
            DB::transaction(function () {
                // Buat data transaksi baru
                Transaksi::create([
                    'siswa_id' => $this->siswaDitemukan->id,
                    'barang_id' => $this->selectedBarangId,
                    'user_id' => Auth::id(), // Ambil ID admin yang sedang login
                    'kuantitas' => 1, // Asumsi kuantitas selalu 1
                    'ruang_pemakaian' => $this->ruang_pemakaian,
                    'waktu_pinjam' => now(),
                    'waktu_kembali' => $this->waktu_kembali,
                    'status' => 'dipinjam',
                ]);

                // Kurangi stok barang
                $barang = Barang::find($this->selectedBarangId);
                $barang->decrement('jumlah_saat_ini');
            });

            // Langkah 4: Beri notifikasi dan reset form
            session()->flash('message', 'Data peminjaman berhasil disimpan!');
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
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

    // Metode untuk menyiapkan pengembalian barang
    public function konfirmasiPengembalian($id)
    {
        $this->transaksiIdUntukDikembalikan = $id;
        $this->transaksiTerpilih = Transaksi::find($id);
    }

    public function prosesPengembalian()
    {
        if ($this->transaksiIdUntukDikembalikan) {
            try {
                DB::transaction(function () {
                    $transaksi = Transaksi::find($this->transaksiIdUntukDikembalikan);

                    // 1. Ubah status transaksi
                    $transaksi->update(['status' => 'dikembalikan']);

                    // 2. Kembalikan stok barang (tambah 1)
                    $transaksi->barang->increment('jumlah_saat_ini');
                });

                session()->flash('message', 'Barang berhasil ditandai telah kembali!');
            } catch (\Exception $e) {
                session()->flash('error', 'Gagal memproses pengembalian barang.');
            }
        }

        // Reset properti untuk menutup modal
        $this->transaksiIdUntukDikembalikan = null;
        $this->transaksiTerpilih = null;
    }
     public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $totalUnitBarang = Barang::sum('jumlah_total');
        $totalDipinjam = Transaksi::where('status', 'dipinjam')->count();
        $totalRusak = Barang::sum('jumlah_rusak');
        $jatuhTempo = Transaksi::where('status', 'dipinjam')->where('waktu_kembali', '<', now())->count();

        // Data untuk tabel history
         $transaksis = Transaksi::with(['siswa', 'barang'])
            ->where(function($query) {
                // Cari di nama barang
                $query->whereHas('barang', function($subQuery) {
                    $subQuery->where('nama_barang', 'like', '%' . $this->search . '%');
                })
                // Atau cari di nama siswa
                ->orWhereHas('siswa', function($subQuery) {
                    $subQuery->where('nama', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.dashboard.index', [
            'transaksis' => $transaksis,
            'totalUnitBarang' => $totalUnitBarang, // Kirim data statistik ke view
            'totalDipinjam' => $totalDipinjam,
            'totalRusak' => $totalRusak,
            'jatuhTempo' => $jatuhTempo,
        ]);
    }
}
