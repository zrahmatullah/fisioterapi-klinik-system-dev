<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSoapTemplate extends Model
{
    use HasFactory;

    protected $table = 'master_soap_templates';

    protected $fillable = [
        'title',
        'discipline',
        'subjective_template',
        'objective_template',
        'assessment_template',
        'planning_template',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];
}
