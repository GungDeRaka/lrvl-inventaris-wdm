<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\Transaksi;
use App\Models\PengadaanBarang;
use App\Models\SumberDana;
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

    // --- PROPERTI UMUM ---
    public $showModal = false;
    public $barang_id;
    public $kode_barang, $nama_barang, $kategori_id, $ruangan_id, $jumlah_total;
    public $stok_minimum; // Tambahan untuk stok minimum
    
    // --- PROPERTI DETAIL (Hanya ID, data diambil di render) ---
    public $detailBarangId = null; 

    // --- PROPERTI STATUS / HAPUS ---
    public $barangIdToDelete;
    public $barangNamaToDelete;
    public $barangIdToUpdateStatus;
    public $barangNamaForStatus;
    public $jumlahYangRusak = 1;
    public $barangIdToRepair;
    public $barangNamaForRepair;
    public $jumlahYangDiperbaiki = 1;
    public $maxPerbaikan;
    
    // --- PROPERTI FILTER ---
    public $filterKategori = '';
    public $search = '';

    // --- PROPERTI PEMINDAHAN BARANG ---
    public $showPindahModal = false;
    public $pindahBarangId;
    public $pindahBarang; 
    public $jumlahPindah;
    public $ruanganTujuanId;
    public $showRiwayatPindahModal = false;

    // --- PROPERTI PENGADAAN (Formulir) ---
    public $jumlah, $harga_satuan, $tanggal_pengadaan;
    public $sumber_dana_id;
    
    // --- PROPERTI SUMBER DANA BARU ---
    public $sumberDanaBaru = '';
    public $isAddingSumberDana = false;

    // --- PROPERTI TAMBAH STOK ---
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
            'stok_minimum'  => 'required|integer|min:0',
            
            // Aturan untuk pengadaan
            'jumlah'        => 'required|integer|min:1',
            'harga_satuan'  => 'nullable|numeric|min:0',
            'sumber_dana_id'=> 'required|exists:sumber_danas,id',
            'tanggal_pengadaan' => 'required|date',
        ];

        // Saat edit barang, field pengadaan tidak wajib
        if ($this->barang_id) {
            $rules['jumlah'] = 'nullable|integer|min:0';
            $rules['harga_satuan'] = 'nullable|numeric|min:0';
            $rules['sumber_dana_id'] = 'nullable|exists:sumber_danas,id';
            $rules['tanggal_pengadaan'] = 'nullable|date';
        }

        return $rules;
    }

    // --- METHOD RESET & MODAL ---
    public function resetInput()
    {
        $this->reset([
            'barang_id', 'kode_barang', 'nama_barang', 'kategori_id', 'ruangan_id', 'stok_minimum',
            'jumlah', 'harga_satuan', 'sumber_dana_id', 'tanggal_pengadaan'
        ]);
        $this->resetErrorBag();
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

    // --- METHOD SEARCH & PAGINATION ---
    public function updatingFilterKategori()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // --- METHOD SUMBER DANA ---
    public function toggleSumberDanaBaru()
    {
        $this->isAddingSumberDana = !$this->isAddingSumberDana;
        $this->sumberDanaBaru = '';
        $this->sumber_dana_id = '';
    }

    public function simpanSumberDanaBaru()
    {
        $this->validate(['sumberDanaBaru' => 'required|string|unique:sumber_danas,nama_sumber']);
        $sumber = SumberDana::create(['nama_sumber' => $this->sumberDanaBaru]);
        $this->sumber_dana_id = $sumber->id;
        $this->isAddingSumberDana = false;
        $this->sumberDanaBaru = '';
        session()->flash('message', 'Sumber dana berhasil ditambahkan.');
    }

    // --- METHOD CRUD BARANG ---
    public function simpanBarang()
    {
        $validatedData = $this->validate();
        $user = Auth::user();

        try {
            DB::transaction(function () use ($validatedData, $user) {
                if ($this->barang_id) {
                    // EDIT BARANG (Tidak merubah stok, hanya metadata)
                    $barang = Barang::find($this->barang_id);
                    $barang->update([
                        'kode_barang' => $validatedData['kode_barang'],
                        'nama_barang' => $validatedData['nama_barang'],
                        'kategori_id' => $validatedData['kategori_id'],
                        'ruangan_id' => $validatedData['ruangan_id'], // Diperlukan jika bukan pemindahan stok
                        'stok_minimum' => $validatedData['stok_minimum'] ?? $barang->stok_minimum,
                    ]);
                    session()->flash('message', 'Data barang berhasil diperbarui.');
                } else {
                    // TAMBAH BARANG BARU
                    if ($user->peran === 'kepala_gudang') {
                        // Kepala Gudang: Langsung Simpan
                        $barang = Barang::create([
                            'kode_barang' => $validatedData['kode_barang'],
                            'nama_barang' => $validatedData['nama_barang'],
                            'kategori_id' => $validatedData['kategori_id'],
                            'ruangan_id' => $validatedData['ruangan_id'],
                            'stok_minimum' => $validatedData['stok_minimum'] ?? 0,
                            'jumlah_total' => $validatedData['jumlah'],
                            'jumlah_saat_ini' => $validatedData['jumlah'],
                        ]);

                        PengadaanBarang::create([
                            'barang_id' => $barang->id,
                            'sumber_dana_id' => $validatedData['sumber_dana_id'],
                            'jumlah' => $validatedData['jumlah'],
                            'harga_satuan' => $validatedData['harga_satuan'] ?? 0,
                            'total_harga' => $validatedData['jumlah'] * ($validatedData['harga_satuan'] ?? 0),
                            'tanggal_pengadaan' => $validatedData['tanggal_pengadaan'],
                            'user_id' => Auth::id(),
                        ]);
                        session()->flash('message', 'Barang baru berhasil ditambahkan.');
                    } else {
                        // Penjaga Gudang: Ajukan RAB
                        $rab = RabPengadaan::create([
                            'user_id' => $user->id,
                            'keterangan' => 'Pengajuan barang baru: ' . $validatedData['nama_barang'],
                            'tanggal_pengajuan' => $validatedData['tanggal_pengadaan'],
                            'status' => 'diajukan',
                        ]);

                        RabItem::create([
                            'rab_pengadaan_id' => $rab->id,
                            'barang_id' => null,
                            'nama_barang_baru' => $validatedData['nama_barang'],
                            'kode_barang_baru' => $validatedData['kode_barang'],
                            'kategori_id' => $validatedData['kategori_id'],
                            'ruangan_id' => $validatedData['ruangan_id'],
                            'stok_minimum_baru' => $validatedData['stok_minimum'] ?? 0,
                            'spesifikasi' => 'Kode: ' . $validatedData['kode_barang'],
                            'jumlah' => $validatedData['jumlah'],
                            'harga_satuan' => $validatedData['harga_satuan'] ?? 0,
                            'harga_total' => ($validatedData['jumlah'] * ($validatedData['harga_satuan'] ?? 0)),
                            'sumber_dana_id' => $validatedData['sumber_dana_id'], // Simpan info sumber dana di item RAB jika perlu (opsional)
                        ]);
                        session()->flash('message', 'Pengajuan barang baru telah dikirim ke Kepala Gudang.');
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
        $this->stok_minimum = $barang->stok_minimum;

        $this->reset(['jumlah', 'harga_satuan', 'sumber_dana_id', 'tanggal_pengadaan']);
        $this->showModal = true;
    }

    // --- METHOD TAMBAH STOK ---
    public function openTambahStokModal($id)
    {
        $barang = Barang::findOrFail($id);
        $this->tambahStokBarangId = $id;
        $this->tambahStokBarangNama = $barang->nama_barang;
        $this->reset(['jumlah', 'harga_satuan', 'sumber_dana_id', 'tanggal_pengadaan']);
        $this->resetErrorBag();
        $this->showTambahStokModal = true;
    }

    public function closeTambahStokModal()
    {
        $this->showTambahStokModal = false;
    }

    public function prosesTambahStok()
    {
        $validated = $this->validate([
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'sumber_dana_id' => 'required|exists:sumber_danas,id',
            'tanggal_pengadaan' => 'required|date',
        ]);

        $user = Auth::user();

        try {
            DB::transaction(function () use ($validated, $user) {
                $barang = Barang::findOrFail($this->tambahStokBarangId);

                if ($user->peran === 'kepala_gudang') {
                    // Kepala Gudang: Langsung
                    PengadaanBarang::create([
                        'barang_id' => $this->tambahStokBarangId,
                        'sumber_dana_id' => $this->sumber_dana_id,
                        'jumlah' => $validated['jumlah'],
                        'harga_satuan' => $validated['harga_satuan'],
                        'total_harga' => $validated['jumlah'] * $validated['harga_satuan'],
                        'tanggal_pengadaan' => $validated['tanggal_pengadaan'],
                        'user_id' => Auth::id(),
                    ]);

                    $barang->increment('jumlah_total', $validated['jumlah']);
                    $barang->increment('jumlah_saat_ini', $validated['jumlah']);
                    session()->flash('message', 'Stok barang berhasil ditambahkan.');
                } else {
                    // Penjaga Gudang: RAB
                    $rab = RabPengadaan::create([
                        'user_id' => $user->id,
                        'keterangan' => 'Pengajuan tambah stok untuk: ' . $barang->nama_barang,
                        'tanggal_pengajuan' => $validated['tanggal_pengadaan'],
                        'status' => 'diajukan',
                    ]);

                    RabItem::create([
                        'rab_pengadaan_id' => $rab->id,
                        'barang_id' => $barang->id,
                        'nama_barang_baru' => $barang->nama_barang,
                        'spesifikasi' => 'Tambah stok',
                        'jumlah' => $validated['jumlah'],
                        'harga_satuan' => $validated['harga_satuan'] ?? 0,
                        'harga_total' => ($validated['jumlah'] * ($validated['harga_satuan'] ?? 0)),
                    ]);
                    session()->flash('message', 'Pengajuan tambah stok dikirim ke Kepala Gudang.');
                }
            });
            $this->closeTambahStokModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // --- METHOD PEMINDAHAN BARANG ---
    public function openPindahModal($id)
    {
        $this->pindahBarang = Barang::findOrFail($id);
        $this->pindahBarangId = $id;
        $this->reset(['jumlahPindah', 'ruanganTujuanId']);
        $this->resetErrorBag();
        $this->showPindahModal = true;
    }

    public function closePindahModal()
    {
        $this->showPindahModal = false;
    }

    public function prosesPemindahan()
    {
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
                $barangTujuan = Barang::where('kode_barang', $barangAsal->kode_barang)
                    ->where('ruangan_id', $validated['ruanganTujuanId'])->first();

                if ($barangTujuan) {
                    $barangTujuan->increment('jumlah_total', $validated['jumlahPindah']);
                    $barangTujuan->increment('jumlah_saat_ini', $validated['jumlahPindah']);
                } else {
                    $barangTujuan = Barang::create([
                        'kode_barang' => $barangAsal->kode_barang,
                        'nama_barang' => $barangAsal->nama_barang,
                        'kategori_id' => $barangAsal->kategori_id,
                        'ruangan_id'  => $validated['ruanganTujuanId'],
                        'stok_minimum' => $barangAsal->stok_minimum,
                        'jumlah_total' => $validated['jumlahPindah'],
                        'jumlah_saat_ini' => $validated['jumlahPindah'],
                    ]);
                }

                $barangAsal->decrement('jumlah_total', $validated['jumlahPindah']);
                $barangAsal->decrement('jumlah_saat_ini', $validated['jumlahPindah']);

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
            // Jika sedang melihat detail, tutup juga
            $this->detailBarangId = null; 
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // --- METHOD RIWAYAT PEMINDAHAN ---
    public function openRiwayatPindahModal()
    {
        $this->showRiwayatPindahModal = true;
    }

    public function closeRiwayatPindahModal()
    {
        $this->showRiwayatPindahModal = false;
    }

    // --- METHOD RUSAK & PERBAIKAN ---
    public function konfirmasiStatusRusak($id)
    {
        $barang = Barang::find($id);
        $this->barangIdToUpdateStatus = $id;
        $this->barangNamaForStatus = $barang->nama_barang;
        $this->jumlahYangRusak = 1;
    }

    public function updateStatusRusak()
    {
        $barang = Barang::find($this->barangIdToUpdateStatus);
        $this->validate(
            ['jumlahYangRusak' => 'required|integer|min:1|max:' . $barang->jumlah_saat_ini],
            ['jumlahYangRusak.max' => 'Jumlah rusak tidak boleh melebihi stok yang tersedia.']
        );

        if ($barang) {
            $barang->decrement('jumlah_total', $this->jumlahYangRusak);
            $barang->decrement('jumlah_saat_ini', $this->jumlahYangRusak);
            $barang->increment('jumlah_rusak', $this->jumlahYangRusak);
            session()->flash('message', $this->jumlahYangRusak . ' unit barang berhasil ditandai rusak.');
        }
        $this->barangIdToUpdateStatus = null;
    }

    public function konfirmasiPerbaikan($id)
    {
        $barang = Barang::find($id);
        $this->barangIdToRepair = $id;
        $this->barangNamaForRepair = $barang->nama_barang;
        $this->maxPerbaikan = $barang->jumlah_rusak;
        $this->jumlahYangDiperbaiki = 1;
    }

    public function prosesPerbaikan()
    {
        $this->validate(
            ['jumlahYangDiperbaiki' => 'required|integer|min:1|max:' . $this->maxPerbaikan],
            ['jumlahYangDiperbaiki.max' => 'Jumlah perbaikan tidak boleh melebihi jumlah yang rusak.']
        );

        $barang = Barang::find($this->barangIdToRepair);
        if ($barang) {
            $barang->increment('jumlah_total', $this->jumlahYangDiperbaiki);
            $barang->increment('jumlah_saat_ini', $this->jumlahYangDiperbaiki);
            $barang->decrement('jumlah_rusak', $this->jumlahYangDiperbaiki);
            session()->flash('message', $this->jumlahYangDiperbaiki . ' unit barang berhasil diperbaiki.');
        }
        $this->barangIdToRepair = null;
    }

    // --- METHOD DETAIL (DIPERBAIKI) ---
    public function closeDetailModal()
    {
        $this->detailBarangId = null;
    }

    // --- RENDER ---
    public function render()
    {
        // 1. Logika Data Detail (Hanya jika modal dibuka)
        $detailData = null; // Variabel lokal untuk data detail
        
        if ($this->detailBarangId) {
            $barang = Barang::with([
                            'riwayatPengadaan.sumberDana', 
                            'pemindahanKeluar.barangTujuan.ruangan',
                            'pemindahanMasuk.barangAsal.ruangan'
                        ])
                        ->findOrFail($this->detailBarangId);

            $distribusi = Transaksi::with('siswa')
                ->whereHas('barangs', function ($query) {
                    $query->where('barang_id', $this->detailBarangId);
                })
                ->whereIn('status', ['dipinjam', 'disetujui'])
                ->get()
                ->groupBy('ruang_pemakaian');

            $detailData = [
                'barang' => $barang,
                'distribusi' => $distribusi,
                'riwayatPengadaan' => $barang->riwayatPengadaan,
                'riwayatPemindahanKeluar' => $barang->pemindahanKeluar,
                'riwayatPemindahanMasuk' => $barang->pemindahanMasuk,
            ];
        }

        // 2. Logika Riwayat Pemindahan (Hanya jika modal dibuka)
        $riwayatPemindahan = [];
        if ($this->showRiwayatPindahModal) {
            $riwayatPemindahan = PemindahanBarang::with(['barangAsal', 'barangTujuan.ruangan', 'user'])
                ->latest()
                ->paginate(10, ['*'], 'pindahPage');
        }

        // 3. Logika Utama Tabel Barang
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
        
        return view('livewire.barang.index', [
            'barangs' => $barangs,
            'kategoris' => $kategoris,
            'ruangans' => $ruangans,
            'detailBarang' => $detailData, // Mengirim variabel lokal $detailData sebagai 'detailBarang'
            'riwayatPemindahan' => $riwayatPemindahan,
        ]);
    }
}