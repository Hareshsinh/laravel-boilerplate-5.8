<?php

namespace App\Http\Controllers\Backend\Auth\Permission;

use App\Models\Auth\Permission;
use App\Http\Controllers\Controller;
use App\Events\Backend\Auth\Permission\PermissionDeleted;
use App\Repositories\Backend\Auth\PermissionRepository;
use App\Http\Requests\Backend\Auth\Permission\StorePermissionRequest;
use App\Http\Requests\Backend\Auth\Permission\ManagePermissionRequest;
use App\Http\Requests\Backend\Auth\Permission\UpdatePermissionRequest;

/**
 * Class PermissionController.
 */
class PermissionController extends Controller
{
    /**
     * @var permissionRepository
     */
    protected $permissionRepository;

    /**
     * @param permissionRepository $permissionRepository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param ManagePermissionRequest $request
     *
     * @return mixed
     */
    public function index(ManagePermissionRequest $request, Permission $permission)
    {
        $permissionData = $permission
            ->orderBy('id')
            ->paginate();
        return view('backend.auth.permission.index')
            ->withPermissions($permissionData);
    }

    /**
     * @param ManagePermissionRequest $request
     * @return mixed
     */
    public function create(ManagePermissionRequest $request)
    {
        return view('backend.auth.permission.create')
            ->withPermissions($this->permissionRepository->get());
    }


    /**
     * @param StorePermissionRequest $request
     * @return mixed
     * @throws \Throwable
     */
    public function store(StorePermissionRequest $request)
    {
        $this->permissionRepository->create($request->only('name'));

        return redirect()->route('admin.auth.permission.index')->withFlashSuccess(__('alerts.backend.permissions.created'));
    }

    /**
     * @param ManagePermissionRequest $request
     * @param Permission $permission
     *
     * @return mixed
     */
    public function edit(ManagePermissionRequest $request, Permission $permission)
    {

        if ($permission->isAdmin()) {
            return redirect()->route('admin.auth.permission.index')->withFlashDanger('You can not edit the administrator Permission.');
        }

        return view('backend.auth.permission.edit')
            ->withPermission($permission);
    }


    /**
     * @param UpdatePermissionRequest $request
     * @param Permission $permission
     * @return mixed
     * @throws \Throwable
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $this->permissionRepository->update($permission, $request->only('name'));

        return redirect()->route('admin.auth.permission.index')->withFlashSuccess(__('alerts.backend.permissions.updated'));
    }

    /**
     * @param ManagePermissionRequest $request
     * @param Permission   $permission
     *
     * @throws \Exception
     * @return mixed
     */
    public function destroy(ManagePermissionRequest $request, Permission $permission)
    {
        if ($permission->isAdmin()) {
            return redirect()->route('admin.auth.permission.index')->withFlashDanger(__('exceptions.backend.access.permissions.cant_delete_admin'));
        }

        $relationships = $permission->permissionRoles->count();

        if (empty($relationships)) {
            $permission->delete();

            event(new PermissionDeleted($permission));

            return redirect()->route('admin.auth.permission.index')->withFlashSuccess(__('alerts.backend.permissions.deleted'));

        } else {

            return redirect()->route('admin.auth.permission.index')->withFlashWarning(__('exceptions.backend.access.permissions.cant_delete_permission'));
        }
    }
}
