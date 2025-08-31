<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelJenispegawai extends Model
{
    protected $table      = 'm_pegawai_jenis';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['JENIS_PEGAWAI', 'STATUS'];

    protected $useTimestamps = false;
}