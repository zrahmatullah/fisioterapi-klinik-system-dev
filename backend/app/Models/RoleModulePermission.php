<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModulePermission extends Model
{
    use HasFactory;

    protected $table = 'role_module_permissions';

    protected $fillable = [
        'role_id',
        'module_code',
        'module_name',
        'can_view',
        'can_create',
        'can_update',
        'can_delete',
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
