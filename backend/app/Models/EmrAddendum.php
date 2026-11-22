<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmrAddendum extends Model
{
    use HasFactory;

    protected $table = 'emr_addendums';

    protected $fillable = [
        'emr_record_id',
        'user_id',
        'alasan_koreksi',
        'catatan_addendum',
        'data_sebelumnya',
    ];

    protected $casts = [
        'data_sebelumnya' => 'array',
    ];

    public function emrRecord()
    {
        return $this->belongsTo(EmrRecord::class, 'emr_record_id');
    }

    public function user()
    {
        return $this->belongsTo(UserLogin::class, 'user_id');
    }
}
