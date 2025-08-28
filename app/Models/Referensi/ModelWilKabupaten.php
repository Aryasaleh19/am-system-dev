<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelWilKabupaten extends Model
{
    protected $table      = 'm_wil_kab';
    protected $primaryKey = 'KDKAB';

    protected $allowedFields = ['NMKAB', 'KDKABBPJS'];

    protected $useTimestamps = false;
}