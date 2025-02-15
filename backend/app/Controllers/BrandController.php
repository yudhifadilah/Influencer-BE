<?php

namespace App\Controllers;

use App\Models\BrandModel;
use CodeIgniter\RESTful\ResourceController;

class BrandController extends ResourceController
{
    protected $modelName = 'App\Models\BrandModel';
    protected $format    = 'json';

    public function register()
    {
        helper(['form']);

        $rules = [
            'email'       => 'required|valid_email|is_unique[brands.email]',
            'password'    => 'required|min_length[6]',
            'brand_name'  => 'required',
            'pic_name'    => 'required',
            'pic_phone'   => 'required|numeric',
            'province'    => 'required',
            'city'        => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Upload Brand Logo
        $brandLogo = $this->request->getFile('brand_logo');
        if ($brandLogo && $brandLogo->isValid() && !$brandLogo->hasMoved()) {
            $newName = $brandLogo->getRandomName();
            $brandLogo->move('uploads/brands/', $newName);
            $data['brand_logo'] = base_url('uploads/brands/' . $newName);
        } else {
            $data['brand_logo'] = null;
        }

        $this->model->insert($data);

        return $this->respondCreated([
            'message' => 'Brand registered successfully',
            'brand_logo' => $data['brand_logo']
        ]);
    }

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->failNotFound('Brand not found');
        }
        return $this->respond($data);
    }

    public function update($id = null)
    {
        $data = $this->request->getRawInput();
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Upload Brand Logo (Jika ada file baru)
        $brandLogo = $this->request->getFile('brand_logo');
        if ($brandLogo && $brandLogo->isValid() && !$brandLogo->hasMoved()) {
            $newName = $brandLogo->getRandomName();
            $brandLogo->move('uploads/brands/', $newName);
            $data['brand_logo'] = base_url('uploads/brands/' . $newName);
        }

        if ($this->model->update($id, $data)) {
            return $this->respondUpdated(['message' => 'Brand updated successfully']);
        } else {
            return $this->fail('Update failed');
        }
    }

    public function delete($id = null)
    {
        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'Brand deleted successfully']);
        } else {
            return $this->failNotFound('Brand not found');
        }
    }
}
