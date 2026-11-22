<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalReferenceRange extends Model
{
    use HasFactory;

    protected $table = 'vital_reference_ranges';

    protected $fillable = [
        'vital_parameter_id',
        'age_bracket',
        'min_age_months',
        'max_age_months',
        'min_value',
        'max_value',
        'critical_low',
        'critical_high',
        'notes',
    ];

    protected $casts = [
        'min_age_months' => 'integer',
        'max_age_months' => 'integer',
        'min_value' => 'float',
        'max_value' => 'float',
        'critical_low' => 'float',
        'critical_high' => 'float',
    ];

    public function parameter()
    {
        return $this->belongsTo(MasterVitalParameter::class, 'vital_parameter_id');
    }
}
