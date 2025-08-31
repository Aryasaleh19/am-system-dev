<?php

namespace App\Models\Keuangan;

use CodeIgniter\Model;

class ModelPembayaranSiswa extends Model
{
    protected $table      = 'keuangan_pembayaran_siswa';
    protected $primaryKey = 'ID';

    protected $allowedFields = [
        'ID', 
        'NIS', 
        'ID_JENIS_PENERIMAAN', 
        'ID_MAPING_JENIS_PENERIMAAN', 
        'JUMLAH', 
        'TANGGAL', 
        'BULAN_TAGIHAN', 
        'TAHUN_TAGIHAN', 
        'ID_REKENING_BANK', 
        'CATATAN',
        'BUKTI',
        'OLEH'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CREATED_AT';
    protected $updatedField  = 'UPDATED_AT';


    public function getRiwayatPembayaranDenganMapping($nis, $id_jenis = null, $idMapPenerimaan = null)
    {
        return $this->db->table('keuangan_pembayaran_siswa rp')  // pakai tabel pembayaran siswa
            ->select('rp.*, rp.ID AS ID_PEMBAYARAN, jp.JENIS_PENERIMAAN, jp.TENOR, mp.JUMLAH as JUMLAH_MASTER, rb.NAMA_BANK as NAMA_BANK, rb.NO_REKENING, pengguna.NAMA as NAMA_PENGGUNA')
            ->join('m_penerimaan_jenis_maping mp', 'mp.ID = rp.ID_MAPING_JENIS_PENERIMAAN', 'inner')
            ->join('m_penerimaan_jenis jp', 'jp.ID = mp.ID_JENIS_PENERIMAAN', 'inner')
            ->join('keuangan_rekening rb', 'rb.ID = rp.ID_REKENING_BANK', 'left')
            ->join('pengguna pengguna', 'pengguna.PEGAWAI_ID = rp.OLEH', 'left')
            ->where('rp.NIS', $nis)
            ->where('rp.ID_JENIS_PENERIMAAN', $id_jenis)
            ->where('rp.ID_MAPING_JENIS_PENERIMAAN', $idMapPenerimaan)
            ->where('mp.STATUS', 1) // mapping aktif
            ->orderBy('rp.TANGGAL', 'DESC')
            ->get()
            ->getResultArray();
    }
    public function getRiwayatPembayaranDenganMappingInfo($nis = null)
    {
        return $this->db->table('keuangan_pembayaran_siswa rp')
            ->select('rp.*, rp.ID AS ID_PEMBAYARAN, jp.JENIS_PENERIMAAN, jp.TENOR, mp.JUMLAH as JUMLAH_MASTER, rb.NAMA_BANK, rb.NO_REKENING, pengguna.NAMA as NAMA_PENGGUNA, mp.TELAH_DIBAYAR, mp.SISA_DIBAYAR, jp.ID AS ID_JENIS_PENERIMAAN, s.NAMA as NAMA_SISWA, s.NIS_NEW as NISN')
            ->join('m_penerimaan_jenis_maping mp', 'mp.NIS = rp.NIS AND mp.ID_JENIS_PENERIMAAN = rp.ID_JENIS_PENERIMAAN', 'inner')
            ->join('m_penerimaan_jenis jp', 'jp.ID = rp.ID_JENIS_PENERIMAAN', 'inner')
            ->join('keuangan_rekening rb', 'rb.ID = rp.ID_REKENING_BANK', 'left')
            ->join('pengguna pengguna', 'pengguna.PEGAWAI_ID = rp.OLEH', 'left')
            ->join('siswa s', 's.NIS = rp.NIS', 'left')
            ->where('rp.NIS', $nis)
            ->where('mp.STATUS', 1)
            ->orderBy('rp.TANGGAL', 'DESC')
            ->get()
            ->getResultArray();

    }



}