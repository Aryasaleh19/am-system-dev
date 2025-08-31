<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelJabatanAbsensi extends Model
{
    protected $table      = 'absensi_pengaturan';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['ID_JABATAN', 'HARI', 'MULAI', 'DATANG', 'PULANG', 'STATUS','OLEH'];

    protected $useTimestamps = false;
}