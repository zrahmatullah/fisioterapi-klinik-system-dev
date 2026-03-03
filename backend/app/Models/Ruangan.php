<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangan';

    protected $fillable = [
        'ruangan',
        'status_aktif',
    ];

    public function terapis()
    {
        return $this->belongsToMany(
            UserProfile::class,
            'terapis_to_ruangan',
            'ruangan_id',
            'terapis_id'
        );
    }

}
