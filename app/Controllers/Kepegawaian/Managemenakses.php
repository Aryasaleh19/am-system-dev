<?php 
namespace App\Controllers\Kepegawaian;

use App\Controllers\BaseController;
use App\Models\Kepegawaian\ModelPegawai;
use App\Models\Kepegawaian\ModelPengguna;
use App\Models\Kepegawaian\ModelAkses;
use App\Models\Referensi\ModelJabatan;
use App\Models\Referensi\ModelJenispegawai;
use App\Models\Referensi\ModelProfesi;
use App\Models\Referensi\ModelAgama;
use App\Models\Referensi\ModelPendidikan;
use App\Models\Referensi\ModelGedung;
use App\Models\Referensi\ModelRuangan;
use App\Models\Pengaturan\ModelModul;
use App\Models\Pengaturan\Model_group_modul;
use App\Models\Pengaturan\Model_maping_modul;

class Managemenakses extends BaseController
{
    protected $pegawaiModel;
    protected $jabatanModel;
    protected $jenisPegawaiModel;
    protected $profesiModel;
    protected $agamaModel;
    protected $pendidikanModel;
    protected $penggunaModel;
    protected $aksesModel;
    protected $gedungModel;
    protected $ruanganModel;
    protected $modulModel;
    protected $groupmodelModel;
    protected $mapingModel;

    public function __construct()
    {
        $this->pegawaiModel      = new ModelPegawai();
        $this->jabatanModel      = new ModelJabatan();
        $this->jenisPegawaiModel = new ModelJenispegawai();
        $this->profesiModel      = new ModelProfesi();
        $this->agamaModel        = new ModelAgama();
        $this->pendidikanModel   = new ModelPendidikan();
        $this->penggunaModel     = new ModelPengguna();
        $this->aksesModel        = new ModelAkses();
        $this->gedungModel       = new ModelGedung(); 
        $this->mapingModel       = new Model_maping_modul(); 
        $this->modulModel        = new ModelModul();
        $this->groupmodelModel   = new Model_group_modul();
        $this->ruanganModel   = new ModelRuangan();
    }

    public function index()
    {
        return view('kepegawaian/managemenakses/index', [
            'title' => '🛡️ Managemen Akses Pengguna'
        ]);
    }

