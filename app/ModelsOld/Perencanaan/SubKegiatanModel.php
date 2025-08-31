<?php

namespace App\Models\Perencanaan;

use CodeIgniter\Model;

class SubKegiatanModel extends Model
{
    protected $table            = 'perencanaan_sub_kegiatan';
    protected $primaryKey       = 'ID_SUB';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'ID_KEGIATAN',
        'NAMA_SUB_KEGIATAN',
        'ANGGARAN',
        'STATUS'
    ];

    public function getByKegiatan($idKegiatan)
    {
        return $this->where('ID_KEGIATAN', $idKegiatan)
                    ->orderBy('NAMA_SUB_KEGIATAN', 'ASC')
                    ->findAll() ?? [];
    }
}