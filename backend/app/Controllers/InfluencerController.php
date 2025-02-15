<?php

namespace App\Controllers;

use App\Models\InfluencerModel;
use CodeIgniter\RESTful\ResourceController;

class InfluencerController extends ResourceController
{
    protected $modelName = 'App\Models\InfluencerModel';
    protected $format    = 'json';

    public function register()
    {
        helper(['form']);

        $rules = [
            'email'               => 'required|valid_email|is_unique[influencers.email]',
            'password'            => 'required|min_length[6]',
            'full_name'           => 'required',
            'birth_date'          => 'required|valid_date',
            'gender'              => 'required',
            'influencer_category' => 'required',
            'phone_number'        => 'required|numeric',
            'ktp_number'          => 'required|numeric',
            'npwp_number'         => 'required|numeric',
            'instagram_link'      => 'required|valid_url',
            'followers_count'     => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Proses upload gambar
        $profilePicture = $this->request->getFile('profile_picture');
        if ($profilePicture && $profilePicture->isValid() && !$profilePicture->hasMoved()) {
            $newName = $profilePicture->getRandomName();
            $profilePicture->move('uploads/', $newName);
            $data['profile_picture'] = base_url('uploads/' . $newName);
        } else {
            $data['profile_picture'] = null; // Jika tidak upload gambar, kosongkan
        }

        // Simpan ke database
        $this->model->insert($data);

        return $this->respondCreated([
            'message' => 'Influencer registered successfully',
            'profile_picture' => $data['profile_picture']
        ]);
    }
    

    public function index()
    {
        $model = new InfluencerModel();
        return $this->respond($model->findAll());
    }

    public function show($id = null)
    {
        $model = new InfluencerModel();
        $data = $model->find($id);

        if (!$data) {
            return $this->failNotFound('Influencer not found');
        }

        return $this->respond($data);
    }

    public function update($id = null)
    {
        $model = new InfluencerModel();
        $data = $this->request->getRawInput();

        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if ($model->update($id, $data)) {
            return $this->respondUpdated(['message' => 'Influencer updated successfully']);
        } else {
            return $this->fail('Update failed');
        }
    }

    public function delete($id = null)
    {
        $model = new InfluencerModel();
        if ($model->delete($id)) {
            return $this->respondDeleted(['message' => 'Influencer deleted successfully']);
        } else {
            return $this->failNotFound('Influencer not found');
        }
    }

}
