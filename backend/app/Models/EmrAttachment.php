<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmrAttachment extends Model
{
    use HasFactory;

    protected $table = 'emr_attachments';

    protected $fillable = [
        'emr_record_id',
        'profile_anak_id',
        'nama_file',
        'file_path',
        'file_type',
        'file_size',
        'kategori',
        'keterangan',
    ];

    public function emrRecord()
    {
        return $this->belongsTo(EmrRecord::class, 'emr_record_id');
    }

    public function profileAnak()
    {
        return $this->belongsTo(ProfileAnak::class, 'profile_anak_id');
    }
}
