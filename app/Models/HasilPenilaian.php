<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPenilaian extends Model
{
    use HasFactory;

    protected $table = 'hasil_penilaian';

    protected $fillable = [
        'penilaian_id',
        'criterion_id',
        'skor'
    ];

    protected $casts = [
        'skor' => 'decimal:2',
    ];

    /**
     * Relasi ke Penilaian
     */
    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id');
    }

    /**
     * Relasi ke Criterion
     */
    public function criterion()
    {
        return $this->belongsTo(Criterion::class, 'criterion_id');
    }
}