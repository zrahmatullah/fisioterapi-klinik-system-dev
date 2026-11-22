<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterIcd10 extends Model
{
    use HasFactory;

    protected $table = 'master_icd10';

    protected $fillable = [
        'kode',
        'deskripsi_en',
        'deskripsi_id',
        'kategori',
        'is_terapi_anak',
    ];

    protected $casts = [
        'is_terapi_anak' => 'boolean',
    ];
}
