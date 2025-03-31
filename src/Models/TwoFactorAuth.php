<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorAuth extends Model
{
    protected $table = 'two_factor_auth';
    
    protected $fillable = [
        'user_id',
        'secret_key',
        'is_enabled',
        'recovery_codes'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'recovery_codes' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}