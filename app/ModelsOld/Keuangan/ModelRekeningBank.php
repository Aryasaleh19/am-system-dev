<?php

namespace App\Models\Keuangan;

use CodeIgniter\Model;

class ModelRekeningBank extends Model
{
    protected $table      = 'keuangan_rekening';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['NO_REKENING', 'NAMA_BANK', 'SALDO_AWAL', 'SALDO_AKHIR', 'STATUS'];

    protected $useTimestamps = false;
}