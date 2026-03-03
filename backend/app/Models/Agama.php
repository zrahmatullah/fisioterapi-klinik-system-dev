<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    use HasFactory;

    protected $table = 'agama_m';

    protected $fillable = [
        'nama'
    ];

    /**
     * Relasi ke profile_anak
     */
    public function profileAnak()
    {
        return $this->hasMany(ProfileAnak::class, 'agama_id');
    }
}
