<?php 
namespace App\Controllers\Kepegawaian;

use App\Controllers\BaseController;
use App\Models\Kepegawaian\ModelPegawai;
use App\Models\Kepegawaian\ModelPengguna;
use App\Models\Referensi\ModelJabatan;
use App\Models\Referensi\ModelJenispegawai;
use App\Models\Referensi\ModelProfesi;
use App\Models\Referensi\ModelAgama;
use App\Models\Referensi\ModelPendidikan;

class Absensi extends BaseController
{
    protected $pegawaiModel;
    protected $jabatanModel;
    protected $jenisPegawaiModel;
    protected $profesiModel;
    protected $agamaModel;
    protected $pendidikanModel;
    protected $penggunaModel;

    public function __construct()
    {
        $this->pegawaiModel = new ModelPegawai();
        $this->jabatanModel = new ModelJabatan();
        $this->jenisPegawaiModel = new ModelJenispegawai();
        $this->profesiModel = new ModelProfesi();
        $this->agamaModel = new ModelAgama();
        $this->pendidikanModel = new ModelPendidikan();
        $this->penggunaModel = new ModelPengguna();
    }

    public function index()
    {
        return view('kepegawaian/absensi/index', [
            'title' => '📲 Absensi'
        ]);
    }

}