<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriLayanan extends Model
{
    use HasFactory;

    protected $table = 'kategori_layanan';

    protected $fillable = [
        'kategori_layanan',
        'status_aktif',
    ];

    public function layanan()
    {
        return $this->hasMany(Layanan::class, 'kategori_layanan_id');
    }
}
