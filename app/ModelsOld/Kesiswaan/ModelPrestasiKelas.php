<?php 
namespace App\Models\Kesiswaan;

use CodeIgniter\Model;

class ModelPrestasiKelas extends Model
{
    protected $table = 'siswa_prestasi_kelas';
    protected $primaryKey = 'ID';
    protected $allowedFields = [
        'NIS', 'ID_RIWAYAT_SEKOLAH', 'RUANGAN_ID', 'KELAS',
        'TMT', 'OLEH', 'STATUS'
    ];

    public function getRiwayatByNIS($nis)
    {
        return $this->select('
                siswa_prestasi_kelas.ID,
                siswa_prestasi_kelas.KELAS,
                siswa_prestasi_kelas.TMT,
                siswa_prestasi_kelas.STATUS,
                r.RUANGAN,
                s.NAMA_SEKOLAH,
                siswa_prestasi_kelas.STATUS
            ')
            ->join('m_r_ruangan r', 'r.ID = siswa_prestasi_kelas.RUANGAN_ID', 'left')
            ->join('siswa_riwayat_sekolah ri', 'ri.ID = siswa_prestasi_kelas.ID_RIWAYAT_SEKOLAH', 'left')
            ->join('m_sekolah s', 's.ID = ri.SEKOLAH_ID', 'left')
            ->where('siswa_prestasi_kelas.NIS', $nis)
            ->orderBy('siswa_prestasi_kelas.TMT', 'DESC')
            ->findAll();
    }
    public function getRiwayatById($id)
    {
        return $this->select('
                siswa_prestasi_kelas.*,
                r.RUANGAN,
                s.NAMA_SEKOLAH
            ')
            ->join('m_r_ruangan r', 'r.ID = siswa_prestasi_kelas.RUANGAN_ID', 'left')
            ->join('siswa_riwayat_sekolah ri', 'ri.ID = siswa_prestasi_kelas.ID_RIWAYAT_SEKOLAH', 'left')
            ->join('m_sekolah s', 's.ID = ri.SEKOLAH_ID', 'left')
            ->where('siswa_prestasi_kelas.ID', $id)
            ->first();
    }


}