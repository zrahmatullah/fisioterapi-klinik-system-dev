<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKelamin extends Model
{
    use HasFactory;

    protected $table = 'jenis_kelamin';

    protected $fillable = [
        'nama'
    ];

    /**
     * Relasi ke profile_anak
     */
    public function profileAnak()
    {
        return $this->hasMany(ProfileAnak::class, 'jenis_kelamin_id');
    }
    public function userProfiles()
    {
        return $this->hasMany(UserProfile::class, 'jenis_kelamin_id');
    }
}
