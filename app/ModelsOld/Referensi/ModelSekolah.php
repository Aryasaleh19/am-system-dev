<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelSekolah extends Model
{
    protected $table      = 'm_sekolah';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['KODE','NAMA_SEKOLAH', 'STATUS'];

    protected $useTimestamps = false;
}