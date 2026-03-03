<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpEmailLog extends Model
{
    protected $table = 'otp_email_logs';

    protected $fillable = [
        'otp_email_id',
        'email',
        'status',
        'response'
    ];

    public function otp()
    {
        return $this->belongsTo(OtpEmail::class, 'otp_email_id');
    }
}
