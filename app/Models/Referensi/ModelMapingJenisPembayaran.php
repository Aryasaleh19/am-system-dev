<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelMapingJenisPembayaran extends Model
{
    protected $table      = 'm_penerimaan_jenis_maping';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['NIS', 'ID_JENIS_PENERIMAAN',  'TENOR', 'JUMLAH', 'TELAH_DIBAYAR', 'SISA_DIBAYAR', 'LUNAS', 'STATUS', 'OLEH'];

    protected $useTimestamps = true;
    protected $createdField  = 'CREATED_AT';
    protected $updatedField  = 'CREATED_AT';

    /**
     * Ambil semua mapping berdasarkan NIS
     */
    public function getMappingByNis($nis)
    {
        return $this->where('NIS', $nis)->findAll();
    }

    /**
     * Cek apakah jenis pembayaran sudah dimapping oleh siswa
     */
    public function isMapped($nis, $jenisId)
    {
        return $this->where('NIS', $nis)
                    ->where('ID_JENIS_PENERIMAAN', $jenisId)
                    ->first() !== null;
    }

    public function getMappingWithJenis($nis)
    {
        return $this->select('m_penerimaan_jenis_maping.*, j.JENIS_PENERIMAAN, j.TENOR AS TENOR_MASTER, j.JUMLAH AS JUMLAH_MASTER, m_sekolah.NAMA_SEKOLAH')
        ->join('m_penerimaan_jenis j', 'j.ID = m_penerimaan_jenis_maping.ID_JENIS_PENERIMAAN', 'left')
        ->join('m_sekolah', 'm_sekolah.ID = j.SEKOLAH_ID', 'left')
        ->where('m_penerimaan_jenis_maping.NIS', $nis)
        ->findAll();
    }


    public function getMappingWithJenisAktif($nis)
    {
        return $this->select('m_penerimaan_jenis_maping.*, j.JENIS_PENERIMAAN, j.TENOR AS TENOR_MASTER, j.JUMLAH AS JUMLAH_MASTER')
                    ->join('m_penerimaan_jenis j', 'j.ID = m_penerimaan_jenis_maping.ID_JENIS_PENERIMAAN', 'left')
                    ->where('m_penerimaan_jenis_maping.NIS', $nis)
                    ->where('m_penerimaan_jenis_maping.STATUS', '1')
                    ->findAll();
    }
    
    public function getAllJenisAktifByAngkatan($angkatanId)
    {
        return $this->select('j.ID, j.JENIS_PENERIMAAN, j.JUMLAH')
            ->join('m_penerimaan_jenis j', 'j.ID = m_penerimaan_jenis_maping.ID_JENIS_PENERIMAAN', 'left')
            ->join('siswa s', 's.NIS = m_penerimaan_jenis_maping.NIS', 'left')
            ->where('s.ANGKATAN_ID', $angkatanId)
            ->where('j.KATEGORI', 'Siswa')
            ->where('j.STATUS', '1') // hanya jenis pembayaran yang aktif
            ->where('m_penerimaan_jenis_maping.STATUS', '1') // hanya mapping yang aktif
            ->groupBy('j.ID, j.JENIS_PENERIMAAN')
            ->findAll();
    }


    public function createMapping($data)
    {
        return $this->insert($data);
    }

    /**
     * Hapus mapping berdasarkan ID
     */
    public function deleteMapping($id)
    {
        return $this->delete($id);
    }
}