<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelWilKecamatan extends Model
{
    protected $table      = 'm_wil_kec';
    protected $primaryKey = 'KDKEC';
    protected $allowedFields = ['NMKEC'];
    protected $useTimestamps = false;
}