<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelProfesi extends Model
{
    protected $table      = 'm_pegawai_profesi';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['PROFESI', 'STATUS'];

    protected $useTimestamps = false;
}