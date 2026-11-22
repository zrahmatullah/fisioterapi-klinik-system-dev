<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSigna extends Model
{
    use HasFactory;

    protected $table = 'master_signa';

    protected $fillable = [
        'code',
        'signa_text',
        'times_per_day',
        'dose_qty',
        'unit',
        'status_aktif',
    ];

    protected $casts = [
        'times_per_day' => 'integer',
        'dose_qty' => 'float',
        'status_aktif' => 'boolean',
    ];
}
