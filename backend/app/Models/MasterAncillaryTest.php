<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAncillaryTest extends Model
{
    use HasFactory;

    protected $table = 'master_ancillary_tests';

    protected $fillable = [
        'code',
        'test_name',
        'category',
        'tariff_umum',
        'tariff_asuransi',
        'specimen_or_modality',
        'status_aktif',
    ];

    protected $casts = [
        'tariff_umum' => 'decimal:2',
        'tariff_asuransi' => 'decimal:2',
        'status_aktif' => 'boolean',
    ];
}
