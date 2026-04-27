<?php

namespace App\Controllers;

use App\Models\PermissionModel;
use App\Models\RolePermissionModel;

class PermissionController extends BaseController
{
    protected $permissionModel;
    protected $rolePermissionModel;

    public function __construct()
    {
        $this->permissionModel = new PermissionModel();
        $this->rolePermissionModel = new RolePermissionModel();
    }

    public function index()
    {
        return view('admin/man_hak_akses');
    }

    public function get($role_id)
    {
        $permissions = $this->permissionModel
            ->orderBy('group_name', 'ASC')
            ->findAll();

        $grouped = [];

        foreach ($permissions as $perm) {

            $group = $perm['group_name'];

            $existing = $this->rolePermissionModel
                ->where('role_id', $role_id)
                ->where('permission_id', $perm['id'])
                ->first();

            $grouped[$group][] = [
                'permission_id' => $perm['id'],
                'name'  => $perm['nama'],
                'label' => $perm['label'],
                'value' => $existing ? $existing['value'] : 0
            ];
        }

        $data = [];

        foreach ($grouped as $group => $items) {
            $data[] = [
                'group' => $group,
                'permissions' => $items
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function update()
    {
        $role_id = $this->request->getPost('role_id');
        $permission_id = $this->request->getPost('permission_id');
        $value = $this->request->getPost('value');
        $userId = session()->get('user_id');

        $existing = $this->rolePermissionModel
            ->where('role_id', $role_id)
            ->where('permission_id', $permission_id)
            ->first();

        if ($existing) {
            $this->rolePermissionModel->update($existing['id'], [
                'value' => $value,
                'last_edited_by' => $userId
            ]);
        } else {
            $this->rolePermissionModel->insert([
                'role_id' => $role_id,
                'permission_id' => $permission_id,
                'value' => $value,
                'created_by' => $userId
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }
}