    public function ajaxList()
    {
        $request = \Config\Services::request();
        $searchValue = $request->getGet('search')['value'] ?? '';
        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;

        $builder = $this->pegawaiModel
            ->select('
                m_pegawai.ID,
                m_pegawai.NIP,
                m_pegawai.NIK,
                m_pegawai.NAMA,
                m_pegawai.JENIS_KELAMIN,
                m_pegawai.AKTIF,
                m_pegawai_jenis.JENIS_PEGAWAI,
                m_pegawai_jabatan.JABATAN,
                m_pegawai_profesi.PROFESI,
                m_pegawai.TMT_SK,
                m_pegawai.TM_SK,
                m_agama.AGAMA
            ')
            ->join('m_pegawai_jenis', 'm_pegawai_jenis.ID = m_pegawai.JENIS_PEGAWAI_ID', 'left')
            ->join('m_pegawai_jabatan', 'm_pegawai_jabatan.ID = m_pegawai.JABATAN_ID', 'left')
            ->join('m_pegawai_profesi', 'm_pegawai_profesi.ID = m_pegawai.PROFESI_ID', 'left')
            ->join('m_agama', 'm_agama.ID = m_pegawai.AGAMA_ID', 'left');

        if ($searchValue) {
            $builder->groupStart()
                ->like('m_pegawai.NAMA', $searchValue)
                ->orLike('m_pegawai.NIP', $searchValue)
                ->orLike('m_pegawai.NIK', $searchValue)
                ->groupEnd();
        }

        $totalFiltered = $builder->countAllResults(false);
        $builder->limit($length, $start);
        $data = $builder->get()->getResult();

        $rows = [];
        foreach ($data as $d) {
            $rows[] = [
                'ID' => $d->ID,
                'NIP' => $d->NIP,
                'NIK' => $d->NIK,
                'NAMA' => $d->NAMA,
                'JENIS_KELAMIN' => $d->JENIS_KELAMIN,
                'JENIS_PEGAWAI' => $d->JENIS_PEGAWAI,
                'JABATAN' => $d->JABATAN,
                'PROFESI' => $d->PROFESI,
                'TMT_SK' => $d->TMT_SK,
                'TM_SK' => $d->TM_SK,
                'AGAMA' => $d->AGAMA,
                'AKTIF' => $d->AKTIF
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($request->getGet('draw')),
            'recordsTotal' => $this->pegawaiModel->countAll(),
            'recordsFiltered' => $totalFiltered,
            'data' => $rows
        ]);
    }

    public function get($id)
    {
        $data = $this->pegawaiModel->find($id);
        return $this->response->setJSON($data ?? []);
    }

    public function get_akun($id)
    {
        // Ambil akses
        $rows = $this->aksesModel->where('PEGAWAI_ID', $id)->findAll();

        $jabatan = [];
        $modul   = [];
        $ruangan = [];

        foreach ($rows as $row) {
            if (!empty($row['JABATAN_ID'])) $jabatan[] = (int)$row['JABATAN_ID'];
            if (!empty($row['MODUL_ID']))   $modul[]   = (int)$row['MODUL_ID'];
            if (!empty($row['RUANGAN_ID'])) $ruangan[] = (int)$row['RUANGAN_ID'];
        }

        // Ambil data pegawai/akun
        $pegawai = $this->penggunaModel->find($id);

        return $this->response->setJSON([
            'status'      => true,
            'PEGAWAI_ID'  => $pegawai['PEGAWAI_ID'] ?? null,
            'USERNAME'    => $pegawai['USERNAME'] ?? '',
            'NIK'         => $pegawai['NIK'] ?? '',
            'NAMA'         => $pegawai['NAMA'] ?? '',
            'jabatan'     => $jabatan,
            'modul'       => $modul,
            'ruangan'     => $ruangan
        ]);
    }



    public function formakun($pegawaiId = null)
    {
        $jabatan = $this->jabatanModel->where('STATUS', 1)->orderBy('JABATAN', 'ASC')->findAll();
        $groups = $this->groupmodelModel->where('STATUS', 1)->orderBy('GROUP_MODUL', 'ASC')->findAll();
        $mapping = $this->mapingModel->where('STATUS', 1)->orderBy('GROUP_ID', 'ASC')->findAll();
        $moduls = $this->modulModel->where('STATUS', 1)->findAll();

        $modulIndex = [];
        foreach ($moduls as $mod) {
            $modulIndex[$mod['ID']] = $mod;
        }

        $modulByGroup = [];
        foreach ($groups as $group) {
            $modulByGroup[$group['ID']] = [
                'GROUP_MODUL' => $group['GROUP_MODUL'],
                'MODUL' => []
            ];
        }

        foreach ($mapping as $map) {
            $groupId = $map['GROUP_ID'];
            $modulId = $map['MODUL_ID'];
            if (isset($modulByGroup[$groupId]) && isset($modulIndex[$modulId])) {
                $modulByGroup[$groupId]['MODUL'][] = $modulIndex[$modulId];
            }
        }

        $gedungList = $this->gedungModel->where('STATUS', 1)->findAll();
        $ruanganList = $this->ruanganModel->where('STATUS', 1)->orderBy('RUANGAN', 'ASC')->findAll();

        $ruanganByGedung = [];
        foreach ($gedungList as $gedung) {
            $ruanganByGedung[$gedung['ID']] = [
                'GEDUNG'  => $gedung['GEDUNG'],
                'RUANGAN' => []
            ];
        }
        foreach ($ruanganList as $ruangan) {
            if (isset($ruanganByGedung[$ruangan['GEDUNG_ID']])) {
                $ruanganByGedung[$ruangan['GEDUNG_ID']]['RUANGAN'][] = $ruangan;
            }
        }

        $aksesTerpilih = [
            'JABATAN' => [],
            'MODUL'   => [],
            'RUANGAN' => []
        ];

        if ($pegawaiId) {
            $akses = $this->aksesModel->where('PEGAWAI_ID', $pegawaiId)->findAll();
            foreach ($akses as $a) {
                if (!empty($a['JABATAN_ID'])) {
                    $aksesTerpilih['JABATAN'][] = (int)$a['JABATAN_ID'];
                }
                if (!empty($a['MODUL_ID'])) {
                    $aksesTerpilih['MODUL'][] = (int)$a['MODUL_ID'];
                }
                if (!empty($a['RUANGAN_ID'])) {
                    $aksesTerpilih['RUANGAN'][] = (int)$a['RUANGAN_ID'];
                }
            }
        }

        ksort($modulByGroup);

        return view('kepegawaian/managemenakses/modals_form_akun', [
            'jabatan'        => $jabatan,
            'modulByGroup'   => $modulByGroup,
            'ruanganByGedung'=> $ruanganByGedung,
            'aksesTerpilih'  => $aksesTerpilih,
            'pegawaiId'      => $pegawaiId
        ]);
    }

    public function save_akses()
    {
        $db = db_connect();
        $aksesModel = new ModelAkses();

        $pegawaiId = $this->request->getPost('PEGAWAI_ID');
        if (!$pegawaiId) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Pegawai tersebut, belum memiliki Username dan Password. Silahkan buatkan akun pada menu Pegawai.'
            ]);
        }

        $post = $this->request->getPost();

        // Bersihkan akses lama
        $aksesModel->where('PEGAWAI_ID', $pegawaiId)->delete();

        $dataInsert = [];

        foreach ($post as $key => $value) {
            if (strpos($key, 'JABATAN_') === 0) {
                $dataInsert[] = [
                    'ID' => mt_rand(111111,999999),
                    'PEGAWAI_ID' => $pegawaiId,
                    'JABATAN_ID' => $value,
                    'MODUL_ID'   => null,
                    'RUANGAN_ID' => null,
                ];
            }
            if (strpos($key, 'MODUL_') === 0) {
                $dataInsert[] = [
                    'ID' => mt_rand(111111,999999),
                    'PEGAWAI_ID' => $pegawaiId,
                    'JABATAN_ID' => null,
                    'MODUL_ID'   => $value,
                    'RUANGAN_ID' => null,
                ];
            }
            if (strpos($key, 'RUANGAN_') === 0) {
                $dataInsert[] = [
                    'ID' => mt_rand(111111,999999),
                    'PEGAWAI_ID' => $pegawaiId,
                    'JABATAN_ID' => null,
                    'MODUL_ID'   => null,
                    'RUANGAN_ID' => $value,
                ];
            }
        }

        if (!empty($dataInsert)) {
            $aksesModel->insertBatch($dataInsert);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Akses berhasil disimpan'
        ]);
    }

    private function prepareBatchInsert($postData, $pegawaiId)
    {
        $batchInsert = [];
        foreach ($postData as $key => $value) {
            if (preg_match('/^(JABATAN|MODUL|RUANGAN)_\d+$/', $key)) {
                $type = explode('_', $key)[0];
                $batchInsert[] = [
                    'PEGAWAI_ID' => $pegawaiId,
                    'JABATAN_ID' => $type === 'JABATAN' ? (int)$value : null,
                    'MODUL_ID' => $type === 'MODUL' ? (int)$value : null,
                    'RUANGAN_ID' => $type === 'RUANGAN' ? (int)$value : null,
                ];
            }
        }
        return $batchInsert;
    }





}