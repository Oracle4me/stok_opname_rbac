<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\UserModel;

class RoleController extends BaseController
{
    protected $roleModel;
    protected $userModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('admin/man_roles');
    }

    public function getRoleSelect()
    {
        $data = $this->roleModel
            ->where('is_deleted', 0)
            ->where('is_active', 1)
            ->findAll();

        $result = [];

        foreach ($data as $row) {
            $result[] = [
                'id'   => $row['id'],
                'text' => $row['nama']
            ];
        }

        return $this->response->setJSON([
            'results' => $result
        ]);
    }

    public function getData()
    {
        $data = $this->roleModel
            ->where('is_deleted', 0)
            ->findAll();
        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function save()
    {

        $id = $this->request->getPost('id');
        $userId = session()->get('user_id');

        $nama = trim($this->request->getPost('nama') ?? '');
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if ($nama === '') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nama role wajib diisi!'
            ]);
        }

        // Check ID 
        if ($id && !$this->roleModel->find($id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        // Cek apakah duplikat untuk name
        $exist = $this->roleModel
            ->where('LOWER(nama) =', strtolower($nama))
            ->where('id !=', $id ?? 0)
            ->first();

        if ($exist) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nama role sudah digunakan'
            ]);
        }

        $data = [
            'nama' => $nama,
            'is_active' => $isActive,
        ];

        if ($id) {
            // Hak akses edit
            if (!can('role_edit')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak punya akses edit'
                ]);
            }
            
            $data['last_edited_by'] = $userId;
            $this->roleModel->update($id, $data);
        } else {
            // Hak akses tambah
            if (!can('role_create')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak punya akses tambah'
                ]);
            }

            $data['created_by'] = $userId;
            $this->roleModel->insert($data);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data berhasil disimpan'
        ]);
    }

    // Soft delete
    public function delete($id)
    {
        $userCount = $this->userModel
            ->where('role_id', $id)
            ->where('is_deleted', 0)
            ->countAllResults();

        if ($userCount > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Role sedang digunakan, tidak dapat dihapus'
            ]);
        }

        $this->roleModel->update($id, [
            'is_deleted' => 1,
            'is_active' => 0
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Role berhasil dihapus'
        ]);
    }
}
