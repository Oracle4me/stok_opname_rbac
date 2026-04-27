<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\Exceptions\PageNotFoundException;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $permissions = session()->get('permissions') ?? [];

        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $requiredPermission = $arguments[0] ?? null;

        if ($requiredPermission && !in_array($requiredPermission, $permissions)) {

            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak dipakai
    }
}