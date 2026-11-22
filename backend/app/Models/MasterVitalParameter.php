<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterVitalParameter extends Model
{
    use HasFactory;

    protected $table = 'master_vital_parameters';

    protected $fillable = [
        'code',
        'name',
        'unit',
        'is_core_ttv',
        'is_pediatric_growth',
        'sort_order',
        'status_aktif',
    ];

    protected $casts = [
        'is_core_ttv' => 'boolean',
        'is_pediatric_growth' => 'boolean',
        'sort_order' => 'integer',
        'status_aktif' => 'boolean',
    ];

    public function referenceRanges()
    {
        return $this->hasMany(VitalReferenceRange::class, 'vital_parameter_id')->orderBy('min_age_months', 'asc');
    }
}
