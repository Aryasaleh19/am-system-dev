<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelRuangan extends Model
{
    protected $table      = 'm_r_ruangan';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['RUANGAN', 'GEDUNG_ID', 'STATUS'];

    protected $useTimestamps = false;
}