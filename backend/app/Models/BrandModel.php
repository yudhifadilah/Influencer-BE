<?php

namespace App\Models;

use CodeIgniter\Model;

class BrandModel extends Model
{
    protected $table = 'brands';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'email', 'password', 'brand_name', 'pic_name', 'pic_phone',
        'province', 'city', 'referral_code', 'brand_logo'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    public function getBrandByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
}
