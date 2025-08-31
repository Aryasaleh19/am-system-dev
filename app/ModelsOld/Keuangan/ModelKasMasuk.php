<?php

namespace App\Models\Keuangan;

use CodeIgniter\Model;

class ModelKasMasuk extends Model
{
    protected $table      = 'keuangan_kas_masuk';
    protected $primaryKey = 'ID';

    protected $allowedFields = [
        'ID', 'TANGGAL', 'ID_JENIS_PENERIMAAN', 'ID_KAS_BANK_TERIMA', 
        'DITERIMA_DARI', 'JUMLAH', 'BUKTI', 'OLEH', 'STATUS', 'CREATE_AT', 'UPDATE_AT'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CREATE_AT';
    protected $updatedField  = 'UPDATE_AT';

    public function getRiwayatKasmasuk($start = 0, $length = 10, $search = null, $orderColumn = 'TANGGAL', $orderDir = 'desc')
    {
        $builder = $this->db->table($this->table)
            ->select('keuangan_kas_masuk.*, 
                      keuangan_rekening.NO_REKENING, keuangan_rekening.NAMA_BANK, 
                      m_penerimaan_jenis.JENIS_PENERIMAAN, pengguna.NAMA as OLEH')
            ->join('keuangan_rekening', 'keuangan_rekening.ID = keuangan_kas_masuk.ID_KAS_BANK_TERIMA', 'left')
            ->join('m_penerimaan_jenis', 'm_penerimaan_jenis.ID = keuangan_kas_masuk.ID_JENIS_PENERIMAAN', 'left')
            ->join('pengguna', 'pengguna.PEGAWAI_ID = keuangan_kas_masuk.OLEH', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('keuangan_kas_masuk.ID', $search)
                ->orLike('keuangan_kas_masuk.DITERIMA_DARI', $search)
                ->orLike('m_penerimaan_jenis.JENIS_PENERIMAAN', $search)
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