<?php

namespace App\Models\Keuangan;

use CodeIgniter\Model;

class ModelPinjaman extends Model
{
    protected $table      = 'keuangan_kk_pinjaman';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['NO_TRANSAKSI', 'TMT', 'ID_PEGAWAI', 'JUMLAH_AKAD', 'SISA', 'TENOR', 'STATUS', 'ID_KAS_PEMBAYAR', 'OLEH', 'KETERANGAN', 'CREATE_AT'];

    protected $useTimestamps = true;
    protected $createdField  = 'CREATE_AT';
    protected $updatedField  = 'CREATE_AT';


    function cekPinjamanByIdPegawai($idPegawai){
        $builder = $this->db->table('keuangan_kk_pinjaman');
        $builder->select('keuangan_kk_pinjaman.*, m_pegawai.NAMA AS NAMA_PEGAWAI, pengguna.NAMA AS NAMA_PENGGUNA');
        $builder->join('m_pegawai', 'm_pegawai.ID = keuangan_kk_pinjaman.ID_PEGAWAI', 'left');
        $builder->join('pengguna', 'pengguna.PEGAWAI_ID = keuangan_kk_pinjaman.OLEH', 'left');
        $builder->where('keuangan_kk_pinjaman.ID_PEGAWAI', $idPegawai);
        $builder->where('keuangan_kk_pinjaman.STATUS', 1); // ada pinjaman (pinjaman dalam keadaan aktif)
        $query = $builder->get()->getResult();
        return $query;
    }
}