<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelPendidikan extends Model
{
    protected $table      = 'm_pendidikan';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['PENDIDIKAN', 'STATUS'];

    protected $useTimestamps = false;
}