<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTemplate extends Model
{
    use HasFactory;

    protected $table = 'surat_templates';

    protected $fillable = [
        'kode_template',
        'judul',
        'jenis_surat',
        'kop_surat_aktif',
        'format_nomor',
        'template_content',
        'status_aktif',
    ];

    protected $casts = [
        'kop_surat_aktif' => 'boolean',
        'status_aktif' => 'boolean',
    ];
}
