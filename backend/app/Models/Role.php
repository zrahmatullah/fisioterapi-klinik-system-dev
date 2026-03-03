<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';
    protected $fillable = ['status_aktif', 'role'];

    public function userLogin()
    {
        return $this->hasMany(UserLogin::class);
    }
}
