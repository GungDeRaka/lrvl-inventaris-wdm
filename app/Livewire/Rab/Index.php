<?php

namespace App\Livewire\Rab;

use App\Models\RabPengadaan;
use App\Models\RabItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // <-- Tambahkan Auth
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    // Properti untuk daftar RAB (Kepala Gudang)
    public $showDetailModal = false;
    public $selectedRab;
    public $catatan_kepala = '';

    // Properti untuk form pengajuan RAB (Penjaga Gudang)
    public $keterangan = '';
    public $items = [];
    public $newItemNama = '';
    public $newItemSpec = '';
    public $newItemJumlah = 1;
    public $newItemHarga = 0;

    // --- LOGIKA UNTUK PENGAJUAN RAB (dari Rab/Create) ---
    public function addItem()
    {
        $this->validate([
            'newItemNama' => 'required|string',
            'newItemJumlah' => 'required|integer|min:1',
            'newItemHarga' => 'required|numeric|min:0',
            'newItemSpec' => 'nullable|string',
        ]);

        $this->items[] = [
            'nama' => $this->newItemNama,
            'spesifikasi' => $this->newItemSpec,
            'jumlah' => (int)$this->newItemJumlah,
            'harga' => (float)$this->newItemHarga,
            'total' => (int)$this->newItemJumlah * (float)$this->newItemHarga,
        ];
        $this->reset(['newItemNama', 'newItemSpec', 'newItemJumlah', 'newItemHarga']);
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
                    ]);
                }
            });
            session()->flash('message', 'Pengajuan RAB berhasil dikirim.');
            $this->reset(['keterangan', 'items']);
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
        $this->selectedRab->update([
            'status' => $status, // 'disetujui' atau 'ditolak'
            'disetujui_oleh' => Auth::id(),
            'tanggal_keputusan' => now()->toDateString(),
            'catatan_kepala' => $this->catatan_kepala,
        ]);
        session()->flash('message', 'RAB telah berhasil di-' . ($status == 'disetujui' ? 'setujui' : 'tolak') . '.');
        $this->closeModal();
    }
    // --- AKHIR LOGIKA PERSETUJUAN ---

    public function render()
    {
        $user = Auth::user();
        $rabData = [];

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

        return view('livewire.rab.index', $rabData);
    }
}
