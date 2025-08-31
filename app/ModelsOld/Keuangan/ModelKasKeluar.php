<?php

namespace App\Models\Keuangan;

use CodeIgniter\Model;

class ModelKasKeluar extends Model
{
    protected $table      = 'keuangan_kas_keluar';
    protected $primaryKey = 'ID';

    protected $allowedFields = [
        'ID', 'TANGGAL', 'ID_JENIS_PENGELUARAN', 'ID_KAS_BANK_PEMBAYAR', 
        'PENERIMA', 'JUMLAH', 'BUKTI', 'OLEH', 'STATUS',  'KETERANGAN', 'CREATE_AT', 'UPDATE_AT'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CREATE_AT';
    protected $updatedField  = 'UPDATE_AT';

    public function getRiwayatKasKeluar($start = 0, $length = 10, $search = null, $orderColumn = 'TANGGAL', $orderDir = 'desc')
    {
        $builder = $this->db->table($this->table)
            ->select('keuangan_kas_keluar.*, 
                      keuangan_rekening.NO_REKENING, keuangan_rekening.NAMA_BANK, 
                      m_pengeluaran_jenis.JENIS_PENGELUARAN, pengguna.NAMA as OLEH')
            ->join('keuangan_rekening', 'keuangan_rekening.ID = keuangan_kas_keluar.ID_KAS_BANK_PEMBAYAR', 'left')
            ->join('m_pengeluaran_jenis', 'm_pengeluaran_jenis.ID = keuangan_kas_keluar.ID_JENIS_PENGELUARAN', 'left')
            ->join('pengguna', 'pengguna.PEGAWAI_ID = keuangan_kas_keluar.OLEH', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('keuangan_kas_keluar.ID', $search)
                ->orLike('keuangan_kas_keluar.PENERIMA', $search)
                ->orLike('m_pengeluaran_jenis.JENIS_PENGELUARAN', $search)
                ->orLike('keuangan_rekening.NO_REKENING', $search)
                ->orLike('keuangan_rekening.NAMA_BANK', $search)
                ->orLike('pengguna.NAMA', $search)
                ->groupEnd();
        }

        $totalFiltered = $builder->countAllResults(false);

        $builder->orderBy($orderColumn, $orderDir)
                ->limit($length, $start);

        $query = $builder->get();
        $data = $query->getResultArray();

        return [
            'data' => $data,
            'recordsFiltered' => $totalFiltered
        ];
    }
}