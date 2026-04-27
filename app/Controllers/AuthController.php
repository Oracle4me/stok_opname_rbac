<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{

    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    public function login()
    {
        return view('auth/login');
    }

    public function loginProcess()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (!$email || !$password) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email dan password wajib diisi'
            ]);
        }

        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email tidak ditemukan'
            ]);
        }

        if ($user['status'] == 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akun anda dinonaktifkan, hubungi admin'
            ]);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Password salah'
            ]);
        }

        $permissions = $this->db->table('role_permissions rp')
                        ->select('p.nama')
                        ->join('permissions p', 'p.id = rp.permission_id')
                        ->where('rp.role_id', $user['role_id'])
                        ->where('rp.value', 1)
                        ->get()
                        ->getResultArray();

        $permissionList = array_column($permissions, 'nama');
        session()->set([
            'user_id' => $user['id'],
            'nama' => $user['nama'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'permissions' => $permissionList,
            'logged_in' => true
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Login berhasil'
        ]);
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Berhasil logout');
    }
}
