<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HasilPenilaian extends Model
{
    use HasFactory;

    protected $table = 'hasil_penilaian';

    protected $fillable = [
        'penilaian_id',
        'criterion_id',
        'skor',
        'file_penunjang'
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

    /**
     * Get full URL for file penunjang
     */
    public function getFilePenunjangUrlAttribute()
    {
        if ($this->file_penunjang) {
            return Storage::url($this->file_penunjang);
        }
        return null;
    }

    /**
     * Delete file penunjang when record is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($hasilPenilaian) {
            if ($hasilPenilaian->file_penunjang) {
                Storage::delete($hasilPenilaian->file_penunjang);
            }
        });
    }
}