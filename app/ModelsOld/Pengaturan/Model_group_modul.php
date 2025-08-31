<?php

namespace App\Models\Pengaturan;

use CodeIgniter\Model;

class Model_group_modul extends Model
{
    protected $table      = 'm_modul_group';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['GROUP_MODUL', 'STATUS'];

    protected $useTimestamps = false;
}