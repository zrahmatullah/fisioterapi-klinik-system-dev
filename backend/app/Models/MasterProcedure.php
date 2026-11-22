<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProcedure extends Model
{
    use HasFactory;

    protected $table = 'master_procedures';

    protected $fillable = [
        'icd9_code',
        'procedure_name',
        'category',
        'tariff_umum',
        'tariff_asuransi',
        'status_aktif',
    ];

    protected $casts = [
        'tariff_umum' => 'decimal:2',
        'tariff_asuransi' => 'decimal:2',
        'status_aktif' => 'boolean',
    ];
}
