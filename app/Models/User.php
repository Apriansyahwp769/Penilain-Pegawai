<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email', 
        'password',
        'nip',
        'division_id',
        'position_id', 
        'phone',
        'join_date',
        'role',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'join_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // ========================================
    // RELASI KE ALLOCATIONS (TAMBAHAN BARU)
    // ========================================
    
    /**
     * Relasi sebagai Penilai (untuk Ketua Divisi)
     * User bisa menilai banyak staff
     */
    public function allocationsAsPenilai()
    {
        return $this->hasMany(Allocation::class, 'penilai_id');
    }

    /**
     * Relasi sebagai Yang Dinilai (untuk Staff)
     * User bisa dinilai oleh banyak ketua divisi
     */
    public function allocationsAsDinilai()
    {
        return $this->hasMany(Allocation::class, 'dinilai_id');
    }

    /**
     * Get total allocations sebagai penilai
     */
    public function getTotalAllocationsAsPenilaiAttribute()
    {
        return $this->allocationsAsPenilai()->count();
    }

    /**
     * Get total allocations sebagai yang dinilai
     */
    public function getTotalAllocationsAsDinilaiAttribute()
    {
        return $this->allocationsAsDinilai()->count();
    }

    /**
     * Get completed allocations sebagai yang dinilai
     */
    public function getCompletedAllocationsAttribute()
    {
        return $this->allocationsAsDinilai()
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get pending allocations sebagai yang dinilai
     */
    public function getPendingAllocationsAttribute()
    {
        return $this->allocationsAsDinilai()
            ->where('status', 'pending')
            ->count();
    }

    // ========================================
    // FUNGSI ASLI (TIDAK DIUBAH)
    // ========================================

    /**
     * Relationship dengan Division
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Relationship dengan Position  
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Scope untuk user aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk role tertentu
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Check jika user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check jika user adalah ketua divisi
     */
    public function isKetuaDivisi()
    {
        return $this->role === 'ketua_divisi';
    }

    /**
     * Check jika user adalah staff
     */
    public function isStaff()
    {
        return $this->role === 'staff';
    }
}