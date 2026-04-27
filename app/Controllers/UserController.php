<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        return view('admin/man_users');
    }

    public function getData()
    {
        $data = $this->userModel
            ->select('users.*, roles.nama as role_nama')
            ->join('roles', 'roles.id = users.role_id AND roles.is_deleted = 0', 'left')
            ->where('users.is_deleted', 0)
            ->findAll();

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        $userId = session()->get('user_id');

        $username = trim($this->request->getPost('username'));
        $nama     = trim($this->request->getPost('nama'));
        $email    = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');
        $roleId   = $this->request->getPost('role_id');
        $status = $this->request->getPost('status') ? 1 : 0;

        if (!$username || !$nama || !$email || !$roleId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Semua field wajib diisi'
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Format email tidak valid'
            ]);
        }

        // Validasi input
        $existUsername = $this->userModel
            ->where('LOWER(username)', strtolower($username))
            ->where('id !=', $id ?? 0)
            ->first();

        if ($existUsername) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Username sudah digunakan'
            ]);
        }

        $existEmail = $this->userModel
            ->where('LOWER(email)', strtolower($email))
            ->where('id !=', $id ?? 0)
            ->first();

        if ($existEmail) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email sudah digunakan'
            ]);
        }

        $role = $this->roleModel
            ->where('id', $roleId)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->first();

        if (!$role) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Role tidak valid atau sudah nonaktif'
            ]);
        }

        $data = [
            'username'  => $username,
            'nama'      => $nama,
            'email'     => $email,
            'role_id'   => $roleId,
            'status' => $status,
        ];

        if (!$id) {
            // Hak akses tambah
            if (!can('user_create')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak punya akses tambah'
                ]);
            }

            if (!$password) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Password wajib diisi'
                ]);
            }

            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            $data['created_by'] = $userId;

            $this->userModel->insert($data);

        } else {
            // Hak akses edit
            if (!can('user_edit')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak punya akses edit'
                ]);
            }

            if ($password) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $data['last_edited_by'] = $userId;
            $this->userModel->update($id, $data);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data user berhasil disimpan'
        ]);
    }

    // Soft delete
    public function delete($id)
    {
        $this->userModel->update($id, [
            'is_deleted' => 1,
            'status' => 0
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'User berhasil dihapus'
        ]);
    }
}