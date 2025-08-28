<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelJenisPengeluaran extends Model
{
    protected $table      = 'm_pengeluaran_jenis';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['KODE', 'JENIS_PENGELUARAN', 'STATUS'];

    protected $useTimestamps = false;
}