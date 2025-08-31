<?php

namespace App\Models\Perencanaan;

use CodeIgniter\Model;

class BelanjaModel extends Model
{
    protected $table            = 'perencanaan_belanja';
    protected $primaryKey       = 'ID_BELANJA';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'ID_SUB',
        'URAIAN_BELANJA',
        'ANGGARAN',
        'REALISASI',
        'TANGGAL',
        'STATUS'
    ];

    public function getBySub($idSub)
    {
        return $this->where('ID_SUB', $idSub)->findAll();
    }

}