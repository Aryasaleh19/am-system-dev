<?php

namespace App\Models\Pengaturan;

use CodeIgniter\Model;

class ModelModul extends Model
{
    protected $table      = 'm_modul';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['MODUL', 'LINK', 'STATUS'];

    protected $useTimestamps = false;
}