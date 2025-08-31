<?php 
namespace App\Models\Kesiswaan;

use CodeIgniter\Model;

class ModelAngkatan extends Model
{
    protected $table = 'siswa_angkatan';
    protected $primaryKey = 'ID';
    protected $allowedFields = [
        'ANGKATAN', 'STATUS'
    ];


}