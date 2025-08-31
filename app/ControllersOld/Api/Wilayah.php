<?php 
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\Referensi\ModelWilProvinsi;
use App\Models\Referensi\ModelWilKabupaten;
use App\Models\Referensi\ModelWilKecamatan;
use App\Models\Referensi\ModelWilKelurahan;

class Wilayah extends BaseController
{
    public function provinsi()
    {
        $model = new ModelWilProvinsi();
        return $this->response->setJSON($model->findAll());
    }

    public function kabupaten($kdprov)
    {
        $model = new ModelWilKabupaten();
        $data = $model->where('KDKAB LIKE', "$kdprov%")->findAll();
        return $this->response->setJSON($data);
    }

    public function kecamatan($kdkab)
    {
        $model = new ModelWilKecamatan();
        $data = $model->where('KDKEC LIKE', "$kdkab%")->findAll();
        return $this->response->setJSON($data);
    }

    public function kelurahan($kdkec)
    {
        $model = new ModelWilKelurahan();
        $data = $model->where('KDKEL LIKE', "$kdkec%")->findAll();
        return $this->response->setJSON($data);
    }
}

?>