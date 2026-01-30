<?php

namespace App\Livewire\Rab;

use App\Models\RabPengadaan;
use App\Models\RabItem;
use App\Models\Barang;
use App\Models\PengadaanBarang;
use App\Models\SumberDana;
use App\Models\Kategori; // Perlu Model Kategori
use App\Models\Ruangan;  // Perlu Model Ruangan
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Manajemen RAB')]
class Index extends Component
{
    use WithPagination;

    // State UI
    public $showDetailModal = false;
    public $showCreateModal = false;
    public $showProcurementModal = false; // Modal Khusus Penjaga Gudang Input Data Teknis

    public $selectedRab;
    public $catatan_kepala = ''; // Bisa diisi Kepala Gudang / Bendahara

    // Properti Pengajuan Baru (Penjaga Gudang)
    public $judul = '';
    public $keterangan = '';
    public $items = [];
    public $newItemNama = '';
    public $newItemSpec = '';
    public $newItemJumlah = 1;
    public $newItemHarga = 0;
    public $newItemSumberId = '';
    public $modeInput = 'baru'; // Opsi: 'baru' atau 'restock'
    public $existingBarangId = '';

    // Properti Edit Item (Bendahara)
    public $editingItemId = null;
    public $editJumlah = 0;
    public $editSumberId;
    public $isAddingSumber = false; // Status apakah sedang mode tambah sumber dana
    public $newSumberName = '';     // Menampung nama sumber dana baru

    // Properti Input Teknis (Penjaga Gudang - Fase Pengadaan)
    public $procurementItems = []; // Array untuk menampung input kode/kategori/ruang

