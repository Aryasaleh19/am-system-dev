<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelWilProvinsi extends Model
{
    protected $table      = 'm_wil_prov';
    protected $primaryKey = 'KDPROV';

    protected $allowedFields = ['NMPROV'];

    protected $useTimestamps = false;
}