<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasienWali extends Model
{
    use HasFactory;

    protected $table = 'pasien_wali';

    protected $fillable = [
        'profile_anak_id',
        'user_profile_id',
        'nama_wali',
        'hubungan',
        'no_hp',
        'pekerjaan',
        'email',
        'alamat',
        'is_kontak_darurat',
        'is_wali_utama',
    ];

    protected $casts = [
        'is_kontak_darurat' => 'boolean',
        'is_wali_utama' => 'boolean',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }
}
