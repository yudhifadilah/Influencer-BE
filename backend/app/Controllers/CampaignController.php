<?php

namespace App\Controllers;

use App\Models\CampaignModel;
use CodeIgniter\RESTful\ResourceController;

class CampaignController extends ResourceController
{
    protected $modelName = 'App\Models\CampaignModel';
    protected $format    = 'json';

    // Tambahkan Campaign
    public function create()
    {
        helper(['form']);

        $rules = [
            'name'          => 'required',
            'category'      => 'required',
            'influencer_id' => 'required|integer',
            'start_date'    => 'required|valid_date',
            'end_date'      => 'required|valid_date',
            'pdf_file'      => 'uploaded[pdf_file]|max_size[pdf_file,2048]|ext_in[pdf_file,pdf]',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $pdf = $this->request->getFile('pdf_file');
        $newName = $pdf->getRandomName();
        $pdf->move('uploads/pdf/', $newName);
        $pdfUrl = base_url('uploads/pdf/' . $newName);

        $data = [
            'name'          => $this->request->getPost('name'),
            'category'      => $this->request->getPost('category'),
            'influencer_id' => $this->request->getPost('influencer_id'),
            'start_date'    => $this->request->getPost('start_date'),
            'end_date'      => $this->request->getPost('end_date'),
            'pdf_file'      => $pdfUrl,
            'status'        => 'pending'
        ];

        $this->model->insert($data);

        return $this->respondCreated(['message' => 'Campaign created successfully']);
    }

    // Tampilkan semua campaign milik influencer yang login
    public function index()
    {
        $influencerId = $this->request->getHeaderLine('Influencer-ID'); // Ambil ID dari header
        if (!$influencerId) {
            return $this->failUnauthorized('Unauthorized: Influencer ID is required');
        }

        $campaigns = $this->model->where('influencer_id', $influencerId)->findAll();

        return $this->respond($campaigns);
    }

    // Tampilkan satu campaign berdasarkan ID, hanya jika milik influencer yang login
    public function show($id = null)
    {
        $influencerId = $this->request->getHeaderLine('Influencer-ID');
        if (!$influencerId) {
            return $this->failUnauthorized('Unauthorized: Influencer ID is required');
        }

        $campaign = $this->model->where('id', $id)->where('influencer_id', $influencerId)->first();

        if (!$campaign) {
            return $this->failNotFound('Campaign not found or not authorized');
        }

        return $this->respond($campaign);
    }

    
    public function getByInfluencer($influencer_id = null)
    {
    if (!$influencer_id) {
        return $this->fail('Influencer ID is required');
    }

    $campaigns = $this->model->where('influencer_id', $influencer_id)->findAll();

    if (empty($campaigns)) {
        return $this->failNotFound('No campaigns found for this influencer');
    }

    return $this->respond($campaigns);
    }

    // Update status campaign (hanya influencer yang bersangkutan yang bisa mengubahnya)
    public function updateStatus($id = null)
    {
        $influencerId = $this->request->getHeaderLine('Influencer-ID'); // Pastikan ID influencer dikirim dalam header
        if (!$influencerId) {
            return $this->failUnauthorized('Unauthorized: Influencer ID is required');
        }

        $campaign = $this->model->find($id);

        if (!$campaign) {
            return $this->failNotFound('Campaign not found');
        }

        // Pastikan hanya influencer yang berhak bisa mengubah status
        if ($campaign['influencer_id'] != $influencerId) {
            return $this->failForbidden('Access denied: You are not allowed to update this campaign');
        }

        $status = $this->request->getPost('status');

        if (!in_array($status, ['accepted', 'rejected'])) {
            return $this->fail('Invalid status');
        }

        $this->model->update($id, ['status' => $status]);

        // Notifikasi ke admin (simulasi, bisa pakai email atau sistem notifikasi lain)
        $notifMessage = ($status == 'accepted') ? 
            "Campaign '{$campaign['name']}' telah diterima oleh influencer." :
            "Campaign '{$campaign['name']}' telah ditolak oleh influencer.";

        return $this->respond(['message' => $notifMessage]);
    }

    // Hapus campaign (opsional, hanya admin atau pemilik campaign yang bisa menghapus)
    public function delete($id = null)
    {
        $influencerId = $this->request->getHeaderLine('Influencer-ID');
        if (!$influencerId) {
            return $this->failUnauthorized('Unauthorized: Influencer ID is required');
        }

        $campaign = $this->model->where('id', $id)->where('influencer_id', $influencerId)->first();

        if (!$campaign) {
            return $this->failNotFound('Campaign not found or unauthorized');
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'Campaign deleted successfully']);
        }

        return $this->fail('Failed to delete campaign');
    }
}
