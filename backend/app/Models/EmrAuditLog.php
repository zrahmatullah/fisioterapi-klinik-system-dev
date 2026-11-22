<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmrAuditLog extends Model
{
    use HasFactory;

    protected $table = 'emr_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'profile_anak_id',
        'emr_record_id',
        'action',
        'alasan_akses',
        'details',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(UserLogin::class, 'user_id');
    }

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }
}
