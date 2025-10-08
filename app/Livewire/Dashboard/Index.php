<?php

namespace App\Livewire\Dashboard;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Siswa;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
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
    public $kerusakanItems = [];

    // Properti untuk Modal Detail Siswa
    public $siswaDetail = null;

    // Properti untuk Modal Laporan
    public $showReportModal = false;
    public $tanggal_mulai, $tanggal_akhir;

    // Properti untuk Pencarian Riwayat
    public $search = '';

    // Properti untuk Modal Tolak Permintaan
    public $showTolakModal = false;
    public $transaksiIdToTolak;
    public $alasan_penolakan = '';

    public $showKonfirmasiModal = false;
    public $konfirmasiTransaksi;

    // properti untuk modal perpanjangan
    public $showPerpanjanganModal = false;
    public $perpanjanganTransaksi;
    public $waktu_kembali_baru;



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
            // Tambahkan with('ruangan') di sini
            $this->barangDitemukan = Barang::with('ruangan')
                ->where(function ($query) use ($value) {
                    $query->where('nama_barang', 'like', '%' . $value . '%')
                        ->orWhere('kode_barang', 'like', '%' . $value . '%');
                })
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
        // Cek agar barang yang sama tidak masuk dua kali
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

        // Tambahkan barang beserta ruangan asalnya ke keranjang
        // Tambahkan barang baru ke keranjang dengan kuantitas 1
        $this->keranjang[] = [
            'id' => $id,
            'nama' => $nama,
            'asal' => $ruanganAsal,
            'kuantitas' => 1, // Kuantitas awal
            'stok_tersedia' => $barang->jumlah_saat_ini, // Simpan info stok
        ];

        $this->barangDitemukan = [];
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

        $asalRuanganNames = Barang::with('ruangan')
            ->whereIn('id', collect($this->keranjang)->pluck('id'))
            ->get()
            ->pluck('ruangan.nama_ruangan')
            ->unique();

        if ($asalRuanganNames->contains($this->ruang_pemakaian)) {
            // Jika sama, tambahkan error dan hentikan proses
            $this->addError('ruang_pemakaian', 'Ruang pemakaian tidak boleh sama dengan ruang asal barang.');
            return;
        }

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
                    $transaksi->barangs()->attach($item['id'], ['kuantitas' => $item['kuantitas']]);
                    Barang::find($item['id'])->decrement('jumlah_saat_ini', $item['kuantitas']);
                }
            });
            session()->flash('message', 'Peminjaman berhasil dicatat.');
            $this->closeTransactionModal();
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function batalkanPeminjaman($id)
    {
        $transaksi = Transaksi::find($id);

        // Pastikan hanya transaksi yang sudah disetujui yang bisa dibatalkan
        if ($transaksi && $transaksi->status == 'disetujui') {
            DB::transaction(function () use ($transaksi) {
                // 1. Ubah status dan beri alasan
                $transaksi->update([
                    'status' => 'ditolak',
                    'alasan_penolakan' => 'Dibatalkan oleh admin.'
                ]);

                // 2. Kembalikan stok barang yang sudah di-booking
                foreach ($transaksi->barangs as $barang) {
                    $barang->increment('jumlah_saat_ini', $barang->pivot->kuantitas);
                }
            });
            session()->flash('message', 'Booking peminjaman berhasil dibatalkan.');
        }
    }

    // Fungsi untuk Persetujuan Booking Siswa
    public function setujuiPermintaan($id)
    {
        DB::transaction(function () use ($id) {
            $transaksi = Transaksi::findOrFail($id);

            // Logika pengecekan stok
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

            // Update status menjadi 'disetujui' dan KURANGI STOK
            $transaksi->update(['status' => 'disetujui', 'user_id' => Auth::id()]);
            foreach ($transaksi->barangs as $barang) {
                $barang->decrement('jumlah_saat_ini', $barang->pivot->kuantitas);
            }

            session()->flash('message', 'Permintaan berhasil disetujui.');
        });
    }

    public function tolakPermintaan($id)
    {
        $this->reset('alasan_penolakan');
        $this->transaksiIdToTolak = $id;
        $this->showTolakModal = true;
    }

    public function prosesPenolakan()
    {
        $this->validate(['alasan_penolakan' => 'required|string|min:5']);

        $transaksi = Transaksi::find($this->transaksiIdToTolak);
        if ($transaksi) {
            // HANYA update status dan alasan, TIDAK ADA PENGURANGAN STOK
            $transaksi->update([
                'status' => 'ditolak',
                'user_id' => Auth::id(),
                'alasan_penolakan' => $this->alasan_penolakan
            ]);
            session()->flash('message', 'Permintaan telah ditolak dengan alasan.');
        }
        $this->showTolakModal = false;
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
        $transaksi = Transaksi::with('barangs')->find($id);
        $this->transaksiTerpilih = $transaksi;

        // Inisialisasi array kerusakan
        $this->kerusakanItems = [];
        foreach ($transaksi->barangs as $barang) {
            $this->kerusakanItems[$barang->id] = 0; // Set default jumlah rusak ke 0
        }
    }

    public function prosesPengembalian()
    {
        if (!$this->transaksiTerpilih) return;

        $adaKerusakan = false;
        foreach ($this->kerusakanItems as $jumlahRusak) {
            if ($jumlahRusak > 0) {
                $adaKerusakan = true;
                break;
            }
        }

        DB::transaction(function () use ($adaKerusakan) {
            $transaksi = $this->transaksiTerpilih;
            $transaksi->update(['status' => 'dikembalikan']);

            foreach ($transaksi->barangs as $barang) {
                $kuantitasPinjam = $barang->pivot->kuantitas;
                $jumlahRusak = (int) $this->kerusakanItems[$barang->id];

                // Validasi jumlah rusak tidak melebihi yang dipinjam
                if ($jumlahRusak > $kuantitasPinjam) {
                    throw new \Exception("Jumlah barang rusak melebihi jumlah yang dipinjam.");
                }

                // Kembalikan stok yang tidak rusak
                $barang->increment('jumlah_saat_ini', $kuantitasPinjam - $jumlahRusak);

                // Proses barang rusak jika ada
                if ($jumlahRusak > 0) {
                    $barang->decrement('jumlah_total', $jumlahRusak);
                    $barang->increment('jumlah_rusak', $jumlahRusak);
                }
            }

            // Tangguhkan akun siswa jika ada kerusakan
            if ($adaKerusakan) {
                $transaksi->siswa->update(['is_ditangguhkan' => true]);
            }
        });

        session()->flash('message', 'Barang berhasil dikembalikan.');
        $this->transaksiIdUntukDikembalikan = null;
        $this->transaksiTerpilih = null;
        $this->kerusakanItems = [];
    }

    // mengecek asal ruangan barang di keranjang
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

    public function bukaModalKonfirmasi($id)
    {
        $transaksi = Transaksi::with('barangs', 'siswa')->find($id);
        $this->konfirmasiTransaksi = $transaksi;

        $this->kerusakanItems = [];
        foreach ($transaksi->barangs as $barang) {
            // Isi default jumlah rusak sesuai laporan siswa
            $this->kerusakanItems[$barang->id] = $barang->pivot->jumlah_rusak_dilaporkan;
        }

        $this->showKonfirmasiModal = true;
    }

    public function finalisasiPengembalian()
    {
        if (!$this->konfirmasiTransaksi) return;

        $adaKerusakan = false;
        foreach ($this->kerusakanItems as $jumlahRusak) {
            if ($jumlahRusak > 0) {
                $adaKerusakan = true;
                break;
            }
        }

        DB::transaction(function () use ($adaKerusakan) {
            $transaksi = $this->konfirmasiTransaksi;
            $transaksi->update(['status' => 'dikembalikan']);

            foreach ($transaksi->barangs as $barang) {
                $kuantitasPinjam = $barang->pivot->kuantitas;
                $jumlahRusak = (int) $this->kerusakanItems[$barang->id];

                // Validasi jumlah rusak
                if ($jumlahRusak > $kuantitasPinjam) {
                    throw new \Exception("Jumlah barang rusak melebihi jumlah yang dipinjam.");
                }

                // Kembalikan stok yang tidak rusak
                $barang->increment('jumlah_saat_ini', $kuantitasPinjam - $jumlahRusak);

                // Proses barang rusak jika ada
                if ($jumlahRusak > 0) {
                    $barang->decrement('jumlah_total', $jumlahRusak);
                    $barang->increment('jumlah_rusak', $jumlahRusak);
                }
            }

            // Tangguhkan akun siswa jika ada kerusakan
            if ($adaKerusakan) {
                $transaksi->siswa->update(['is_ditangguhkan' => true]);
            }
        });

        session()->flash('message', 'Pengembalian barang telah difinalisasi.');
        $this->showKonfirmasiModal = false;
    }


    // tiga method dibawah untuk menangani perpanjangan peminjaman
    public function bukaModalPerpanjangan($id)
    {
        $this->perpanjanganTransaksi = Transaksi::with('barangs', 'siswa')->find($id);
        $this->waktu_kembali_baru = $this->perpanjanganTransaksi->waktu_kembali; // Isi dengan waktu lama sebagai default
        $this->showPerpanjanganModal = true;
    }

    public function setujuiPerpanjangan()
    {
        $this->validate(['waktu_kembali_baru' => 'required|date|after:' . $this->perpanjanganTransaksi->waktu_kembali]);

        $this->perpanjanganTransaksi->update([
            'waktu_kembali' => $this->waktu_kembali_baru,
            'status' => 'dipinjam' // Kembalikan status ke 'dipinjam'
        ]);

        session()->flash('message', 'Perpanjangan waktu peminjaman berhasil disetujui.');
        $this->showPerpanjanganModal = false;
    }

    public function tolakPerpanjangan($id)
    {
        $transaksi = Transaksi::find($id);
        if ($transaksi) {
            $transaksi->update([
                'status' => 'dipinjam', // Kembalikan status ke 'dipinjam'
                'alasan_penolakan' => 'Permohonan perpanjangan ditolak oleh admin.'
            ]);
            session()->flash('message', 'Permohonan perpanjangan telah ditolak.');
        }
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

        $menungguKonfirmasi = Transaksi::with(['siswa', 'barangs'])
            ->where('status', 'menunggu-konfirmasi')
            ->latest()->get();

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

        $permohonanPerpanjangan = Transaksi::with(['siswa', 'barangs'])
            ->where('status', 'perpanjangan-diajukan')
            ->latest()->get();

        return view('livewire.dashboard.index', [
            'transaksis' => $transaksis,
            'permintaanMasuk' => $permintaanMasuk,
            'totalUnitBarang' => Barang::sum('jumlah_total'),
            'totalDipinjam' => $totalDipinjam,
            'totalRusak' => Barang::sum('jumlah_rusak'),
            'jatuhTempo' => $jatuhTempo,
            'menungguKonfirmasi' => $menungguKonfirmasi,
            'permohonanPerpanjangan' => $permohonanPerpanjangan,
            'ruangans' => Ruangan::all(),
        ]);
    }
}
