<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Tambahkan ini

class Kategori extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Memudahkan saat create/update

    /**
     * Mendapatkan semua barang dalam kategori ini.
     */
    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class);
    }
}
 ?>