<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'siklus_id',
        'penilai_id',
        'dinilai_id',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Siklus
     */
    public function siklus()
    {
        return $this->belongsTo(Siklus::class, 'siklus_id');
    }

    /**
     * Relasi ke User sebagai Penilai (Ketua Divisi)
     */
    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    /**
     * Relasi ke User sebagai Yang Dinilai (Staff)
     */
    public function dinilai()
    {
        return $this->belongsTo(User::class, 'dinilai_id');
    }

    /**
     * Relasi ke Penilaian (TAMBAHAN BARU)
     */
    public function penilaian()
    {
        return $this->hasOne(Penilaian::class, 'allocation_id');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan siklus
     */
    public function scopeBySiklus($query, $siklusId)
    {
        return $query->where('siklus_id', $siklusId);
    }

    /**
     * Scope untuk filter berdasarkan penilai (TAMBAHAN BARU)
     */
    public function scopeByPenilai($query, $penilaiId)
    {
        return $query->where('penilai_id', $penilaiId);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            default => 'Unknown',
        };
    }
}