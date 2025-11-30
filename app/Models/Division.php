<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * Relationship dengan User
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope untuk divisi aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}