<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\Transaksi;
use App\Models\PengadaanBarang; 
use Illuminate\Support\Facades\DB; 
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

    // Ganti method simpanBarang() dengan ini
    public function simpanBarang()
    {
        $validatedData = $this->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                if ($this->barang_id) {
                    // Logic untuk UPDATE BARANG (Hanya data barang, bukan stok)
                    $barang = Barang::find($this->barang_id);
                    $barang->update([
                        'kode_barang' => $validatedData['kode_barang'],
                        'nama_barang' => $validatedData['nama_barang'],
                        'kategori_id' => $validatedData['kategori_id'],
                        'ruangan_id' => $validatedData['ruangan_id'],
                    ]);
                    session()->flash('message', 'Data barang berhasil diperbarui.');
                } else {
                    // Logic untuk CREATE BARANG BARU
                    $barang = Barang::create([
                        'kode_barang' => $validatedData['kode_barang'],
                        'nama_barang' => $validatedData['nama_barang'],
                        'kategori_id' => $validatedData['kategori_id'],
                        'ruangan_id' => $validatedData['ruangan_id'],
                        'jumlah_total' => $validatedData['jumlah'], // Stok awal dari pengadaan pertama
                        'jumlah_saat_ini' => $validatedData['jumlah'],
                    ]);

                    // Buat record pengadaan pertama untuk barang baru
                    PengadaanBarang::create([
                        'barang_id' => $barang->id,
                        'jumlah' => $validatedData['jumlah'],
                        'harga_satuan' => $validatedData['harga_satuan'] ?? 0,
                        'sumber_dana' => $validatedData['sumber_dana'],
                        'tanggal_pengadaan' => $validatedData['tanggal_pengadaan'],
                    ]);
                    session()->flash('message', 'Barang baru berhasil ditambahkan.');
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
        // Validasi hanya field pengadaan
        $validatedData = $this->validate([
            'jumlah'        => 'required|integer|min:1',
            'harga_satuan'  => 'nullable|numeric|min:0',
            'sumber_dana'   => 'required|string|max:255',
            'tanggal_pengadaan' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                // 1. Cari barang yang akan ditambah stoknya
                $barang = Barang::findOrFail($this->tambahStokBarangId);

                // 2. Buat record pengadaan baru
                PengadaanBarang::create([
                    'barang_id' => $barang->id,
                    'jumlah' => $validatedData['jumlah'],
                    'harga_satuan' => $validatedData['harga_satuan'] ?? 0,
                    'sumber_dana' => $validatedData['sumber_dana'],
                    'tanggal_pengadaan' => $validatedData['tanggal_pengadaan'],
                ]);

                // 3. Update jumlah total dan jumlah saat ini di tabel barangs
                $barang->increment('jumlah_total', $validatedData['jumlah']);
                $barang->increment('jumlah_saat_ini', $validatedData['jumlah']);
            });

            session()->flash('message', 'Stok barang berhasil ditambahkan.');
            $this->closeTambahStokModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            // Jangan tutup modal jika error
        }
    }

    public function render()
    {
        $detailBarang = null;
        if ($this->detailBarangId) {
            $barang = Barang::with('riwayatPengadaan') 
                ->findOrFail($this->detailBarangId);

            $distribusi = Transaksi::with('siswa')
                ->whereHas('barangs', function ($query) {
                    $query->where('barang_id', $this->detailBarangId);
                })
                ->whereIn('status', ['dipinjam', 'disetujui'])
                ->get()
                ->groupBy('ruang_pemakaian');

            $detailBarang = [
                'barang' => $barang,
                'distribusi' => $distribusi,
                'riwayatPengadaan' => $barang->riwayatPengadaan, // Kirim data pengadaan
            ];
        }

        // Query untuk tabel utama
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
            ->latest()
            ->paginate(10);

        return view('livewire.barang.index', [
            'barangs' => $barangs,
            'kategoris' => $kategoris,
            'ruangans' => $ruangans,
            'detailBarang' => $detailBarang,
        ]);
    }
}
