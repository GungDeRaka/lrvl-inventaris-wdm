<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RabPengadaan extends Model {
    use HasFactory;
    protected $guarded = [];

    public function pengaju(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function peninjau(): BelongsTo {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
    public function items(): HasMany {
        return $this->hasMany(RabItem::class);
    }
}