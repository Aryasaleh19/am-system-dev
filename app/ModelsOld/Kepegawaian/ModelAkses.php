<?php
namespace App\Models\Kepegawaian;

use CodeIgniter\Model;

class ModelAkses extends Model
{
    protected $table = 'pengguna_akses';
    protected $allowedFields = ['ID', 'PEGAWAI_ID', 'JABATAN_ID', 'MODUL_ID', 'RUANGAN_ID'];
}