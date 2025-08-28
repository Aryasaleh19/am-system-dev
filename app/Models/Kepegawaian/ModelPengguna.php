<?php 
namespace App\Models\Kepegawaian;

use CodeIgniter\Model;

class ModelPengguna extends Model
{
    protected $table = 'pengguna';
    protected $primaryKey = 'PEGAWAI_ID';
    protected $allowedFields = [
        'PEGAWAI_ID', 'NIK', 'NAMA', 'USERNAME', 'PASSWORD',
        'ACTIVE', 'CREATED_AT'
    ];

    protected $createdField = 'CREATED_AT'; // input di kolom CREATE_AT
    protected $updatedField = false; // Jika tidak pakai kolom updated_at


}