<?php 
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\Referensi\ModelAgama;
use App\Models\Referensi\ModelSekolah;
use App\Models\Referensi\ModelRuangan;
use App\Models\Referensi\ModelJenisPenerimaan;
use App\Models\Referensi\ModelJabatan;
use App\Models\Kesiswaan\ModelAngkatan;
use App\Models\Kesiswaan\ModelSiswa;
use App\Models\Pengaturan\ProfilModel;
use App\Models\Kepegawaian\ModelPengguna;
use App\Models\Kepegawaian\ModelPegawai;
use App\Models\Keuangan\ModelRekeningBank;

class Referensi extends BaseController
{
    public function agama()
    {
        $model = new ModelAgama();
        return $this->response->setJSON($model->orderBy('ID')->findAll());
    }

    public function angkatan()
    {
        $model = new ModelAngkatan();
        $rows = $model->where('STATUS', 1)->orderBy('ID')->findAll();
        return $this->response->setJSON($rows);
    }

    public function sekolah()
    {
        $model = new ModelSekolah();
        $rows = $model->where('STATUS', 1)->orderBy('ID')->findAll();
        return $this->response->setJSON($rows);
    
    }
    public function ruangan()
    {
        $model = new ModelRuangan();
        $rows = $model->where('STATUS', 1)->orderBy('ID')->findAll();
        return $this->response->setJSON($rows);
    }

    public function datasiswa()
    {
        $model = new ModelSiswa();
        $rows = $model->orderBy('NIS')->findAll();
        return $this->response->setJSON($rows);
    }
    
    public function petugas()
    {
        $model = new ModelPengguna();
        $rows = $model->orderBy('PEGAWAI_ID')->findAll();
        return $this->response->setJSON($rows);
    }

    public function pegawai()
    {
        $term = $this->request->getGet('term'); // ambil parameter search dari Select2
        $model = new ModelPegawai();

        if($term) {
            // filter nama pegawai sesuai term
            $rows = $model->like('NAMA', $term)->orderBy('NAMA')->findAll();
        } else {
            $rows = $model->orderBy('NAMA')->findAll();
        }

        return $this->response->setJSON($rows);
    }


    public function bank()
    {
        $search = $this->request->getVar('term');
        $model = new ModelRekeningBank();

        if ($search) {
            $rows = $model
                ->like('NAMA_BANK', $search)
                ->orLike('NO_REKENING', $search)
                ->orderBy('NAMA_BANK')
                ->findAll();
        } else {
            $rows = $model->orderBy('NAMA_BANK')->findAll(20);
        }

        return $this->response->setJSON($rows); // pastikan SALDO_AKHIR termasuk di allowedFields
    }


    
    public function jenispenerimaan_pembayaran()
    {
        $model = new ModelJenisPenerimaan();
        $rows = $model->orderBy('JENIS_PENERIMAAN')->findAll();
        return $this->response->setJSON($rows);
    }

    public function koplaporan()
    {
        $model = new ProfilModel();
        $rows = $model->where('STATUS', 1)->orderBy('ID')->findAll();
        return $this->response->setJSON($rows);
    }

    public function getSiswaBySekolah()
    {
        $sekolah_id = $this->request->getGet('sekolah_id');
        $modelSiswa = new ModelSiswa();
        $data = $modelSiswa->getSiswaBySekolah($sekolah_id);
        return $this->response->setJSON($data);
    }
    

    public function getJenisPenerimaanBySekolah()
    {
        $sekolah_id = $this->request->getGet('sekolah_id');
        $modelJenis = new ModelJenisPenerimaan();
        $data = $modelJenis->getDataJenisPenerimaanBySekolah($sekolah_id);
        return $this->response->setJSON($data);
    
    }
    public function getSiswaByAngkatan()
    {
        $angkatanId = $this->request->getGet('angkatanId');
        $modelSiswa = new ModelSiswa();
        $data = $modelSiswa->getSiswaByAngkatan($angkatanId);
        return $this->response->setJSON($data);
    }

    public function program()
    {
        $term = $this->request->getGet('term') ?? '';

        $programModel = new \App\Models\Perencanaan\ProgramModel();

        // Cari program sesuai term, limit 20
        $programs = $programModel->like('NAMA_PROGRAM', $term)
                                ->orderBy('NAMA_PROGRAM', 'ASC')
                                ->findAll(20);

        $results = [];
        foreach ($programs as $program) {
            $results[] = [
                'id' => $program['ID_PROGRAM'],
                'text' => $program['NAMA_PROGRAM'] . ' (' . $program['TAHUN'] . ')'
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }
    public function kegiatan()
    {
        $term = $this->request->getGet('term') ?? '';

        $kegiatanModel = new \App\Models\Perencanaan\KegiatanModel();

        // Cari program sesuai term, limit 20
        $kegiatans = $kegiatanModel->like('NAMA_KEGIATAN', $term)
                                ->orderBy('NAMA_KEGIATAN', 'ASC')
                                ->findAll(20);

        $results = [];
        foreach ($kegiatans as $kegiatan) {
            $results[] = [
                'id' => $kegiatan['ID_KEGIATAN'],
                'text' => $kegiatan['NAMA_KEGIATAN']
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }
    public function subkegiatan()
    {
        $term = $this->request->getGet('term') ?? '';

        $subkegiatanModel = new \App\Models\Perencanaan\SubKegiatanModel();

        // Cari program sesuai term, limit 20
        $subkegiatans = $subkegiatanModel->like('NAMA_SUB_KEGIATAN', $term)
                                ->orderBy('NAMA_SUB_KEGIATAN', 'ASC')
                                ->findAll(20);

        $results = [];
        foreach ($subkegiatans as $subkegiatan) {
            $results[] = [
                'id' => $subkegiatan['ID_KEGIATAN'],
                'text' => $subkegiatan['NAMA_SUB_KEGIATAN']
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }
    public function belanja()
    {
        $term = $this->request->getGet('term') ?? '';

        $belanjaModel = new \App\Models\Perencanaan\BelanjaModel();

        // Cari program sesuai term, limit 20
        $belanjas = $belanjaModel->like('URAIAN_BELANJA', $term)
                                ->orderBy('URAIAN_BELANJA', 'ASC')
                                ->findAll(20);

        $results = [];
        foreach ($belanjas as $belanja) {
            $results[] = [
                'id' => $belanja['ID_BELANJA'],
                'text' => $belanja['URAIAN_BELANJA']
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }


}

?>