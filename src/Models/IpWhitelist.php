<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpWhitelist extends Model
{
    protected $fillable = [
        'ip_address',
        'description',
        'expires_at',
        'created_by'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isValid(): bool
    {
        return !$this->expires_at || $this->expires_at->isFuture();
    }
}