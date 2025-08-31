<?php

namespace App\Models\Perencanaan;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table            = 'perencanaan_program';
    protected $primaryKey       = 'ID_PROGRAM';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'NAMA_PROGRAM',
        'TAHUN',
        'ANGGARAN'
    ];

    public function getByTahun($tahun = null)
    {
        $builder = $this->orderBy('NAMA_PROGRAM', 'ASC');
        if($tahun) {
            $builder->where('TAHUN', $tahun);
        }
        return $builder->findAll(); // selalu array
    }
}