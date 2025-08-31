<?php

namespace App\Models\Keuangan;

use CodeIgniter\Model;

class ModelPinjamanSetoran extends Model
{
    protected $table      = 'keuangan_km_pinjaman';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['NO_TRANSAKSI', 'ID_PINJAMAN', 'ID_KAS_PENERIMA', 'TANGGAL', 'TENOR_KE', 'BULAN', 'TAHUN', 'JUMLAH_SETOR', 'OLEH', 'STATUS', 'CREATE_AT'];

    protected $useTimestamps = true;
    protected $createdField  = 'CREATE_AT';
    protected $updatedField  = 'CREATE_AT';

    function cekSetoranByPinjaman($idpinjaman){
        $builder = $this->db->table('keuangan_km_pinjaman');
        $builder->select('keuangan_km_pinjaman.*, pengguna.NAMA AS NAMA_PENGGUNA, keuangan_rekening.NAMA_BANK');
        $builder->join('keuangan_kk_pinjaman', 'keuangan_kk_pinjaman.ID = keuangan_km_pinjaman.ID_PINJAMAN', 'left');
        $builder->join('m_pegawai', 'm_pegawai.ID = keuangan_kk_pinjaman.ID_PEGAWAI', 'left');
        $builder->join('pengguna', 'pengguna.PEGAWAI_ID = keuangan_km_pinjaman.OLEH', 'left');
        $builder->join('keuangan_rekening', 'keuangan_rekening.ID = keuangan_km_pinjaman.ID_KAS_PENERIMA', 'left');
        $builder->where('keuangan_km_pinjaman.ID_PINJAMAN', $idpinjaman);
        $builder->where('keuangan_kk_pinjaman.STATUS', 1); // ada pinjaman (pinjaman dalam keadaan aktif)
        $query = $builder->get()->getResult();
        return $query;
    }
}