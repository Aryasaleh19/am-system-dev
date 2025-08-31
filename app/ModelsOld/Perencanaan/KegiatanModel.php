<?php
namespace App\Models\Perencanaan;

use CodeIgniter\Model;

class KegiatanModel extends Model
{
    protected $table = 'perencanaan_kegiatan';
    protected $primaryKey = 'ID_KEGIATAN';
    protected $allowedFields = ['ID_PROGRAM', 'NAMA_KEGIATAN', 'ANGGARAN',  'TAHUN', 'STATUS'];

    // Ambil semua kegiatan dengan nama program
    public function getKegiatanWithProgram($search = null, $order = null, $limit = null, $start = null)
    {
        $builder = $this->select('perencanaan_kegiatan.*, perencanaan_program.NAMA_PROGRAM')
                        ->join('perencanaan_program', 'perencanaan_program.ID_PROGRAM = perencanaan_kegiatan.ID_PROGRAM', 'left');

        if ($search) {
            $builder->groupStart()
                    ->like('perencanaan_kegiatan.NAMA_KEGIATAN', $search)
                    ->orLike('perencanaan_program.NAMA_PROGRAM', $search)
                    ->groupEnd();
        }

        // Urutkan dulu berdasarkan Program lalu Tahun
        $builder->orderBy('perencanaan_program.NAMA_PROGRAM', 'ASC');
        $builder->orderBy('perencanaan_kegiatan.TAHUN', 'ASC');
        $builder->orderBy('perencanaan_kegiatan.ID_KEGIATAN', 'ASC'); // opsional agar urut konsisten

        if ($limit !== null) {
            $builder->limit($limit, $start);
        }

        return $builder->get()->getResultArray();
    }

    public function getByProgram($idProgram)
    {
        return $this->where('ID_PROGRAM', $idProgram)
                    ->orderBy('NAMA_KEGIATAN', 'ASC')
                    ->findAll() ?? [];
    }


    
    public function getKegiatanProgramById($id)
    {
        return $this->select('perencanaan_kegiatan.*, perencanaan_program.NAMA_PROGRAM')
                    ->join('perencanaan_program', 'perencanaan_program.ID_PROGRAM = perencanaan_kegiatan.ID_PROGRAM', 'left')
                    ->where('perencanaan_kegiatan.ID_KEGIATAN', $id)
                    ->get()
                    ->getRowArray(); // <-- single row
    }


    public function countFiltered($search = null)
    {
        $builder = $this->join('perencanaan_program', 'perencanaan_program.ID_PROGRAM = perencanaan_kegiatan.ID_PROGRAM', 'left');
        if ($search) {
            $builder->groupStart()
                    ->like('perencanaan_kegiatan.NAMA_KEGIATAN', $search)
                    ->orLike('perencanaan_program.NAMA_PROGRAM', $search)
                    ->groupEnd();
        }
        return $builder->countAllResults();
    }
}