<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeluhanAnak extends Model
{
    use HasFactory;

    protected $table = 'keluhan_anak';

    protected $fillable = [
        'profile_anak_id',
        'status_aktif',
        'no_keluhan',
        'kategori_keluhan',
        'tanggal_keluhan',
        'isi',
        'status_tanggapan',
        'tanggapan_keluhan',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function anak()
    {
        return $this->belongsTo(
            ProfileAnak::class,
            'profile_anak_id'
        );
    }

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }
}