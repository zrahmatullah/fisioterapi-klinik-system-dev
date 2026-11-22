<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterIntakeTime extends Model
{
    use HasFactory;

    protected $table = 'master_intake_times';

    protected $fillable = [
        'code',
        'intake_time_text',
        'latin_abbr',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];
}
