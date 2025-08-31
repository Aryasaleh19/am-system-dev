<?php 
namespace App\Models\Kesiswaan;

use CodeIgniter\Model;

class ModelSiswaSekolah extends Model
{
    protected $table = 'siswa_riwayat_sekolah';
    protected $primaryKey = 'ID';
    protected $allowedFields = [
        'NIS_NEW', 'ANGKATAN_NEW', 'NIS', 'SEKOLAH_ID', 'OLEH', 'TANGGAL', 'STATUS'
    ];

    public function getRiwayatSekolahByNIS($nis)
    {
        return $this->select('siswa_riwayat_sekolah.*, m_sekolah.NAMA_SEKOLAH')
                    ->join('m_sekolah', 'm_sekolah.ID = siswa_riwayat_sekolah.SEKOLAH_ID', 'left')
                    ->where('NIS', $nis)
                    ->findAll();
    }

}