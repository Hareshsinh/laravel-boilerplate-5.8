<?php

namespace App\Repositories\Backend\Auth;

use App\Events\Backend\Auth\Permission\PermissionUpdated;
use App\Repositories\BaseRepository;
use DB;
use Spatie\Permission\Models\Permission;

/**
 * Class PermissionRepository.
 */
class PermissionRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Permission::class;
    }

    /**
     * @param array $data
     *
     * @throws GeneralException
     * @throws \Throwable
     * @return Role
     */
    public function create(array $data) : Permission
    {
        // Make sure it doesn't already exist
        if ($this->permissionExists($data['name'])) {
            throw new GeneralException('A permission already exists with the name '.e($data['name']));
        }

        if (! isset($data['permissions']) || ! \count($data['permissions'])) {
            $data['permissions'] = [];
        }


        return DB::transaction(function () use ($data) {
            $permission = parent::create(['name' => strtolower($data['name']),'guard_name' => 'web']);
            if ($permission) {
                return $permission;
            }
            throw new GeneralException(trans('exceptions.backend.access.permissions.create_error'));
        });
    }


    /**
     * @param Role $permission
     * @param array $data
     * @return mixed
     * @throws \Throwable
     */
    public function update($permission, array $data)
    {
        if ($permission->isAdmin()) {
            throw new GeneralException('You can not edit the administrator role.');
        }

        // If the name is changing make sure it doesn't already exist
        if ($permission->name !== strtolower($data['name'])) {
            if ($this->permissionExists($data['name'])) {
                throw new GeneralException('A Permission already exists with the name '.$data['name']);
            }
        }

        if (! isset($data['permissions']) || ! \count($data['permissions'])) {
            $data['permissions'] = [];
        }



        return DB::transaction(function () use ($permission, $data) {
            if ($permission->update([
                'name' => strtolower($data['name']),
            ])) {

                event(new PermissionUpdated($permission));

                return $permission;
            }

            throw new GeneralException(trans('exceptions.backend.access.permissions.update_error'));
        });
    }

    /**
     * @param $name
     *
     * @return bool
     */
    protected function permissionExists($name) : bool
    {
        return $this->model
                ->where('name', strtolower($name))
                ->count() > 0;
    }
}
