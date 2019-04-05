<?php

namespace App\Models\Auth;

use App\Models\Auth\Traits\Attribute\PermissionAttribute;
use App\Models\Auth\Traits\Method\PermissionMethod;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use PermissionAttribute,PermissionMethod;
   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','guard_name'];

    public function permissionRoles() {
        return $this->belongsToMany('App\Models\Auth\Role', 'role_has_permissions', 'permission_id', 'role_id');
    }
}
