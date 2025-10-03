<?php

namespace App\Livewire\Dashboard;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Siswa;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    // Properti untuk Modal Peminjaman Admin
    public $showTransactionModal = false;
    public $nis = '', $siswaDitemukan;
    public $searchBarang = '', $barangDitemukan = [];
    public $keranjang = [];
    public $ruang_pemakaian = '', $waktu_kembali;

    // Properti untuk Modal Pengembalian
    public $transaksiIdUntukDikembalikan, $transaksiTerpilih;

    // Properti untuk Modal Detail Siswa
    public $siswaDetail = null;

    // Properti untuk Modal Laporan
    public $showReportModal = false;
    public $tanggal_mulai, $tanggal_akhir;

    // Properti untuk Pencarian Riwayat
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Fungsi untuk Form Peminjaman Admin
    public function openTransactionModal()
    {
        $this->resetForm();
        $this->showTransactionModal = true;
    }

    public function closeTransactionModal()
    {
        $this->showTransactionModal = false;
    }

    public function resetForm()
    {
        $this->reset(['nis', 'siswaDitemukan', 'searchBarang', 'barangDitemukan', 'keranjang', 'ruang_pemakaian', 'waktu_kembali']);
    }

    public function updatedNis($value)
    {
        $this->siswaDitemukan = Siswa::where('nis', $value)->first();
    }

    public function updatedSearchBarang($value)
    {
        if (strlen($this->searchBarang) >= 2) {
            $this->barangDitemukan = Barang::where('nama_barang', 'like', '%' . $value . '%')
                ->limit(5)->get();
        } else {
            $this->barangDitemukan = [];
        }
    }

    public function tambahKeKeranjang($id, $nama)
    {
        foreach ($this->keranjang as $item) {
            if ($item['id'] == $id) return;
        }
        $this->keranjang[] = ['id' => $id, 'nama' => $nama];
        $this->barangDitemukan = [];
        $this->searchBarang = '';
    }

    public function hapusDariKeranjang($index)
    {
        unset($this->keranjang[$index]);
        $this->keranjang = array_values($this->keranjang);
    }

    public function simpanPeminjaman()
    {
        $this->validate([
            'siswaDitemukan' => 'required',
            'keranjang' => 'required|array|min:1',
            'ruang_pemakaian' => 'required|string|min:3',
            'waktu_kembali' => 'required|date|after:now',
        ]);

        try {
            DB::transaction(function () {
                $transaksi = Transaksi::create([
                    'siswa_id' => $this->siswaDitemukan->id,
                    'user_id' => Auth::id(),
                    'ruang_pemakaian' => $this->ruang_pemakaian,
                    'waktu_pinjam' => now(),
                    'waktu_kembali' => $this->waktu_kembali,
                    'status' => 'dipinjam',
                ]);

                foreach ($this->keranjang as $item) {
                    $transaksi->barangs()->attach($item['id'], ['kuantitas' => 1]);
                    Barang::find($item['id'])->decrement('jumlah_saat_ini');
                }
            });
            session()->flash('message', 'Peminjaman berhasil dicatat.');
            $this->closeTransactionModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    // Fungsi untuk Persetujuan Booking Siswa
    public function setujuiPermintaan($id)
    {
        DB::transaction(function () use ($id) {
            $transaksi = Transaksi::findOrFail($id);
            $semuaStokTersedia = true;

            foreach ($transaksi->barangs as $barang) {
                if ($barang->jumlah_saat_ini < $barang->pivot->kuantitas) {
                    $semuaStokTersedia = false;
                    break;
                }
            }

            if (!$semuaStokTersedia) {
                session()->flash('error', 'Gagal! Stok salah satu barang tidak mencukupi.');
                $transaksi->update(['status' => 'ditolak', 'user_id' => Auth::id()]);
                return;
            }

            $transaksi->update(['status' => 'disetujui', 'user_id' => Auth::id()]);
            foreach ($transaksi->barangs as $barang) {
                $barang->decrement('jumlah_saat_ini', $barang->pivot->kuantitas);
            }
            session()->flash('message', 'Permintaan berhasil disetujui.');
        });
    }

    public function tolakPermintaan($id)
    {
        $transaksi = Transaksi::find($id);
        if ($transaksi) {
            $transaksi->update(['status' => 'ditolak', 'user_id' => Auth::id()]);
            session()->flash('message', 'Permintaan telah ditolak.');
        }
    }

    public function konfirmasiAmbil($id)
    {
        $transaksi = Transaksi::find($id);
        if ($transaksi && $transaksi->status == 'disetujui') {
            $transaksi->update(['status' => 'dipinjam']);
            session()->flash('message', 'Peminjaman telah dikonfirmasi diambil oleh siswa.');
        } else {
            session()->flash('error', 'Gagal mengkonfirmasi peminjaman.');
        }
    }

    // Fungsi untuk Pengembalian
    public function konfirmasiPengembalian($id)
    {
        $this->transaksiIdUntukDikembalikan = $id;
        $this->transaksiTerpilih = Transaksi::with('barangs', 'siswa')->find($id);
    }

    public function prosesPengembalian()
    {
        if ($this->transaksiIdUntukDikembalikan) {
            try {
                DB::transaction(function () {
                    $transaksi = Transaksi::find($this->transaksiIdUntukDikembalikan);
                    $transaksi->update(['status' => 'dikembalikan']);
                    foreach ($transaksi->barangs as $barang) {
                        $barang->increment('jumlah_saat_ini', $barang->pivot->kuantitas);
                    }
                });
                session()->flash('message', 'Barang berhasil ditandai telah kembali!');
            } catch (\Exception $e) {
                session()->flash('error', 'Gagal memproses pengembalian barang.');
            }
        }
        $this->transaksiIdUntukDikembalikan = null;
        $this->transaksiTerpilih = null;
    }

    // Fungsi untuk Modal Detail Siswa
    public function showSiswaDetail($siswaId)
    {
        $this->siswaDetail = Siswa::find($siswaId);
    }

    public function closeModal()
    {
        $this->siswaDetail = null;
    }

    public function openReportModal()
    {
        // Reset tanggal setiap kali modal dibuka
        $this->reset(['tanggal_mulai', 'tanggal_akhir']);
        $this->showReportModal = true;
    }

    public function closeReportModal()
    {
        $this->showReportModal = false;
    }

    // Fungsi Render Utama
    public function render()
    {
        $jatuhTempo = Transaksi::where('status', 'dipinjam')->where('waktu_kembali', '<', now())->count();
        $totalDipinjam = DB::table('barang_transaksi')
            ->join('transaksis', 'barang_transaksi.transaksi_id', '=', 'transaksis.id')
            ->whereIn('transaksis.status', ['dipinjam', 'disetujui'])
            ->sum('barang_transaksi.kuantitas');
        $permintaanMasuk = Transaksi::with(['siswa', 'barangs'])
            ->where('status', 'diajukan')->latest()->get();

        $transaksis = Transaksi::with(['siswa', 'barangs.ruangan'])
            ->where(function ($query) {
                $query->whereHas('barangs', function ($subQuery) {
                    $subQuery->where('nama_barang', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('siswa', function ($subQuery) {
                        $subQuery->where('nama', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()->paginate(10);

        return view('livewire.dashboard.index', [
            'transaksis' => $transaksis,
            'permintaanMasuk' => $permintaanMasuk,
            'totalUnitBarang' => Barang::sum('jumlah_total'),
            'totalDipinjam' => $totalDipinjam,
            'totalRusak' => Barang::sum('jumlah_rusak'),
            'jatuhTempo' => $jatuhTempo,
            'ruangans' => Ruangan::all(),
        ]);
    }
}
