<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'allocation_id',
        'skor_akhir',
        'status',
        'catatan'
    ];

    protected $casts = [
        'skor_akhir' => 'decimal:2',
    ];

    /**
     * Relasi ke Allocation
     */
    public function allocation()
    {
        return $this->belongsTo(Allocation::class, 'allocation_id');
    }

    /**
     * Get user yang dinilai (via allocation)
     */
    public function yangDinilai()
    {
        return $this->allocation->dinilai ?? null;
    }

    /**
     * Get user penilai (via allocation)
     */
    public function penilai()
    {
        return $this->allocation->penilai ?? null;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'selesai' => 'bg-green-100 text-green-800',
            'draft' => 'bg-yellow-100 text-yellow-800',
            'belum_dinilai' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'selesai' => 'Selesai',
            'draft' => 'Draft',
            'belum_dinilai' => 'Belum Dinilai',
            default => 'Unknown',
        };
    }
    public function hasilPenilaian()
    {
        return $this->hasMany(HasilPenilaian::class, 'penilaian_id');
    }
}
