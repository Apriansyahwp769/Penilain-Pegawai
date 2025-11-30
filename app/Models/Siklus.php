<?php
// app/Models/Siklus.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Siklus extends Model
{
    use HasFactory;

    protected $table = 'siklus';

    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_finalisasi',
        'status'
    ];

    // ATAU kalau mau lebih simple, gunakan guarded (tapi kurang aman):
    // protected $guarded = ['id'];

    // PENTING: Gunakan 'date' bukan 'datetime' untuk kolom DATE
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_finalisasi' => 'date',
    ];

    // ========================================
    // RELASI KE ALLOCATIONS (TAMBAHAN BARU)
    // ========================================
    
    /**
     * Relasi ke Allocations
     */
    public function allocations()
    {
        return $this->hasMany(Allocation::class, 'siklus_id');
    }

    /**
     * Get total allocations untuk siklus ini
     */
    public function getTotalAllocationsAttribute()
    {
        return $this->allocations()->count();
    }

    /**
     * Get completed allocations untuk siklus ini
     */
    public function getCompletedAllocationsAttribute()
    {
        return $this->allocations()->where('status', 'completed')->count();
    }

    /**
     * Get pending allocations untuk siklus ini
     */
    public function getPendingAllocationsAttribute()
    {
        return $this->allocations()->where('status', 'pending')->count();
    }

    /**
     * Get in progress allocations untuk siklus ini
     */
    public function getInProgressAllocationsAttribute()
    {
        return $this->allocations()->where('status', 'in_progress')->count();
    }

    // ========================================
    // FUNGSI ASLI (TIDAK DIUBAH)
    // ========================================

    // Scope untuk filter status
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Accessor untuk format tanggal
    public function getFormattedTanggalMulaiAttribute()
    {
        return $this->tanggal_mulai ? $this->tanggal_mulai->format('d/m/Y') : '-';
    }

    public function getFormattedTanggalSelesaiAttribute()
    {
        return $this->tanggal_selesai ? $this->tanggal_selesai->format('d/m/Y') : '-';
    }

    public function getFormattedTanggalFinalisasiAttribute()
    {
        return $this->tanggal_finalisasi ? $this->tanggal_finalisasi->format('d/m/Y') : '-';
    }

    // Helper method untuk check status
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}