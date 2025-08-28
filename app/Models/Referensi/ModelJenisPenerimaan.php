<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelJenisPenerimaan extends Model
{
    protected $table      = 'm_penerimaan_jenis';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['JENIS_PENERIMAAN', 'JUMLAH', 'KATEGORI', 'TENOR', 'SATUAN', 'SEKOLAH_ID', 'STATUS'];

    protected $useTimestamps = false;

    // join dengan tabel m_sekolah
    public function getDataJenisPenerimaan()
    {
        return $this->db->table('m_penerimaan_jenis')
            ->select('m_penerimaan_jenis.ID, m_penerimaan_jenis.JENIS_PENERIMAAN, m_penerimaan_jenis.JUMLAH, m_penerimaan_jenis.KATEGORI, m_penerimaan_jenis.TENOR, m_penerimaan_jenis.SATUAN, m_penerimaan_jenis.STATUS, m_penerimaan_jenis.SEKOLAH_ID, m_sekolah.NAMA_SEKOLAH')
            ->join('m_sekolah', 'm_sekolah.ID = m_penerimaan_jenis.SEKOLAH_ID', 'left');
    }
    public function getDataJenisPenerimaanYayasan()
    {
        // khusus untuk jenis penerimaan yayasan
        return $this->db->table('m_penerimaan_jenis')
            ->select('m_penerimaan_jenis.ID, m_penerimaan_jenis.JENIS_PENERIMAAN, m_penerimaan_jenis.JUMLAH, m_penerimaan_jenis.KATEGORI, m_penerimaan_jenis.TENOR, m_penerimaan_jenis.SATUAN, m_penerimaan_jenis.STATUS, m_penerimaan_jenis.SEKOLAH_ID, m_sekolah.NAMA_SEKOLAH')
            ->join('m_sekolah', 'm_sekolah.ID = m_penerimaan_jenis.SEKOLAH_ID', 'left')
            ->where('m_penerimaan_jenis.SEKOLAH_ID', 5);
    }

    public function getDataJenisPenerimaanBySekolah($sekolah_id = null)
    {
        $builder = $this->db->table('m_penerimaan_jenis')
            ->select('m_penerimaan_jenis.ID, m_penerimaan_jenis.JENIS_PENERIMAAN, m_penerimaan_jenis.JUMLAH, m_penerimaan_jenis.KATEGORI, m_penerimaan_jenis.TENOR, m_penerimaan_jenis.SATUAN, m_penerimaan_jenis.STATUS, m_penerimaan_jenis.SEKOLAH_ID, m_sekolah.NAMA_SEKOLAH')
            ->join('m_sekolah', 'm_sekolah.ID = m_penerimaan_jenis.SEKOLAH_ID', 'left');

        if($sekolah_id) {
            $builder->where('m_penerimaan_jenis.SEKOLAH_ID', $sekolah_id);
        }

        return $builder->get()->getResultArray();
    }



}