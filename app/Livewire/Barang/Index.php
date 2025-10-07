<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Ruangan;
use App\Models\Transaksi;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app')]
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
        $this->barang_id = null;
        $this->kode_barang = '';
        $this->nama_barang = '';
        $this->kategori_id = '';
        $this->ruangan_id = '';
        $this->jumlah_total = '';
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
        $validatedData = $this->validate([
            'kode_barang'   => ['required', 'string', Rule::unique('barangs', 'kode_barang')->ignore($this->barang_id)],
            'nama_barang'   => 'required|string|min:3',
            'kategori_id'   => 'required|exists:kategoris,id',
            'ruangan_id'    => 'required|exists:ruangans,id',
            'jumlah_total'  => 'required|integer|min:0',
        ], [
            'kode_barang.unique' => 'Kode barang ini sudah digunakan.'
        ]);

        if ($this->barang_id) {
            // Logic untuk UPDATE
            $barang = Barang::find($this->barang_id);
            $jumlahTotalLama = $barang->jumlah_total;
            // Update data barang dengan data tervalidasi
            $barang->update($validatedData);

            // Hitung selisih antara jumlah baru dan lama
            $selisih = $this->jumlah_total - $jumlahTotalLama;

            // Sesuaikan jumlah saat ini berdasarkan selisih
            // method increment() bisa menangani nilai positif (menambah) dan negatif (mengurangi)
            $barang->increment('jumlah_saat_ini', $selisih);

            session()->flash('message', 'Data barang berhasil diperbarui.');
        } else {
            // Logic untuk CREATE
            $validatedData['jumlah_saat_ini'] = $this->jumlah_total;
            Barang::create($validatedData);
            session()->flash('message', 'Barang baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $this->barang_id = $id;
        $this->kode_barang = $barang->kode_barang;
        $this->nama_barang = $barang->nama_barang;
        $this->kategori_id = $barang->kategori_id;
        $this->ruangan_id = $barang->ruangan_id;
        $this->jumlah_total = $barang->jumlah_total;
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
    public function render()
    {

        $kategoris = Kategori::all();
        $ruangans = Ruangan::all();
        $barangs = Barang::with('kategori', 'ruangan')
            ->where(function ($query) {
                // Terapkan pencarian pada nama atau kode barang
                $query->where('nama_barang', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_barang', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterKategori, function ($query) {
                $query->where('kategori_id', $this->filterKategori);
            })
            ->latest()
            ->paginate(10);
        $detailBarang = null;
        if ($this->detailBarangId) {
            $barang = Barang::findOrFail($this->detailBarangId);

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
            ];
        }


        return view('livewire.barang.index', [
            'barangs' => $barangs,
            'kategoris' => $kategoris,
            'detailBarang' => $detailBarang,
            'ruangans' => $ruangans,
        ]);
    }
}
