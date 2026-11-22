<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'nama_supplier',
        'kontak_person',
        'no_telp',
        'email',
        'alamat',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];
}
