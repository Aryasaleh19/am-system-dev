<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelJabatanPenerimaan extends Model
{
    protected $table      = 'm_pegawai_penerimaan';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['ID_JABATAN', 'JENIS_PENERIMAAN', 'JUMLAH', 'OLEH', 'STATUS', 'CREATE_AT'];

    protected $useTimestamps = true;
    protected $createdField  = 'CREATE_AT';
    protected $updatedField  = 'CREATE_AT';
    protected $dateFormat    = 'datetime';
}