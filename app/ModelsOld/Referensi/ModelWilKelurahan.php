<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelWilKelurahan extends Model
{
    protected $table      = 'm_wil_kel';
    protected $primaryKey = 'KDKEL';

    protected $allowedFields = ['NMKEL'];

    protected $useTimestamps = false;
}