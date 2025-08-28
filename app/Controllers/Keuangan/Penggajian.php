<?php 
namespace App\Controllers\Keuangan;


use App\Controllers\BaseController;
use App\Models\Keuangan\ModelRekeningBank;
use App\Models\Keuangan\ModelKasKeluar;
use App\Models\Referensi\ModelJenisPenerimaan;
use App\Models\Keuangan\ModelKasMasuk;
use App\Models\Referensi\ModelSekolah;
use App\Models\Referensi\ModelJenisPengeluaran;
use App\Models\Kepegawaian\ModelPegawai;

class Penggajian extends BaseController
{
    protected $modelRekeningBank;
    protected $modeJenisPenerimaan;
    protected $modeKasMasuk;
    protected $modelSekolah;
    protected $modelJenisPengeluaran;
    protected $modelKasKeluar;
    protected $modelPegawai;

    public function __construct()
    {
        $this->modelRekeningBank = new ModelRekeningBank();
        $this->ModelJenisPenerimaan = new ModelJenisPenerimaan();
        $this->modeKasMasuk = new ModelKasMasuk();
        $this->modelSekolah = new ModelSekolah();
        $this->modelJenisPengeluaran = new ModelJenisPengeluaran();
        $this->modelKasKeluar = new ModelKasKeluar();
        $this->modelPegawai = new ModelPegawai();

    }

    public function getJabatanByIdPegawai()
    {
        $id = $this->request->getVar('id');
        $data = $this->modelPegawai->getJabatanByIdPegawai($id);
        echo json_encode($data);
    }


    public function getPenerimaanByIdPegawai()
    {
        $id = $this->request->getVar('id');
        $data = $this->modelPegawai->getPenerimaanByIdPegawai($id);
        echo json_encode($data);
    }


}