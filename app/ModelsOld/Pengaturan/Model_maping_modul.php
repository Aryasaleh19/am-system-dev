<?php

namespace App\Models\Pengaturan;

use CodeIgniter\Model;

class Model_maping_modul extends Model
{
    protected $table      = 'm_modul_maping';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['GROUP_ID', 'MODUL_ID', 'STATUS'];

    protected $useTimestamps = false;
}