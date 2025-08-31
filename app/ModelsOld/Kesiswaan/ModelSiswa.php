<?php 
namespace App\Models\Kesiswaan;

use CodeIgniter\Model;

class ModelSiswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'NIS';
    protected $allowedFields = [
        'NIS', 'NIS_NEW', 'NAMA', 'TEMPAT_LAHIR', 'TANGGAL_LAHIR', 'JENIS_KELAMIN',
        'AGAMA_ID', 'PROV_ID', 'KAB_ID', 'KEC_ID', 'KEL_ID', 'ALAMAT', 'ANGKATAN_ID', 
         'NAMA_AYAH',  'NAMA_IBU', 'KONTAK_ORANG_TUA', 'CREATED_AT', 'STATUS'
    ];

    protected $createdField = 'CREATED_AT'; // input di kolom CREATE_AT
    protected $updatedField = false; // Jika tidak pakai kolom updated_at

    public function getRiwayatSekolahAktif($nis)
    {
        $builder = $this->db->table('siswa_riwayat_sekolah');
        $builder->select('siswa_riwayat_sekolah.ID, m_sekolah.ID AS ID_SEKOLAH, m_sekolah.NAMA_SEKOLAH AS NAMA_SEKOLAH, pengguna.NAMA AS NAMA_PENGGUNA, siswa_riwayat_sekolah.STATUS');
        $builder->join('m_sekolah', 'siswa_riwayat_sekolah.SEKOLAH_ID = m_sekolah.ID', 'left');
        $builder->join('pengguna', 'siswa_riwayat_sekolah.OLEH = pengguna.PEGAWAI_ID', 'left');
        
        $builder->where('siswa_riwayat_sekolah.NIS', $nis);
        $builder->where('siswa_riwayat_sekolah.STATUS', 1);
        return $builder->get()->getResultArray();
    }
    public function getRiwayatSekolah($nis)
    {
        $builder = $this->db->table('siswa_riwayat_sekolah');
        $builder->select('siswa_riwayat_sekolah.ID, m_sekolah.NAMA_SEKOLAH AS NAMA_SEKOLAH, pengguna.NAMA AS NAMA_PENGGUNA, siswa_riwayat_sekolah.STATUS');
        $builder->join('m_sekolah', 'siswa_riwayat_sekolah.SEKOLAH_ID = m_sekolah.ID', 'left');
        $builder->join('pengguna', 'siswa_riwayat_sekolah.OLEH = pengguna.PEGAWAI_ID', 'left');
        $builder->where('siswa_riwayat_sekolah.NIS', $nis);
        return $builder->get()->getResultArray();
    }
    public function getDataDetailSiswa($nis)
    {
        $builder = $this->db->table('siswa s');
        $builder->select('
            s.NIS AS NIY, 
            s.NIS_NEW AS NISN, 
            s.NAMA AS NAMA_SISWA, 
            sk.NAMA_SEKOLAH AS NAMA_SEKOLAH, 
            p.NAMA AS NAMA_PENGGUNA, 
            s.STATUS
        ');
        $builder->join('siswa_riwayat_sekolah r', 'r.NIS = s.NIS AND r.STATUS = 1', 'left');
        $builder->join('m_sekolah sk', 'sk.ID = r.SEKOLAH_ID', 'left');
        $builder->join('pengguna p', 'r.OLEH = p.PEGAWAI_ID', 'left');
        $builder->where('s.NIS', $nis);
        return $builder->get()->getRowArray(); // <-- ambil 1 baris langsung
    }

    public function getSiswaBySekolah($sekolah_id) // hanya siswa yang memiliki kewajiban membayar berdasarkan sekolah
    {
        $builder = $this->db->table('siswa s');
        $builder->select('s.NIS, s.NAMA');
        $builder->join('m_penerimaan_jenis_maping m', 'm.NIS = s.NIS AND m.STATUS = 1', 'inner'); // Hanya siswa yang ada di mapping
        $builder->join('m_penerimaan_jenis j', 'J.ID = m.ID_JENIS_PENERIMAAN'); // Hanya siswa yang ada di mapping
        $builder->where('j.SEKOLAH_ID', $sekolah_id);
        $builder->groupBy('s.NIS'); // Pastikan unik
        $builder->orderBy('s.NAMA', 'ASC');
        return $builder->get()->getResultArray();
    }

    public function getSiswaByAngkatan($angkatanId)
    {
        $builder = $this->db->table('siswa s');
        $builder->select('s.NIS, s.NAMA');
        $builder->join('siswa_riwayat_sekolah r', 'r.NIS = s.NIS AND r.STATUS = 1', 'left');
        $builder->where('s.ANGKATAN_ID', $angkatanId);
        $builder->orderBy('s.NAMA', 'ASC');
        return $builder->get()->getResultArray();
    }


    

}