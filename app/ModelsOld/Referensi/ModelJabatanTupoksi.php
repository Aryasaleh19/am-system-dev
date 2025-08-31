<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelJabatanTupoksi extends Model
{
    protected $table      = 'm_pegawai_tupoksi';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['ID_JABATAN', 'URAIAN_TUPOKSI', 'BEBAN', 'OLEH', 'STATUS', 'CREATE_AT'];

    protected $useTimestamps = true;
    protected $createdField  = 'CREATE_AT';
    protected $updatedField  = 'CREATE_AT';
    protected $dateFormat    = 'datetime';
}