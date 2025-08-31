<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelAgama extends Model
{
    protected $table      = 'm_agama';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['AGAMA', 'STATUS'];

    protected $useTimestamps = false;
}