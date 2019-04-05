<?php

namespace App\Models\Auth\Traits\Method;

/**
 * Trait PermissionMethod.
 */
trait PermissionMethod
{
    /**
     * @return mixed
     */
    public function isAdmin()
    {
        return $this->name === config('access.users.admin_role');
    }
}
