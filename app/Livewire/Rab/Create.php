<?php

namespace App\Livewire\Rab;

use App\Models\Barang;
use App\Models\RabPengadaan;
use Illuminate\Support\Facades\Auth;
use App\Models\RabItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $keterangan = '';
    public $items = []; // Array untuk menampung item RAB

    // Properti untuk form tambah item
    public $newItemNama = '';
    public $newItemSpec = '';
    public $newItemJumlah = 1;
    public $newItemHarga = 0;

    protected $rules = [
        'keterangan' => 'nullable|string',
        'items' => 'required|array|min:1',
        'items.*.nama' => 'required|string',
        'items.*.jumlah' => 'required|integer|min:1',
        'items.*.harga' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'items.required' => 'Minimal harus ada 1 item barang dalam pengajuan.',
        'items.*.nama.required' => 'Nama barang wajib diisi.',
        'items.*.jumlah.required' => 'Jumlah barang wajib diisi.',
        'items.*.jumlah.min' => 'Jumlah barang minimal 1.',
        'items.*.harga.required' => 'Harga satuan wajib diisi.',
    ];

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

        // Reset form item
        $this->reset(['newItemNama', 'newItemSpec', 'newItemJumlah', 'newItemHarga']);
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function ajukanRab()
    {
        // Hanya Penjaga Gudang yang bisa mengajukan
        if (Auth::user()->peran !== 'penjaga_gudang') {
            session()->flash('error', 'Hanya Penjaga Gudang yang dapat mengajukan RAB.');
            return;
        }

        $this->validate(); // Validasi semua data

        $this->validate(); // Validasi semua data

        try {
            DB::transaction(function () {
                $rab = RabPengadaan::create([
                    'user_id' => Auth::id(), // <-- PERBAIKAN DI SINI
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
            // Redirect ke halaman lain jika perlu, misal halaman list RAB

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengajukan RAB: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.rab.create');
    }
}
