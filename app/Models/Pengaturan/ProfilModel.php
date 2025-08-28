<?php

namespace App\Models\Pengaturan;

use CodeIgniter\Model;

class ProfilModel  extends Model
{
   protected $table = 'pengaturan_profil';
    protected $primaryKey = 'ID';
    protected $allowedFields = [
        'NAMA_LENGKAP', 'NAMA_SINGKAT', 'ALAMAT', 'LOGO', 'TELP', 'FAX', 'EMAIL'
    ];

    public function get($id = null)
    {
        return $this->where(['ID' => $id])->first();

    }
}