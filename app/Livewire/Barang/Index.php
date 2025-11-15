<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\Transaksi;
use App\Models\PengadaanBarang;
use App\Models\RabPengadaan;
use App\Models\RabItem;
use App\Models\PemindahanBarang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app')]
#[Title('Manajemen Barang')]
class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $barang_id;
    public $kode_barang, $nama_barang, $kategori_id, $ruangan_id, $jumlah_total;
    public $detailBarangId = null;
    public $barangIdToDelete;
    public $barangNamaToDelete;
    public $barangIdToUpdateStatus;
    public $barangNamaForStatus;
    public $jumlahYangRusak = 1;
    public $barangIdToRepair;
    public $barangNamaForRepair;
    public $jumlahYangDiperbaiki = 1;
    public $maxPerbaikan;
    public $filterKategori = '';
    public $search = '';

    // Kumpulan properti untuk fitur pemindahan barang
    public $showPindahModal = false;
    public $pindahBarangId;
    public $pindahBarang; // Untuk menyimpan objek barang asal
    public $jumlahPindah;
    public $ruanganTujuanId;
    public $kodeBaruPindahan;
    public $namaBaruPindahan;
    public $showRiwayatPindahModal = false;


    // Properti form pengadaan (baru)
    public $jumlah, $harga_satuan, $sumber_dana, $tanggal_pengadaan;

    // properti pengadaan untuk barang yang sudah ada
    public $showTambahStokModal = false;
    public $tambahStokBarangId;
    public $tambahStokBarangNama;

    protected function rules()
    {
        $rules = [
            'kode_barang'   => ['required', 'string', Rule::unique('barangs')->ignore($this->barang_id)],
            'nama_barang'   => 'required|string|min:3',
            'kategori_id'   => 'required|exists:kategoris,id',
            'ruangan_id'    => 'required|exists:ruangans,id',
            // Aturan untuk pengadaan
            'jumlah'        => 'required|integer|min:1',
            'harga_satuan'  => 'nullable|numeric|min:0',
            'sumber_dana'   => 'required|string|max:255',
            'tanggal_pengadaan' => 'required|date',
        ];

        // Saat edit barang, field pengadaan tidak wajib
        if ($this->barang_id) {
            $rules['jumlah'] = 'nullable|integer|min:0'; // Jumlah opsional saat edit
            $rules['harga_satuan'] = 'nullable|numeric|min:0';
            $rules['sumber_dana'] = 'nullable|string|max:255';
            $rules['tanggal_pengadaan'] = 'nullable|date';
        }

        return $rules;
    }

    public function openModal()
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function openRiwayatPindahModal()
    {
        $this->showRiwayatPindahModal = true;
    }

    // Method untuk menutup modal
    public function closeRiwayatPindahModal()
    {
        $this->showRiwayatPindahModal = false;
    }

    public function resetInput()
    {
        $this->reset([
            'barang_id',
            'kode_barang',
            'nama_barang',
            'kategori_id',
            'ruangan_id',
            'jumlah',
            'harga_satuan',
            'sumber_dana',
            'tanggal_pengadaan' // Reset field baru
        ]);
        $this->resetErrorBag(); // Hapus pesan error lama
    }
    public function updatingFilterKategori()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function simpanBarang()
    {
        // Validasi aturan dasar
        $validatedData = $this->validate();
        $user = Auth::user();

        try {
            DB::transaction(function () use ($validatedData, $user) {

                if ($this->barang_id) {
                    // --- LOGIKA EDIT ---
                    // (Hanya edit info, tidak mengubah stok. Tetap sama untuk kedua peran)
                    $barang = Barang::find($this->barang_id);
                    $barang->update([
                        'kode_barang' => $validatedData['kode_barang'],
                        'nama_barang' => $validatedData['nama_barang'],
                        'kategori_id' => $validatedData['kategori_id'],
                        'stok_minimum' => $validatedData['stok_minimum'] ?? $barang->stok_minimum, // asumsikan stok_minimum ada di rules
                    ]);
                    session()->flash('message', 'Data barang berhasil diperbarui.');
                } else {
                    // --- LOGIKA TAMBAH BARANG BARU ---
                    if ($user->peran === 'kepala_gudang') {
                        // KEPALA GUDANG: Langsung simpan ke inventaris
                        $barang = Barang::create([
                            'kode_barang' => $validatedData['kode_barang'],
                            'nama_barang' => $validatedData['nama_barang'],
                            'kategori_id' => $validatedData['kategori_id'],
                            'ruangan_id' => $validatedData['ruangan_id'],
                            'stok_minimum' => $validatedData['stok_minimum'] ?? 0,
                            'jumlah_total' => $validatedData['jumlah'],
                            'jumlah_saat_ini' => $validatedData['jumlah'],
                        ]);

                        // Catat di riwayat pengadaan
                        PengadaanBarang::create([
                            'barang_id' => $barang->id,
                            'jumlah' => $validatedData['jumlah'],
                            'harga_satuan' => $validatedData['harga_satuan'] ?? 0,
                            'sumber_dana' => $validatedData['sumber_dana'],
                            'tanggal_pengadaan' => $validatedData['tanggal_pengadaan'],
                        ]);

                        session()->flash('message', 'Barang baru berhasil ditambahkan.');
                    }
                    // PENJAGA GUDANG: Buat RAB untuk persetujuan
                    else {
                        $rab = RabPengadaan::create([
                            'user_id' => $user->id,
                            'keterangan' => 'Pengajuan barang baru: ' . $validatedData['nama_barang'],
                            'tanggal_pengajuan' => $validatedData['tanggal_pengadaan'],
                            'status' => 'diajukan',
                        ]);

                        RabItem::create([
                            'rab_pengadaan_id' => $rab->id,
                            'barang_id' => null, // null karena barang baru
                            'nama_barang_baru' => $validatedData['nama_barang'],
                            'spesifikasi' => 'Kode: ' . $validatedData['kode_barang'],
                            'jumlah' => $validatedData['jumlah'],
                            'harga_satuan' => $validatedData['harga_satuan'] ?? 0,
                            'harga_total' => ($validatedData['jumlah'] * ($validatedData['harga_satuan'] ?? 0)),
                        ]);

                        session()->flash('message', 'Pengajuan barang baru telah dikirim ke Kepala Gudang untuk persetujuan.');
                    }
                }
            });
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $this->barang_id = $id;
        $this->kode_barang = $barang->kode_barang;
        $this->nama_barang = $barang->nama_barang;
        $this->kategori_id = $barang->kategori_id;
        $this->ruangan_id = $barang->ruangan_id;

        // Kosongkan field pengadaan saat mode edit
        $this->reset(['jumlah', 'harga_satuan', 'sumber_dana', 'tanggal_pengadaan']);

        $this->showModal = true;
    }

    public function konfirmasiStatusRusak($id)
    {
        $barang = Barang::find($id);
        $this->barangIdToUpdateStatus = $id;
        $this->barangNamaForStatus = $barang->nama_barang;
        $this->jumlahYangRusak = 1; // Reset ke 1 setiap kali modal dibuka
    }

    public function updateStatusRusak()
    {
        $barang = Barang::find($this->barangIdToUpdateStatus);

        // Validasi: Jumlah yang rusak tidak boleh > stok yang tersedia
        $this->validate(
            ['jumlahYangRusak' => 'required|integer|min:1|max:' . $barang->jumlah_saat_ini],
            ['jumlahYangRusak.max' => 'Jumlah rusak tidak boleh melebihi stok yang tersedia.']
        );

        if ($barang) {
            // Kurangi jumlah total dan jumlah saat ini
            $barang->decrement('jumlah_total', $this->jumlahYangRusak);
            $barang->decrement('jumlah_saat_ini', $this->jumlahYangRusak);
            // Tambahkan ke jumlah rusak
            $barang->increment('jumlah_rusak', $this->jumlahYangRusak);

            session()->flash('message', $this->jumlahYangRusak . ' unit barang berhasil ditandai rusak.');
        }

        $this->barangIdToUpdateStatus = null; // Tutup modal
    }

    public function konfirmasiPerbaikan($id)
    {
        $barang = Barang::find($id);
        $this->barangIdToRepair = $id;
        $this->barangNamaForRepair = $barang->nama_barang;
        $this->maxPerbaikan = $barang->jumlah_rusak; // Catat jumlah maksimal yang bisa diperbaiki
        $this->jumlahYangDiperbaiki = 1; // Reset ke 1
    }

    public function prosesPerbaikan()
    {
        // Validasi: Jumlah yang diperbaiki tidak boleh lebih dari yang rusak
        $this->validate(
            ['jumlahYangDiperbaiki' => 'required|integer|min:1|max:' . $this->maxPerbaikan],
            ['jumlahYangDiperbaiki.max' => 'Jumlah perbaikan tidak boleh melebihi jumlah yang rusak.']
        );

        $barang = Barang::find($this->barangIdToRepair);
        if ($barang) {
            // Kembalikan jumlah total dan jumlah saat ini
            $barang->increment('jumlah_total', $this->jumlahYangDiperbaiki);
            $barang->increment('jumlah_saat_ini', $this->jumlahYangDiperbaiki);
            // Kurangi dari jumlah rusak
            $barang->decrement('jumlah_rusak', $this->jumlahYangDiperbaiki);

            session()->flash('message', $this->jumlahYangDiperbaiki . ' unit barang berhasil diperbaiki dan dikembalikan ke stok.');
        }

        $this->barangIdToRepair = null; // Tutup modal
    }

    public function closeDetailModal()
    {
        $this->detailBarangId = null;
    }

    // method ini untuk membuka modal tambah stok
    public function openTambahStokModal($id)
    {
        $barang = Barang::findOrFail($id);
        $this->tambahStokBarangId = $id;
        $this->tambahStokBarangNama = $barang->nama_barang;
        // Reset field form pengadaan
        $this->reset(['jumlah', 'harga_satuan', 'sumber_dana', 'tanggal_pengadaan']);
        $this->resetErrorBag(); // Hapus error lama
        $this->showTambahStokModal = true;
    }

    //  method ini untuk menutup modal tambah stok
    public function closeTambahStokModal()
    {
        $this->showTambahStokModal = false;
    }

    //  method ini untuk memproses penambahan stok
    public function prosesTambahStok()
    {
        $validatedData = $this->validate([
            'jumlah'        => 'required|integer|min:1',
            'harga_satuan'  => 'nullable|numeric|min:0',
            'sumber_dana'   => 'required|string|max:255',
            'tanggal_pengadaan' => 'required|date',
        ]);

        $user = Auth::user();

        try {
            DB::transaction(function () use ($validatedData, $user) {
                $barang = Barang::findOrFail($this->tambahStokBarangId);

                // JIKA KEPALA GUDANG: Langsung eksekusi
                if ($user->peran === 'kepala_gudang') {
                    PengadaanBarang::create([
                        'barang_id' => $barang->id,
                        'jumlah' => $validatedData['jumlah'],
                        'harga_satuan' => $validatedData['harga_satuan'] ?? 0,
                        'sumber_dana' => $validatedData['sumber_dana'],
                        'tanggal_pengadaan' => $validatedData['tanggal_pengadaan'],
                    ]);

                    $barang->increment('jumlah_total', $validatedData['jumlah']);
                    $barang->increment('jumlah_saat_ini', $validatedData['jumlah']);

                    session()->flash('message', 'Stok barang berhasil ditambahkan.');
                }
                // JIKA PENJAGA GUDANG: Buat RAB untuk persetujuan
                else {
                    $rab = RabPengadaan::create([
                        'user_id' => $user->id,
                        'keterangan' => 'Pengajuan tambah stok untuk: ' . $barang->nama_barang,
                        'tanggal_pengajuan' => $validatedData['tanggal_pengadaan'],
                        'status' => 'diajukan',
                    ]);

                    RabItem::create([
                        'rab_pengadaan_id' => $rab->id,
                        'barang_id' => $barang->id, // Tautkan ke barang yang sudah ada
                        'nama_barang_baru' => $barang->nama_barang,
                        'spesifikasi' => 'Tambah stok untuk barang yang ada.',
                        'jumlah' => $validatedData['jumlah'],
                        'harga_satuan' => $validatedData['harga_satuan'] ?? 0,
                        'harga_total' => ($validatedData['jumlah'] * ($validatedData['harga_satuan'] ?? 0)),
                    ]);

                    session()->flash('message', 'Pengajuan tambah stok telah dikirim ke Kepala Gudang untuk persetujuan.');
                }
            });
            $this->closeTambahStokModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk membuka modal
    public function openPindahModal($id)
    {
        $this->pindahBarang = Barang::findOrFail($id);
        $this->pindahBarangId = $id;

        // Reset input
        $this->reset(['jumlahPindah', 'ruanganTujuanId']);
        $this->resetErrorBag();
        $this->showPindahModal = true;
    }

    // Method untuk menutup modal
    public function closePindahModal()
    {
        $this->showPindahModal = false;
    }

    // Method untuk memproses pemindahan
    public function prosesPemindahan()
    {
        // if (Auth::user()->peran !== 'kepala_gudang') return;

        $validated = $this->validate([
            'jumlahPindah' => 'required|integer|min:1|max:' . $this->pindahBarang->jumlah_saat_ini,
            'ruanganTujuanId' => 'required|exists:ruangans,id|different:pindahBarang.ruangan_id',
        ], [
            'jumlahPindah.max' => 'Jumlah pindah melebihi stok tersedia.',
            'ruanganTujuanId.different' => 'Ruangan tujuan harus berbeda dengan ruangan asal.',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $barangAsal = $this->pindahBarang;

                // 1. Cek apakah barang dengan kode yang sama SUDAH ADA di ruangan tujuan?
                $barangTujuan = Barang::where('kode_barang', $barangAsal->kode_barang)
                    ->where('ruangan_id', $validated['ruanganTujuanId'])
                    ->first();

                if ($barangTujuan) {
                    // KASUS A: Barang sudah ada di ruangan tujuan -> GABUNGKAN STOK
                    $barangTujuan->increment('jumlah_total', $validated['jumlahPindah']);
                    $barangTujuan->increment('jumlah_saat_ini', $validated['jumlahPindah']);
                } else {
                    // KASUS B: Barang belum ada di ruangan tujuan -> BUAT BARU (CLONE)
                    // Kita salin semua data dari barang asal, kecuali ID dan Ruangan
                    $barangTujuan = Barang::create([
                        'kode_barang' => $barangAsal->kode_barang, // Kode SAMA
                        'nama_barang' => $barangAsal->nama_barang, // Nama SAMA
                        'kategori_id' => $barangAsal->kategori_id,
                        'ruangan_id'  => $validated['ruanganTujuanId'], // Ruangan BARU
                        'stok_minimum' => $barangAsal->stok_minimum,
                        'jumlah_total' => $validated['jumlahPindah'],
                        'jumlah_saat_ini' => $validated['jumlahPindah'],
                    ]);
                }

                // 2. Kurangi stok barang asal
                $barangAsal->decrement('jumlah_total', $validated['jumlahPindah']);
                $barangAsal->decrement('jumlah_saat_ini', $validated['jumlahPindah']);

                // 3. Catat Riwayat Pemindahan
                PemindahanBarang::create([
                    'barang_asal_id' => $barangAsal->id,
                    'barang_tujuan_id' => $barangTujuan->id,
                    'jumlah_dipindahkan' => $validated['jumlahPindah'],
                    'user_id' => Auth::id(),
                    'catatan' => 'Pemindahan stok antar ruangan',
                ]);
            });

            session()->flash('message', 'Barang berhasil dipindahkan.');
            $this->closePindahModal();
            $this->closeDetailModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // app/Livewire/Barang/Index.php

    public function render()
    {
        $dataDetail = null;

        // Cek jika ID sudah di-set (setelah user mengklik tombol)
        if ($this->detailBarangId) {
            // Ambil data barang, DAN SEMUA RELASI yang dibutuhkan oleh modal
            $barang = Barang::with([
                'riwayatPengadaan',
                'pemindahanKeluar.barangTujuan.ruangan',
                'pemindahanMasuk.barangAsal.ruangan'
            ])
                ->findOrFail($this->detailBarangId);

            // Ambil data distribusi
            $distribusi = Transaksi::with('siswa')
                ->whereHas('barangs', function ($query) {
                    $query->where('barang_id', $this->detailBarangId);
                })
                ->whereIn('status', ['dipinjam', 'disetujui'])
                ->get()
                ->groupBy('ruang_pemakaian');

            $dataDetail = [
                'barang' => $barang,
                'distribusi' => $distribusi,
                'riwayatPengadaan' => $barang->riwayatPengadaan,
                'riwayatPemindahanKeluar' => $barang->pemindahanKeluar,
                'riwayatPemindahanMasuk' => $barang->pemindahanMasuk,
            ];
        }

        // Bagian ini untuk tabel utama (tidak berubah)
        $kategoris = Kategori::all();
        $ruangans = Ruangan::all();
        $barangs = Barang::with('kategori', 'ruangan')
            ->where(function ($query) {
                $query->where('nama_barang', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_barang', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterKategori, function ($query) {
                $query->where('kategori_id', $this->filterKategori);
            })
            ->where('jumlah_total', '>', 0)
            ->latest()
            ->paginate(10);

        $riwayatPemindahan = [];
        if ($this->showRiwayatPindahModal) {
            $riwayatPemindahan = PemindahanBarang::with(['barangAsal', 'barangTujuan.ruangan', 'user'])
                ->latest()
                ->paginate(10, ['*'], 'pindahPage');
        }

        return view('livewire.barang.index', [
            'barangs' => $barangs,
            'kategoris' => $kategoris,
            'ruangans' => $ruangans,
            'detailBarang' => $dataDetail,
            'riwayatPemindahan' => $riwayatPemindahan,
        ]);
    }
}
