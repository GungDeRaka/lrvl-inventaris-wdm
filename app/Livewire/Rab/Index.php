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

    // Properti Edit Item (Bendahara)
    public $editingItemId = null;
    public $editJumlah = 0;
    
    // Properti Input Teknis (Penjaga Gudang - Fase Pengadaan)
    public $procurementItems = []; // Array untuk menampung input kode/kategori/ruang

    // --- 1. FITUR PENGAJUAN (PENJAGA GUDANG) ---
    public function openCreateModal() {
        $this->reset(['judul', 'keterangan', 'items', 'newItemNama', 'newItemSpec', 'newItemJumlah', 'newItemHarga', 'newItemSumberId']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal() { $this->showCreateModal = false; }

    public function addItem() {
        $this->validate([
            'newItemNama' => 'required|string',
            'newItemJumlah' => 'required|integer|min:1',
            'newItemHarga' => 'required|numeric|min:0',
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
        $this->reset(['newItemNama', 'newItemSpec', 'newItemJumlah', 'newItemHarga', 'newItemSumberId']);
    }

    public function removeItem($index) {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function ajukanRab() {
        if (Auth::user()->peran !== 'penjaga_gudang') return;
        
        // Validasi utama...
        $this->validate(['judul' => 'required|min:5', 'items' => 'required|array|min:1']);

        DB::transaction(function () {
            $rab = RabPengadaan::create([
                'user_id' => Auth::id(),
                'judul' => $this->judul,
                'keterangan' => $this->keterangan,
                'tanggal_pengajuan' => now(),
                'status' => 'diajukan', // Status Awal
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
        session()->flash('message', 'RAB berhasil diajukan ke Kepala Gudang.');
        $this->closeCreateModal();
    }

    // --- 2. FITUR UTAMA: DETAIL & PROSES (SEMUA ROLE) ---
    public function showDetail($id) {
        $this->selectedRab = RabPengadaan::with(['items', 'pengaju', 'peninjau'])->findOrFail($id);
        $this->catatan_kepala = $this->selectedRab->catatan_kepala;
        
        // Siapkan data untuk Modal Input Teknis (Jika Penjaga Gudang & Status Disetujui Bendahara)
        if(Auth::user()->peran === 'penjaga_gudang' && $this->selectedRab->status === 'disetujui_bendahara') {
            foreach($this->selectedRab->items as $item) {
                $this->procurementItems[$item->id] = [
                    'kode' => '',
                    'kategori_id' => '',
                    'ruangan_id' => '',
                ];
            }
        }
        
        $this->showDetailModal = true;
    }

    public function closeModal() { 
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
        if($noHp) {
            $this->redirect("https://api.whatsapp.com/send?phone={$noHp}&text=" . urlencode($pesan));
        } else {
            // Fallback jika Bendahara belum input No HP
            session()->flash('error', 'Nomor HP Bendahara belum terdaftar di sistem.');
        }
    }

    // --- LOGIKA BENDAHARA: EDIT & SETUJU ---
    public function editItemBendahara($itemId) {
        $this->editingItemId = $itemId;
        $item = RabItem::find($itemId);
        $this->editJumlah = $item->jumlah;
    }

    public function saveItemBendahara($itemId) {
        $item = RabItem::find($itemId);
        $item->jumlah = $this->editJumlah;
        $item->harga_total = $item->jumlah * $item->harga_satuan; // Hitung ulang total
        $item->save();
        $this->editingItemId = null;
        
        // Refresh selectedRab
        $this->selectedRab->refresh();
    }
    
    public function hapusItemBendahara($itemId) {
        RabItem::destroy($itemId);
        $this->selectedRab->refresh();
    }

    public function setujuiOlehBendahara() {
        if (Auth::user()->peran !== 'bendahara') return;

        $this->selectedRab->update([
            'status' => 'disetujui_bendahara',
            'catatan_kepala' => $this->catatan_kepala // Bendahara bisa update catatan
        ]);

        session()->flash('message', 'RAB disetujui. Dikembalikan ke Penjaga Gudang untuk belanja.');
        $this->closeModal();
    }

    // --- LOGIKA PENJAGA GUDANG: INPUT DATA TEKNIS (FASE PENGADAAN) ---
    public function laporBarangDatang() {
        if (Auth::user()->peran !== 'penjaga_gudang') return;
        
        $this->validate([
            'procurementItems.*.kode' => 'required|string|unique:barangs,kode_barang',
            'procurementItems.*.kategori_id' => 'required',
            'procurementItems.*.ruangan_id' => 'required',
        ]);

        DB::transaction(function() {
            foreach($this->procurementItems as $itemId => $data) {
                RabItem::where('id', $itemId)->update([
                    'kode_barang_fix' => $data['kode'],
                    'kategori_id_fix' => $data['kategori_id'],
                    'ruangan_id_fix' => $data['ruangan_id'],
                ]);
            }
            
            $this->selectedRab->update(['status' => 'menunggu_verifikasi']);
        });

        session()->flash('message', 'Data teknis disimpan. Menunggu verifikasi fisik Kepala Gudang.');
        $this->closeModal();
    }

    // --- LOGIKA KEPALA GUDANG: VERIFIKASI AKHIR (INSERT KE BARANG) ---
    public function verifikasiAkhir() {
        if (Auth::user()->peran !== 'kepala_gudang') return;

        DB::transaction(function() {
            foreach($this->selectedRab->items as $item) {
                // INSERT KE TABEL BARANG (Final)
                $barang = Barang::create([
                    'kode_barang' => $item->kode_barang_fix,
                    'nama_barang' => $item->nama_barang_baru,
                    'kategori_id' => $item->kategori_id_fix,
                    'ruangan_id' => $item->ruangan_id_fix,
                    'jumlah_total' => $item->jumlah,
                    'jumlah_saat_ini' => $item->jumlah,
                    // Foto dll bisa null dulu atau default
                ]);

                // INSERT KE RIWAYAT PENGADAAN
                PengadaanBarang::create([
                    'barang_id' => $barang->id,
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

        session()->flash('message', 'RAB Selesai! Barang resmi masuk inventaris.');
        $this->closeModal();
    }
    
    public function tolakRab() {
         $this->selectedRab->update(['status' => 'ditolak', 'catatan_kepala' => $this->catatan_kepala]);
         $this->closeModal();
    }

    public function render()
    {
        $user = Auth::user();
        $rabData = ['sumberDanas' => SumberDana::all()];
        
        // Data untuk Dropdown di Modal Pengadaan (Penjaga Gudang)
        if($user->peran == 'penjaga_gudang') {
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
            $rabData['rabSaya'] = RabPengadaan::where('user_id', $user->id)->latest()->paginate(10);
        }

        return view('livewire.rab.index', $rabData);
    }
}