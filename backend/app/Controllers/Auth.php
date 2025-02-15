<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Controller;
use CodeIgniter\Session\Session;


class Auth extends Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }
    
    public function login()
    {
        $session = session();
        $userModel = new UserModel();
        $json = $this->request->getJSON();

        $user = $userModel->where('username', $json->username)->first();

        if (!$user || !password_verify($json->password, $user['password'])) {
            return $this->response->setJSON(['message' => 'Username atau password salah'])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // Simpan session user
        $session->set([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'is_logged_in' => true
        ]);

        return $this->response->setJSON(['message' => 'Login berhasil']);
    }

    public function logout()
    {
        $session = session();
        $session->destroy();

        return $this->response->setJSON(['message' => 'Logout berhasil']);
    }

    public function profile()
    {
        $session = session();
        if (!$session->get('is_logged_in')) {
            return $this->response->setJSON(['message' => 'Anda belum login'])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        return $this->response->setJSON([
            'user_id' => $session->get('user_id'),
            'username' => $session->get('username')
        ]);
    }

    public function register()
    {
    $userModel = new UserModel();
    $json = $this->request->getJSON();

    $userExists = $userModel->where('username', $json->username)->first();
    if ($userExists) {
        return $this->response->setJSON(['message' => 'Username sudah digunakan'])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    $data = [
        'username' => $json->username,
        'password' => password_hash($json->password, PASSWORD_BCRYPT),
    ];

    $userModel->insert($data);
    return $this->response->setJSON(['message' => 'Registrasi berhasil']);
    }

}
