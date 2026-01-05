<?php

namespace App\Livewire\Rab;

use App\Models\RabPengadaan;
use App\Models\RabItem;
use App\Models\Barang;
use App\Models\SumberDana;
use App\Models\PengadaanBarang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // <-- Tambahkan Auth
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Manajemen RAB')]
class Index extends Component
{
    use WithPagination;

    // Properti untuk daftar RAB (Kepala Gudang)
    public $showDetailModal = false;
    public $selectedRab;
    public $catatan_kepala = '';

    // Properti untuk form pengajuan RAB (Penjaga Gudang)

    public $judul = '';
    public $keterangan = '';
    public $items = [];
    public $newItemNama = '';
    public $newItemSpec = '';
    public $newItemJumlah = 1;
    public $newItemHarga = 0;
    public $newItemSumberId = ''; 
    public $showCreateModal = false;

    // --- LOGIKA UNTUK PENGAJUAN RAB (dari Rab/Create) ---

    // Method untuk membuka/menutup modal create
    public function openCreateModal()
    {
        // 3. Jangan lupa reset newItemSumberId juga
        $this->reset(['judul', 'keterangan', 'items', 'newItemNama', 'newItemSpec', 'newItemJumlah', 'newItemHarga', 'newItemSumberId']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }
    public function addItem()
    {
        $this->validate([
            'newItemNama' => 'required|string',
            'newItemJumlah' => 'required|integer|min:1',
            'newItemHarga' => 'required|numeric|min:0',
            'newItemSpec' => 'nullable|string',
            'newItemSumberId' => 'required|exists:sumber_danas,id',
        ]);

        $sumber = SumberDana::find($this->newItemSumberId);
        $this->items[] = [
            'nama' => $this->newItemNama,
            'spesifikasi' => $this->newItemSpec,
            'jumlah' => (int)$this->newItemJumlah,
            'harga' => (float)$this->newItemHarga,
            'total' => (int)$this->newItemJumlah * (float)$this->newItemHarga,
            'sumber_dana_id' => $this->newItemSumberId, 
            'nama_sumber' => $sumber ? $sumber->nama_sumber : '-',
        ];
        $this->reset(['newItemNama', 'newItemSpec', 'newItemJumlah', 'newItemHarga','newItemSumberId']);
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function ajukanRab()
    {
        if (Auth::user()->peran !== 'penjaga_gudang') return;

        $this->validate([
            'judul' => 'required|string|min:5|max:100', // Validasi judul (5-30 karakter)
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nama' => 'required|string',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
        ], [ /* messages */]);

        try {
            DB::transaction(function () {
                $rab = RabPengadaan::create([
                    'user_id' => Auth::id(),
                    'judul' => $this->judul, // Simpan judul
                    'keterangan' => $this->keterangan,
                    'tanggal_pengajuan' => now()->toDateString(),
                    'status' => 'diajukan',
                ]);
                foreach ($this->items as $item) {
                    RabItem::create([
                        'rab_pengadaan_id' => $rab->id,
                        'nama_barang_baru' => $item['nama'],
                        'spesifikasi' => $item['spesifikasi'],
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $item['harga'],
                        'harga_total' => $item['total'],
                        'sumber_dana_id' => $item['sumber_dana_id'],
                    ]);
                }
            });
            session()->flash('message', 'Pengajuan RAB berhasil dikirim.');
            $this->closeCreateModal(); // Tutup modal setelah sukses
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengajukan RAB: ' . $e->getMessage());
        }
    }


    // --- AKHIR LOGIKA PENGAJUAN ---

    // --- LOGIKA UNTUK PERSETUJUAN RAB (dari Rab/Index lama) ---
    public function showDetail($id)
    {
        // Method ini sekarang bisa dipakai oleh kedua peran
        $rab = RabPengadaan::with('pengaju', 'peninjau', 'items.barang')->findOrFail($id);


        $this->catatan_kepala = $rab->catatan_kepala ?? '';


        $this->selectedRab = $rab;
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedRab = null;
    }

    public function prosesKeputusan($status)
    {
        if (!$this->selectedRab || Auth::user()->peran !== 'kepala_gudang') return;

        $this->validate(['catatan_kepala' => 'nullable|string']);

        try {
            DB::transaction(function () use ($status) {
                // 1. Update status RAB (Tidak berubah)
                $this->selectedRab->update([
                    'status' => $status,
                    'disetujui_oleh' => Auth::id(),
                    'tanggal_keputusan' => now()->toDateString(),
                    'catatan_kepala' => $this->catatan_kepala,
                ]);

                // 2. JIKA DISETUJUI, proses ke inventaris (LOGIKA DIPERBAIKI)
                if ($status == 'disetujui') {
                    foreach ($this->selectedRab->items as $item) {

                        $barangIdToUse = $item->barang_id; // Ambil ID barang yang sudah ada

                        // Cek apakah ini BARANG BARU (barang_id di rab_items adalah null)
                        if (is_null($item->barang_id)) {

                            // KASUS 1: BUAT BARANG BARU TERLEBIH DAHULU
                            $barangBaru = Barang::create([
                                'kode_barang' => $item->kode_barang_baru,
                                'nama_barang' => $item->nama_barang_baru,
                                'kategori_id' => $item->kategori_id,
                                'ruangan_id' => $item->ruangan_id,
                                'stok_minimum' => $item->stok_minimum_baru,
                                'jumlah_total' => $item->jumlah,
                                'jumlah_saat_ini' => $item->jumlah,
                            ]);
                            $barangIdToUse = $barangBaru->id; // Ambil ID dari barang yang baru dibuat

                        } else {
                            // KASUS 2: TAMBAH STOK BARANG YANG ADA
                            $barang = Barang::find($item->barang_id);
                            if ($barang) {
                                $barang->increment('jumlah_total', $item->jumlah);
                                $barang->increment('jumlah_saat_ini', $item->jumlah);
                            }
                        }

                        // 3. Catat di riwayat pengadaan (SETELAH BARANG DIBUAT/DITEMUKAN)
                        if ($barangIdToUse) {
                            PengadaanBarang::create([
                                'barang_id' => $barangIdToUse,
                                'jumlah' => $item->jumlah,
                                'harga_satuan' => $item->harga_satuan,
                                'total_harga' => $item->harga_total,
                                // PERBAIKAN DI SINI: Ambil sumber_dana_id dari item RAB
                                'sumber_dana_id' => $item->sumber_dana_id,
                                'tanggal_pengadaan' => $this->selectedRab->tanggal_keputusan,
                                'user_id' => Auth::id(),
                            ]);
                        }
                    }
                }
            });

            session()->flash('message', 'RAB telah berhasil di-' . ($status == 'disetujui' ? 'setujui dan stok diperbarui' : 'tolak') . '.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses RAB: ' . $e->getMessage());
        }
    }
    // --- AKHIR LOGIKA PERSETUJUAN ---

    public function render()
    {
        $user = Auth::user();
        $rabData = [];
        $sumberDanas = SumberDana::all();

        if ($user->peran === 'kepala_gudang') {
            // Kepala Gudang: lihat RAB yang diajukan & riwayat persetujuan
            $rabData['rabDiajukan'] = RabPengadaan::with('pengaju')
                ->where('status', 'diajukan')
                ->latest('tanggal_pengajuan')->paginate(10, ['*'], 'diajukanPage');

            $rabData['rabDiproses'] = RabPengadaan::with('pengaju', 'peninjau')
                ->whereIn('status', ['disetujui', 'ditolak'])
                ->latest('tanggal_keputusan')->paginate(10, ['*'], 'diprosesPage');
        } elseif ($user->peran === 'penjaga_gudang') {
            // Penjaga Gudang: lihat riwayat SEMUA RAB yang dia ajukan
            $rabData['rabSaya'] = RabPengadaan::with('peninjau') // Muat relasi peninjau
                ->where('user_id', $user->id)
                ->latest('tanggal_pengajuan')->paginate(10);
        }

        return view('livewire.rab.index', array_merge($rabData, ['sumberDanas' => $sumberDanas]));
    }
}
