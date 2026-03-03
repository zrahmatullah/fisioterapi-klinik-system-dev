<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpEmail extends Model
{
    protected $table = 'otp_email';

    protected $fillable = [
        'email',
        'kode_otp',
        'expired_at',
        'is_used',
        'purpose',
        'attempt',
        'reset_token'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    public function logs()
    {
        return $this->hasMany(OtpEmailLog::class);
    }
}
