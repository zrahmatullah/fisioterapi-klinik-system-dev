<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmrGoalTracking extends Model
{
    use HasFactory;

    protected $table = 'emr_goal_trackings';

    protected $fillable = [
        'profile_anak_id',
        'emr_record_id',
        'tipe_goal',
        'judul_target',
        'indikator_keberhasilan',
        'baseline_persen',
        'capaian_persen',
        'tanggal_target',
        'status',
    ];

    protected $casts = [
        'baseline_persen' => 'integer',
        'capaian_persen' => 'integer',
        'tanggal_target' => 'date',
    ];

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }
}
