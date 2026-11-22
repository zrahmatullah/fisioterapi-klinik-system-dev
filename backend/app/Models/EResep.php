<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EResep extends Model
{
    use HasFactory;

    protected $table = 'e_resep';

    protected $fillable = [
        'nomor_resep',
        'registrasi_anak_id',
        'profile_anak_id',
        'dokter_id',
        'status',
        'catatan',
        'catatan_telaah',
        'verified_at',
        'verified_by',
        'dispensed_at',
        'dispensed_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'dispensed_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(EResepItem::class, 'e_resep_id');
    }

    public function registrasiAnak()
    {
        return $this->belongsTo(RegistrasiAnak::class, 'registrasi_anak_id');
    }

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }

    public function dokter()
    {
        return $this->belongsTo(UserProfile::class, 'dokter_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(UserLogin::class, 'verified_by');
    }

    public function dispensedBy()
    {
        return $this->belongsTo(UserLogin::class, 'dispensed_by');
    }
}
