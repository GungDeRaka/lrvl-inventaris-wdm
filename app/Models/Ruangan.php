<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class Ruangan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Mendapatkan semua barang di ruangan ini.
     */
    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class);
    }
}
