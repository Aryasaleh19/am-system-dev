<?php 
namespace App\Models\Kepegawaian;

use CodeIgniter\Model;

class ModelPegawai extends Model
{
    protected $table = 'm_pegawai';
    protected $primaryKey = 'ID';
    protected $allowedFields = [
        'NIP', 'NIK', 'NAMA', 'TEMPAT_LAHIR', 'TANGGAL_LAHIR',
        'JENIS_KELAMIN', 'AGAMA_ID', 'PENDIDIKAN_ID', 'JABATAN_ID',
        'PROFESI_ID', 'TMT_SK', 'TM_SK', 'JENIS_PEGAWAI_ID', 'AKTIF'
    ];

    protected $useTimestamps = false; // Kalau kamu tidak pakai created_at dan updated_at

    public function getWithAkun($pegawaiId)
    {
        return $this->select('m_pegawai.*, pengguna.USERNAME, pengguna.ACTIVE')
            ->join('pengguna', 'pengguna.PEGAWAI_ID = m_pegawai.ID', 'left')
            ->where('m_pegawai.ID', $pegawaiId)
            ->first();
    }

    // mengambil jabatan pegawai
    public function getJabatanByIdPegawai($pegawaiId)
    {
        return $this->select('m_pegawai_jabatan.JABATAN AS NAMA_JABATAN')
            ->join('m_pegawai_jabatan', 'm_pegawai_jabatan.ID = m_pegawai.JABATAN_ID AND m_pegawai_jabatan.STATUS = 1', 'left')
            ->where('m_pegawai.ID', $pegawaiId)
            ->first();
    }
    public function getPenerimaanByIdPegawai($pegawaiId)
    {
        return $this->db->table('m_pegawai')
            ->select('m_pegawai_jabatan.JABATAN AS NAMA_JABATAN')
            ->select('m_pegawai_penerimaan.ID,
                    m_pegawai_penerimaan.JENIS_PENERIMAAN,
                    m_pegawai_penerimaan.JUMLAH AS JUMLAH_MASTER,
                    m_pegawai_penerimaan.JUMLAH')
            ->join('m_pegawai_jabatan', 'm_pegawai_jabatan.ID = m_pegawai.JABATAN_ID AND m_pegawai_jabatan.STATUS = 1', 'left')
            ->join('m_pegawai_penerimaan', 'm_pegawai_jabatan.ID = m_pegawai_penerimaan.ID_JABATAN AND m_pegawai_penerimaan.STATUS = 1', 'left')
            ->where('m_pegawai.ID', $pegawaiId)
            ->get()
            ->getResultArray();
    }

}