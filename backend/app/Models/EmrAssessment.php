<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmrAssessment extends Model
{
    use HasFactory;

    protected $table = 'emr_assessments';

    protected $fillable = [
        'emr_record_id',
        'tipe_asesmen',
        'form_data',
    ];

    protected $casts = [
        'form_data' => 'array',
    ];

    public function emrRecord()
    {
        return $this->belongsTo(EmrRecord::class, 'emr_record_id');
    }
}
