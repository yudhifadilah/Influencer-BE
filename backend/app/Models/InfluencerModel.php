<?php

namespace App\Models;

use CodeIgniter\Model;

class InfluencerModel extends Model
{
    protected $table = 'influencers';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'email', 'password', 'full_name', 'birth_date', 'gender',
        'influencer_category', 'phone_number', 'referral_code', 
        'ktp_number', 'npwp_number', 'instagram_link', 'followers_count', 
        'profile_picture', 'bank_account', 'account_number', 
        'province', 'city', 'registration_date'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'registration_date';

    public function getInfluencerByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
}
