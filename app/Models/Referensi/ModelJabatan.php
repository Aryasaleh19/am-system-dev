<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelJabatan extends Model
{
    protected $table      = 'm_pegawai_jabatan';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['JABATAN', 'STATUS'];

    protected $useTimestamps = false;
}