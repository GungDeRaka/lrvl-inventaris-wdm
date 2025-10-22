<?php

namespace App\Livewire\Rab;

use App\Models\RabPengadaan;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;


#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $showDetailModal = false;
    public $selectedRab;
    public $catatan_kepala = '';

    public function showDetail($id)
    {
        $this->selectedRab = RabPengadaan::with('pengaju', 'items.barang')->findOrFail($id);
        $this->catatan_kepala = $this->selectedRab->catatan_kepala ?? '';
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedRab = null;
    }

    public function prosesKeputusan($status)
    {
        if (!$this->selectedRab) return;

        $this->validate(['catatan_kepala' => 'nullable|string']);

        $this->selectedRab->update([
            'status' => $status, // 'disetujui' atau 'ditolak'
            'disetujui_oleh' =>Auth::id(),
            'tanggal_keputusan' => now()->toDateString(),
            'catatan_kepala' => $this->catatan_kepala,
        ]);

        session()->flash('message', 'RAB telah berhasil di-' . ($status == 'disetujui' ? 'setujui' : 'tolak') . '.');
        $this->closeModal();
    }

    public function render()
    {
        // Ambil RAB yang masih diajukan
        $rabDiajukan = RabPengadaan::with('pengaju')
            ->where('status', 'diajukan')
            ->latest('tanggal_pengajuan')
            ->paginate(10);
            
        return view('livewire.rab.index', [
            'rabDiajukan' => $rabDiajukan,
        ]);
    }
}