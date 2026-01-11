<?php

namespace Tests\Feature\Livewire\Dashboard;

use App\Livewire\Dashboard\Index;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Siswa;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardIndexTest extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $siswa;
    public $ruangan;
    public $barang;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Data Dummy
        $this->user = User::factory()->create(); // Admin
        $this->ruangan = Ruangan::factory()->create(['nama_ruangan' => 'Gudang Utama']);
        $this->siswa = Siswa::factory()->create([
            'nis' => '12345', 
            'nama' => 'Budi',
            'is_ditangguhkan' => false
        ]);
        
        $this->barang = Barang::factory()->create([
            'nama_barang' => 'Laptop Asus',
            'kode_barang' => 'LPT-001',
            'ruangan_id' => $this->ruangan->id,
            'jumlah_total' => 10,
            'jumlah_saat_ini' => 10,
            'jumlah_rusak' => 0
        ]);

        // Login sebagai admin
        $this->actingAs($this->user);
    }

    /** @test */
    public function halaman_dashboard_bisa_dirender()
    {
        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Dashboard Transaksi');
    }

    /** @test */
    public function bisa_mencari_siswa_berdasarkan_nis()
    {
        Livewire::test(Index::class)
            ->set('nis', '12345') // Trigger updatedNis
            ->assertSet('siswaDitemukan.id', $this->siswa->id)
            ->assertSee($this->siswa->nama);
    }

    /** @test */
    public function bisa_mencari_barang_dan_menambah_ke_keranjang()
    {
        Livewire::test(Index::class)
            ->set('searchBarang', 'Laptop') // Trigger updatedSearchBarang
            ->assertSee('Laptop Asus')
            ->call('tambahKeKeranjang', $this->barang->id, $this->barang->nama_barang, $this->ruangan->nama_ruangan)
            ->assertSet('keranjang.0.id', $this->barang->id)
            ->assertSet('keranjang.0.kuantitas', 1);
    }

    /** @test */
    public function admin_bisa_menyimpan_peminjaman_baru()
    {
        $waktuKembali = now()->addHours(2);

        Livewire::test(Index::class)
            ->call('openTransactionModal')
            ->set('nis', '12345') // Set Siswa
            ->call('tambahKeKeranjang', $this->barang->id, $this->barang->nama_barang, $this->ruangan->nama_ruangan)
            ->set('ruang_pemakaian', 'Lab Komputer') // Pastikan beda dengan asal ruangan (Gudang Utama)
            ->set('waktu_kembali', $waktuKembali)
            ->call('simpanPeminjaman')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal'); // Asumsi ada event close modal atau session flash

        // Cek Database
        $this->assertDatabaseHas('transaksis', [
            'siswa_id' => $this->siswa->id,
            'status' => 'dipinjam',
            'ruang_pemakaian' => 'Lab Komputer'
        ]);

        // Cek Stok Berkurang
        $this->assertDatabaseHas('barangs', [
            'id' => $this->barang->id,
            'jumlah_saat_ini' => 9 // 10 - 1
        ]);
    }

    /** @test */
    public function gagal_simpan_jika_ruang_pemakaian_sama_dengan_asal_barang()
    {
        Livewire::test(Index::class)
            ->set('nis', '12345')
            ->call('tambahKeKeranjang', $this->barang->id, $this->barang->nama_barang, $this->ruangan->nama_ruangan)
            ->set('ruang_pemakaian', 'Gudang Utama') // Sama dengan asal barang
            ->set('waktu_kembali', now()->addHours(2))
            ->call('simpanPeminjaman')
            ->assertHasErrors(['ruang_pemakaian']);
    }

    /** @test */
    public function admin_bisa_menyetujui_permintaan_siswa()
    {
        // Buat transaksi status 'diajukan'
        $transaksi = Transaksi::factory()->create([
            'siswa_id' => $this->siswa->id,
            'status' => 'diajukan',
            'user_id' => null
        ]);
        
        // Attach barang (pivot)
        $transaksi->barangs()->attach($this->barang->id, ['kuantitas' => 2]);

        Livewire::test(Index::class)
            ->call('setujuiPermintaan', $transaksi->id)
            ->assertSee('Permintaan berhasil disetujui');

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'status' => 'disetujui',
            'user_id' => $this->user->id
        ]);

        // Cek Stok Berkurang (10 - 2 = 8)
        $this->assertEquals(8, $this->barang->fresh()->jumlah_saat_ini);
    }

    /** @test */
    public function admin_bisa_menolak_permintaan_siswa()
    {
        $transaksi = Transaksi::factory()->create(['status' => 'diajukan']);

        Livewire::test(Index::class)
            ->call('tolakPermintaan', $transaksi->id)
            ->set('alasan_penolakan', 'Stok menipis')
            ->call('prosesPenolakan')
            ->assertSee('Permintaan telah ditolak');

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'status' => 'ditolak',
            'alasan_penolakan' => 'Stok menipis'
        ]);
    }

    /** @test */
    public function admin_bisa_memproses_pengembalian_barang_tanpa_kerusakan()
    {
        // Setup Transaksi 'dipinjam'
        $transaksi = Transaksi::factory()->create(['status' => 'dipinjam']);
        $transaksi->barangs()->attach($this->barang->id, ['kuantitas' => 1]);
        
        // Kurangi stok awal seolah-olah sedang dipinjam
        $this->barang->update(['jumlah_saat_ini' => 9]);

        Livewire::test(Index::class)
            ->call('konfirmasiPengembalian', $transaksi->id)
            ->call('prosesPengembalian') // Default kerusakanItems kosong/0
            ->assertSee('Barang berhasil dikembalikan');

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'status' => 'dikembalikan'
        ]);

        // Stok kembali normal (9 + 1 = 10)
        $this->assertEquals(10, $this->barang->fresh()->jumlah_saat_ini);
    }

    /** @test */
    public function admin_bisa_memproses_pengembalian_barang_dengan_kerusakan()
    {
        // Setup Transaksi
        $transaksi = Transaksi::factory()->create([
            'siswa_id' => $this->siswa->id,
            'status' => 'dipinjam'
        ]);
        $transaksi->barangs()->attach($this->barang->id, ['kuantitas' => 2]);
        $this->barang->update(['jumlah_saat_ini' => 8]); // Stok sisa

        Livewire::test(Index::class)
            ->call('konfirmasiPengembalian', $transaksi->id)
            ->set("kerusakanItems.{$this->barang->id}", 1) // 1 Rusak dari 2 pinjaman
            ->call('prosesPengembalian');

        // Cek Transaksi Selesai
        $this->assertDatabaseHas('transaksis', ['id' => $transaksi->id, 'status' => 'dikembalikan']);

        // Cek Siswa Ditangguhkan
        $this->assertTrue($this->siswa->fresh()->is_ditangguhkan);

        // Cek Logika Barang
        $barangUpdated = $this->barang->fresh();
        // Stok saat ini: 8 (awal) + 1 (kembali bagus) = 9
        $this->assertEquals(9, $barangUpdated->jumlah_saat_ini);
        // Jumlah total aset berkurang: 10 - 1 (rusak) = 9
        $this->assertEquals(9, $barangUpdated->jumlah_total);
        // Jumlah rusak bertambah: 0 + 1 = 1
        $this->assertEquals(1, $barangUpdated->jumlah_rusak);
    }

    /** @test */
    public function admin_bisa_menyetujui_perpanjangan()
    {
        $transaksi = Transaksi::factory()->create([
            'status' => 'perpanjangan-diajukan',
            'waktu_kembali' => now()->addHour()
        ]);
        
        $waktuBaru = now()->addHours(5);

        Livewire::test(Index::class)
            ->call('bukaModalPerpanjangan', $transaksi->id)
            ->set('waktu_kembali_baru', $waktuBaru)
            ->call('setujuiPerpanjangan');

        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'status' => 'dipinjam',
            // Perlu format string karena database menyimpan datetime string
            'waktu_kembali' => $waktuBaru->format('Y-m-d H:i:s') 
        ]);
    }
}