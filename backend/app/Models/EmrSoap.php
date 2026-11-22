<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmrSoap extends Model
{
    use HasFactory;

    protected $table = 'emr_soaps';

    protected $fillable = [
        'emr_record_id',
        'subjektif',
        'objektif',
        'asesmen',
        'planning',
        'target_sesi',
        'aktivitas_terapi',
        'respons_anak',
        'rekomendasi_home_program',
    ];

    public function emrRecord()
    {
        return $this->belongsTo(EmrRecord::class, 'emr_record_id');
    }
}
