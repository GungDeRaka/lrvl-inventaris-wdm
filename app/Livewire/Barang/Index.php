<?php

namespace App\Livewire\Barang;

use App\Models\Barang; 
use Livewire\Component;
use Livewire\WithPagination;
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        // Ambil data barang, urutkan dari yang terbaru, dan gunakan pagination
        $barangs = Barang::with('kategori', 'ruangan')->latest()->paginate(10);

        return view('livewire.barang.index', [
            'barangs' => $barangs,
        ]);
    }
}