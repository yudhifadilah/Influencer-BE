<?php

namespace App\Models;

use CodeIgniter\Model;

class CampaignModel extends Model
{
    protected $table = 'campaigns';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'category',
        'influencer_id',
        'start_date',
        'end_date',
        'pdf_file',
        'status',
    ];
}
