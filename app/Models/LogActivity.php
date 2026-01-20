<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    use HasFactory;

    protected $table = 'log_activity';

    protected $fillable = ['user_id', 'allocation_id', 'penilaian_id', 'action'];

    // app/Models/LogActivity.php

public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}

public function allocation()
{
    return $this->belongsTo(\App\Models\Allocation::class);
}
}