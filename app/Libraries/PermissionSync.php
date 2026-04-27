<?php

namespace App\Libraries;

use App\Models\PermissionModel;
use App\Models\RolePermissionModel;

class PermissionSync
{
    protected $permissionModel;
    protected $rolePermissionModel;

    public function __construct()
    {
        $this->permissionModel = new PermissionModel();
        $this->rolePermissionModel = new RolePermissionModel();
    }

    public function run()
    {
        $config = require APPPATH . 'Config/Permissions.php';

        $dbPermissions = $this->permissionModel->findAll();

        $mapDb = [];
        foreach ($dbPermissions as $p) {
            $mapDb[$p['nama']] = $p;
        }

        foreach ($config as $group => $permissions) {
            foreach ($permissions as $perm) {

                if (!isset($perm['nama']) || !isset($perm['label'])) {
                    continue;
                }

                if (!isset($mapDb[$perm['nama']])) {

                    $permId = $this->permissionModel->insert([
                        'nama'       => $perm['nama'],
                        'label'      => $perm['label'],
                        'group_name' => $group
                    ], true);

                    // Auto ADMIN punya hak akses awal
                    $this->rolePermissionModel->insert([
                        'role_id'       => 1,
                        'permission_id' => $permId,
                        'value'         => 1
                    ]);

                } else {
                    $existing = $mapDb[$perm['nama']];

                    if (
                        $existing['label'] !== $perm['label'] ||
                        $existing['group_name'] !== $group
                    ) {
                        $this->permissionModel->update($existing['id'], [
                            'label'      => $perm['label'],
                            'group_name' => $group
                        ]);
                    }
                }
            }
        }
    }
}