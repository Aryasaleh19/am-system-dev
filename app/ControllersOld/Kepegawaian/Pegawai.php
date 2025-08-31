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

class Pegawai extends BaseController
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
        return view('kepegawaian/pegawai/index', [
            'title' => '🧕 Data Pegawai'
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

        $totalFiltered = $builder->countAllResults(false); // total hasil filter
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

   public function simpan()
    {
        $data = $this->request->getPost();
        $this->pegawaiModel->insert($data);

        return $this->response->setJSON([
            'status' => 'saved',
            'message' => 'Data pegawai berhasil disimpan!'
        ]);
    }

    public function update()
    {
        $id = $this->request->getPost('ID');
        $data = $this->request->getPost();
        unset($data['ID']);
        $this->pegawaiModel->update($id, $data);
        return $this->response->setJSON([
            'status' => 'saved',
            'message' => 'Data pegawai berhasil diubah!'
        ]);
    }

    public function delete($id)
    {
        $this->pegawaiModel->delete($id);
        return $this->response->setJSON(['status' => true]);
    }

    public function form()
    {
        // Ambil data jabatan, jenis pegawai, dan profesi dari model
        $jabatans = $this->jabatanModel->findAll();
        $jenisPegawais = $this->jenisPegawaiModel->findAll();
        $profesis = $this->profesiModel->findAll();
        $agamas = $this->agamaModel->findAll();
        $pendidikans = $this->pendidikanModel->findAll();

        // Kirim data ke view modal_form.php
        return view('kepegawaian/pegawai/modals_form', [
            'jabatans' => $jabatans,
            'profesis' => $profesis,
            'jenispegawais' => $jenisPegawais,
            'agamas' => $agamas,
            'pendidikans' => $pendidikans,
        ]);
    }

    public function get_akun($id)
    {
        $data = $this->pegawaiModel->getWithAkun($id);

        if ($data) {
            // Tambahkan PEGAWAI_ID untuk pengisian form
            $data['PEGAWAI_ID'] = $data['ID'];

            // Hilangkan password jika ada
            unset($data['PASSWORD']);
        }

        return $this->response->setJSON($data ?? []);
    }


    public function formakun()
    {
       return view('kepegawaian/pegawai/modals_form_akun');
    }

    public function simpan_akun()
    {
        $id = $this->request->getPost('PEGAWAI_ID'); // selalu ada tapi belum tentu ada di db
        $data = $this->request->getPost();

        // Cek apakah PEGAWAI_ID ada di tabel pengguna
        $userExists = $this->penggunaModel->find($id);

        $validation = \Config\Services::validation();

        if ($userExists) {
            // Jika ada, berarti update
            $validation->setRules([
                'USERNAME' => [
                    'rules' => "required|min_length[4]|is_unique[pengguna.USERNAME,PEGAWAI_ID,{$id}]",
                    'errors' => [
                        'required' => 'Username wajib diisi.',
                        'min_length' => 'Username minimal 4 karakter.',
                        'is_unique' => 'Username sudah dipakai oleh pengguna lain.'
                    ]
                ],
                'NAMA' => 'required',
                'NIK' => 'required',
            ]);
        } else {
            // Kalau tidak ada, berarti insert
            $validation->setRules([
                'USERNAME' => [
                    'rules' => 'required|min_length[4]|is_unique[pengguna.USERNAME]',
                    'errors' => [
                        'required' => 'Username wajib diisi.',
                        'min_length' => 'Username minimal 4 karakter.',
                        'is_unique' => 'Username sudah dipakai oleh pengguna lain.'
                    ]
                ],
                'PASSWORD' => [
                    'rules' => 'required|min_length[6]',
                    'errors' => [
                        'required' => 'Password wajib diisi.',
                        'min_length' => 'Password minimal 6 karakter.'
                    ]
                ],
                'NAMA' => 'required',
                'NIK' => 'required',
            ]);
        }

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors()
            ]);
        }

        if (!empty($data['PASSWORD'])) {
            $data['PASSWORD'] = password_hash($data['PASSWORD'], PASSWORD_DEFAULT);
        } else {
            unset($data['PASSWORD']);
        }

        if ($userExists) {
            $this->penggunaModel->update($id, $data);
            $message = 'Perubahan akun berhasil!';
        } else {
            $this->penggunaModel->insert($data);
            $message = 'Akun berhasil ditambahkan!';
        }

        return $this->response->setJSON([
            'status' => 'saved',
            'message' => $message
        ]);
    }

}