    // --- 1. FITUR PENGAJUAN (PENJAGA GUDANG) ---
    public function openCreateModal()
    {
        $this->reset(['judul', 'keterangan', 'items', 'newItemNama', 'newItemSpec', 'newItemJumlah', 'newItemHarga', 'newItemSumberId']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function addItem()
    {
        // Validasi Dasar
        $rules = [
            'newItemJumlah' => 'required|integer|min:1',
            'newItemHarga' => 'required|numeric|min:0',
            'newItemSumberId' => 'required|exists:sumber_danas,id',
        ];

        // Validasi Berdasarkan Mode
        if ($this->modeInput == 'baru') {
            $rules['newItemNama'] = 'required|string';
            $rules['newItemSpec'] = 'nullable|string';
        } else {
            $rules['existingBarangId'] = 'required|exists:barangs,id';
        }

        $this->validate($rules);

        $sumber = SumberDana::find($this->newItemSumberId);

        // Logika Nama & Spec
        if ($this->modeInput == 'restock') {
            $barangExisting = Barang::find($this->existingBarangId);
            $nama = $barangExisting->nama_barang;
            $spec = "Penambahan Stok (Kode: {$barangExisting->kode_barang})";
            $barangId = $barangExisting->id;
        } else {
            $nama = $this->newItemNama;
            $spec = $this->newItemSpec;
            $barangId = null; // Barang Baru
        }

        $this->items[] = [
            'nama' => $nama,
            'spesifikasi' => $spec,
            'jumlah' => (int)$this->newItemJumlah,
            'harga' => (float)$this->newItemHarga,
            'total' => (int)$this->newItemJumlah * (float)$this->newItemHarga,
            'sumber_dana_id' => $this->newItemSumberId,
            'nama_sumber' => $sumber ? $sumber->nama_sumber : '-',
            'barang_id' => $barangId, // <--- SIMPAN ID BARANG DISINI
        ];

        // Reset Form
        $this->reset(['newItemNama', 'newItemSpec', 'newItemJumlah', 'newItemHarga', 'newItemSumberId', 'existingBarangId', 'modeInput']);
        $this->modeInput = 'baru'; // Kembalikan default ke baru
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    // Cari fungsi ajukanRab() dan ubah menjadi seperti ini:

    public function ajukanRab()
    {
        // IZINKAN Penjaga Gudang DAN Kepala Gudang
        if (!in_array(Auth::user()->peran, ['penjaga_gudang', 'kepala_gudang'])) return;

        $this->validate([
            'judul' => 'required|min:5',
            'items' => 'required|array|min:1'
        ]);

        // LOGIKA STATUS AWAL
        // Jika Penjaga Gudang -> 'diajukan' (Harus dicek Kepala Gudang dulu)
        // Jika Kepala Gudang -> 'menunggu_bendahara' (Langsung ke Bendahara)
        $statusAwal = (Auth::user()->peran === 'kepala_gudang') ? 'menunggu_bendahara' : 'diajukan';

        DB::transaction(function () use ($statusAwal) {
            $rab = RabPengadaan::create([
                'user_id' => Auth::id(),
                'judul' => $this->judul,
                'keterangan' => $this->keterangan,
                'tanggal_pengajuan' => now(),
                'status' => $statusAwal,

                // Jika Kepala Gudang yang buat, otomatis kolom 'disetujui_oleh' diisi dirinya sendiri
                'disetujui_oleh' => (Auth::user()->peran === 'kepala_gudang') ? Auth::id() : null,
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
                    'barang_id' => $item['barang_id'] ?? null, // Support Restock
                ]);
            }

            // Opsi Tambahan: Jika Kepala Gudang, langsung redirect WA ke Bendahara (Fitur Opsional)
            // if(Auth::user()->peran === 'kepala_gudang') { ... logika wa ... }
        });

        session()->flash('message', 'RAB berhasil dibuat dan diteruskan ke ' . ($statusAwal == 'menunggu_bendahara' ? 'Bendahara' : 'Kepala Gudang') . '.');
        $this->closeCreateModal();
    }


    public function resetInput()
    {
        // Mereset semua properti form ke nilai awal
        $this->reset([
            'judul',
            'keterangan',
            'items',
            'newItemNama',
            'newItemSpec',
            'newItemJumlah',
            'newItemHarga',
            'newItemSumberId',
            'modeInput',
            'existingBarangId'
        ]);

        // Mereset pesan error validasi (jika ada)
        $this->resetErrorBag();
    }
    // ---------------------------

    public function mount()
    {
        // Cek apakah ada parameter 'restock_id' di URL
        if (request()->has('restock_id')) {
            $this->resetInput(); // <--- SEKARANG INI AKAN BERHASIL

            $this->modeInput = 'restock';
            $this->existingBarangId = request()->query('restock_id');
            $this->showCreateModal = true;

            // Opsional: Beri judul default
            $barang = Barang::find($this->existingBarangId);
            if ($barang) {
                $this->judul = "Restock Barang: " . $barang->nama_barang;
            }
        }
    }


    // --- 2. FITUR UTAMA: DETAIL & PROSES (SEMUA ROLE) ---
    public function showDetail($id)
    {
        $this->selectedRab = RabPengadaan::with(['items', 'pengaju', 'peninjau'])->findOrFail($id);
        $this->catatan_kepala = $this->selectedRab->catatan_kepala;

        // Siapkan data untuk Modal Input Teknis (Jika Penjaga Gudang & Status Disetujui Bendahara)
        if (Auth::user()->peran === 'penjaga_gudang' && $this->selectedRab->status === 'disetujui_bendahara') {
            foreach ($this->selectedRab->items as $item) {
                $this->procurementItems[$item->id] = [
                    'kode' => '',
                    'kategori_id' => '',
                    'ruangan_id' => '',
                ];
            }
        }

        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedRab = null;
    }

    // --- LOGIKA KEPALA GUDANG: SETUJU (KE WA BENDAHARA) ---
    public function teruskanKeBendahara()
    {
        if (Auth::user()->peran !== 'kepala_gudang') return;

        // 1. Update Status RAB
        $this->selectedRab->update([
            'status' => 'menunggu_bendahara',
            'disetujui_oleh' => Auth::id(),
            'catatan_kepala' => $this->catatan_kepala
        ]);

        // 2. Ambil Data Bendahara
        $bendahara = User::where('peran', 'bendahara')->first();
        $noHp = $bendahara ? $bendahara->no_hp : '';

        // --- LOGIKA FORMATTING NOMOR HP (AUTO 62) ---
        if ($noHp) {
            // Bersihkan karakter aneh (spasi, strip, dll), hanya ambil angka
            $noHp = preg_replace('/[^0-9]/', '', $noHp);

            // Jika diawali angka 0, ganti dengan 62
            if (substr($noHp, 0, 1) === '0') {
                $noHp = '62' . substr($noHp, 1);
            }
        }
        // ---------------------------------------------

        // 3. Susun Pesan WhatsApp
        $linkSistem = url('/rab');
        $judulRab = $this->selectedRab->judul;
        $pesan = "Halo Bendahara, ada pengajuan RAB baru: *{$judulRab}*.\n\nMohon segera dicek dan diverifikasi melalui sistem.\nLink: $linkSistem";

        session()->flash('message', 'RAB disetujui. Mengarahkan ke WhatsApp Bendahara...');
        $this->closeModal();

        // 4. Redirect ke API WhatsApp
        if ($noHp) {
            $this->redirect("https://api.whatsapp.com/send?phone={$noHp}&text=" . urlencode($pesan));
        } else {
            // Fallback jika Bendahara belum input No HP
            session()->flash('error', 'Nomor HP Bendahara belum terdaftar di sistem.');
        }
    }

    // --- LOGIKA BENDAHARA: EDIT & SETUJU ---


    // Update fungsi edit agar mereset status tambah baru
    public function editItemBendahara($itemId)
    {
        $this->editingItemId = $itemId;
        $item = RabItem::find($itemId);

        $this->editJumlah = $item->jumlah;
        $this->editSumberId = $item->sumber_dana_id;

        // Reset mode tambah sumber dana setiap kali ganti item
        $this->isAddingSumber = false;
        $this->newSumberName = '';
    }

    // Fungsi Toggle (Buka/Tutup Input)
    public function toggleAddSumber()
    {
        $this->isAddingSumber = !$this->isAddingSumber;
        $this->newSumberName = ''; // Bersihkan input saat di-toggle
    }

    // Fungsi Simpan Sumber Dana Baru
    public function saveNewSumber()
    {
        $this->validate([
            'newSumberName' => 'required|string|min:3|unique:sumber_danas,nama_sumber'
        ]);

        // Buat Sumber Dana Baru
        $sumber = SumberDana::create([
            'nama_sumber' => $this->newSumberName
        ]);

        // Otomatis pilih sumber dana yang baru dibuat
        $this->editSumberId = $sumber->id;

        // Kembalikan ke mode dropdown
        $this->isAddingSumber = false;
        $this->newSumberName = '';

        // Opsional: Notifikasi kecil (Toast)
        // $this->dispatch('notify', message: 'Sumber dana baru ditambahkan!'); 
    }

    public function saveItemBendahara($itemId)
    {
        $item = RabItem::find($itemId);

        $item->jumlah = $this->editJumlah;
        // Simpan perubahan sumber dana
        $item->sumber_dana_id = $this->editSumberId;

        $item->harga_total = $item->jumlah * $item->harga_satuan; // Hitung ulang total
        $item->save();

        $this->editingItemId = null;
        $this->selectedRab->refresh();
        $this->isAddingSumber = false;
        // Opsional: Beri notifikasi kecil
        // session()->flash('message', 'Item berhasil diperbarui.');
    }

    public function hapusItemBendahara($itemId)
    {
        RabItem::destroy($itemId);
        $this->selectedRab->refresh();
    }

    public function setujuiOlehBendahara()
    {
        if (Auth::user()->peran !== 'bendahara') return;

        // --- VALIDASI TAMBAHAN: CEK JUMLAH ITEM ---
        // Hitung jumlah item di database untuk RAB ini
        $jumlahItem = $this->selectedRab->items()->count();

        if ($jumlahItem < 1) {
            // Jika kosong, kirim pesan error dan hentikan proses
            session()->flash('error', 'Gagal menyetujui! RAB tidak memiliki item barang. Silakan tolak pengajuan ini jika memang tidak valid.');

            // Opsional: Tutup modal agar user sadar
            // $this->closeModal(); 
            return;
        }
        // ------------------------------------------

        $this->selectedRab->update([
            'status' => 'disetujui_bendahara',
            'catatan_kepala' => $this->catatan_kepala
        ]);

        session()->flash('message', 'RAB disetujui. Dikembalikan ke Penjaga Gudang untuk belanja.');
        $this->closeModal();
    }

    private function generateKodeBarang($namaBarang)
    {
        // A. BERSIHKAN STRING
        // Ubah ke huruf besar & hapus semua karakter selain huruf (angka/simbol hilang)
        $cleanName = strtoupper(preg_replace('/[^a-zA-Z]/', '', $namaBarang));

        // B. AMBIL HURUF KONSONAN (Hapus A, I, U, E, O)
        $consonants = str_replace(['A', 'I', 'U', 'E', 'O'], '', $cleanName);

        // C. TENTUKAN PREFIX (Max 4 Karakter)
        // Jika konsonan cukup (minimal 2), pakai konsonan. 
        // Jika nama barang pendek/vokal semua (misal "Audio"), pakai nama asli yang dibersihkan.
        if (strlen($consonants) >= 2) {
            $prefix = substr($consonants, 0, 4);
        } else {
            // Fallback: Ambil 3-4 huruf pertama dari nama asli jika konsonan habis
            $prefix = substr($cleanName, 0, 4);
        }

        // Jika prefix masih kosong (misal nama barang cuma simbol/angka), pakai default
        if (empty($prefix)) {
            $prefix = "ITEM";
        }

        // D. FORMAT: PREFIX-TAHUN-URUTAN (Contoh: MNTR-2026-0001)
        $year = date('Y');
        $codeBase = $prefix . '-' . $year . '-';

        // E. CEK URUTAN TERAKHIR DI DATABASE BERDASARKAN PREFIX INI
        $lastBarang = Barang::where('kode_barang', 'like', $codeBase . '%')
            ->orderByRaw('LENGTH(kode_barang) DESC') // Urutkan panjang dulu biar 10 tidak dianggap sebelum 2
            ->orderBy('kode_barang', 'desc')
            ->first();

        if (!$lastBarang) {
            $number = 1;
        } else {
            // Pecah string kode terakhir berdasarkan strip '-'
            // Contoh: MNTR-2026-0005 -> diambil 0005
            $parts = explode('-', $lastBarang->kode_barang);
            $lastNumber = end($parts); // Ambil elemen terakhir
            $number = intval($lastNumber) + 1;
        }

        // Gabungkan semua
        return $codeBase . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // --- LOGIKA PENJAGA GUDANG: INPUT DATA TEKNIS (FASE PENGADAAN) ---
    public function laporBarangDatang()
    {
        if (Auth::user()->peran !== 'penjaga_gudang') return;

        // Validasi input kategori & ruangan saja (Kode barang otomatis)
        $itemsBaru = $this->selectedRab->items->whereNull('barang_id');

        if ($itemsBaru->count() > 0) {
            $this->validate([
                'procurementItems.*.kategori_id' => 'required',
                'procurementItems.*.ruangan_id' => 'required',
            ]);
        }

        DB::transaction(function () {
            foreach ($this->procurementItems as $itemId => $data) {
                $item = RabItem::find($itemId);

                // Proses hanya untuk barang baru (bukan restock)
                if (!$item->barang_id) {

                    // --- LOGIKA GENERATE KODE ---
                    // Kirim nama barang ke fungsi generator
                    do {
                        $kodeOtomatis = $this->generateKodeBarang($item->nama_barang_baru);

                        // Cek double protection (memastikan unik)
                        $exists = Barang::where('kode_barang', $kodeOtomatis)->exists();
                    } while ($exists);
                    // -----------------------------

                    $item->update([
                        'kode_barang_fix' => $kodeOtomatis,
                        'kategori_id_fix' => $data['kategori_id'],
                        'ruangan_id_fix' => $data['ruangan_id'],
                    ]);
                }
            }

            $this->selectedRab->update(['status' => 'menunggu_verifikasi']);
        });

        session()->flash('message', 'Laporan diterima. Kode Barang (Konsonan) berhasil dibuat otomatis.');
        $this->closeModal();
    }

    // --- LOGIKA KEPALA GUDANG: VERIFIKASI AKHIR (INSERT KE BARANG) ---
    public function verifikasiAkhir()
    {
        if (Auth::user()->peran !== 'kepala_gudang') return;

        DB::transaction(function () {
            foreach ($this->selectedRab->items as $item) {

                // LOGIKA BARU: CEK APAKAH INI RESTOCK?
                if ($item->barang_id) {
                    // KASUS 1: RESTOCK (Update Barang Lama)
                    $barang = Barang::find($item->barang_id);
                    if ($barang) {
                        $barang->jumlah_total += $item->jumlah;
                        $barang->jumlah_saat_ini += $item->jumlah;
                        $barang->save();
                    }
                    $finalBarangId = $item->barang_id; // Untuk riwayat pengadaan

                } else {
                    // KASUS 2: BARANG BARU (Create)
                    $barang = Barang::create([
                        'kode_barang' => $item->kode_barang_fix,
                        'nama_barang' => $item->nama_barang_baru,
                        'kategori_id' => $item->kategori_id_fix,
                        'ruangan_id' => $item->ruangan_id_fix,
                        'jumlah_total' => $item->jumlah,
                        'jumlah_saat_ini' => $item->jumlah,
                    ]);
                    $finalBarangId = $barang->id;
                }

                // CATAT RIWAYAT PENGADAAN (Berlaku untuk keduanya)
                PengadaanBarang::create([
                    'barang_id' => $finalBarangId,
                    'jumlah' => $item->jumlah,
                    'harga_satuan' => $item->harga_satuan,
                    'total_harga' => $item->harga_total,
                    'sumber_dana_id' => $item->sumber_dana_id,
                    'tanggal_pengadaan' => now(),
                    'user_id' => Auth::id(),
                ]);
            }

            $this->selectedRab->update(['status' => 'selesai']);
        });

        session()->flash('message', 'Verifikasi Berhasil! Stok inventaris telah diperbarui.');
        $this->closeModal();
    }

    public function tolakRab()
    {
        $this->selectedRab->update(['status' => 'ditolak', 'catatan_kepala' => $this->catatan_kepala]);
        $this->closeModal();
    }

    public function render()
    {
        $user = Auth::user();
        $rabData = ['sumberDanas' => SumberDana::all()];

        // Data untuk Dropdown di Modal Pengadaan (Penjaga Gudang)
        if ($user->peran == 'penjaga_gudang') {
            $rabData['kategoris'] = Kategori::all();
            $rabData['ruangans'] = Ruangan::all();
        }

        // QUERY SESUAI ROLE
        if ($user->peran === 'kepala_gudang') {
            // Tab 1: Menunggu Persetujuan Awal OR Menunggu Verifikasi Akhir
            $rabData['rabDiajukan'] = RabPengadaan::whereIn('status', ['diajukan', 'menunggu_verifikasi'])
                ->latest()->paginate(10);
            $rabData['rabDiproses'] = RabPengadaan::whereIn('status', ['menunggu_bendahara', 'disetujui_bendahara', 'selesai', 'ditolak'])
                ->latest()->paginate(10, ['*'], 'historyPage');
        } elseif ($user->peran === 'bendahara') {
            // Bendahara hanya fokus yang statusnya 'menunggu_bendahara'
            $rabData['rabDiajukan'] = RabPengadaan::where('status', 'menunggu_bendahara')->latest()->paginate(10);
            $rabData['rabDiproses'] = RabPengadaan::where('status', '!=', 'menunggu_bendahara')->latest()->paginate(10);
        } else { // Penjaga Gudang
            $rabData['rabSaya'] = RabPengadaan::with(['pengaju', 'peninjau']) // Load data pengaju
                // ->where('user_id', $user->id) // <--- HAPUS/KOMENTARI BARIS INI
                ->latest('tanggal_pengajuan')
                ->paginate(10, ['*'], 'myPage');
        }
        $rabData['barangs'] = Barang::all();

        return view('livewire.rab.index', $rabData);
    }
}
