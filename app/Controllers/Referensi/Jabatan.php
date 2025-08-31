<?php 
namespace App\Controllers\Referensi;

use App\Controllers\BaseController;
use App\Models\Referensi\ModelJabatan;
use App\Models\Referensi\ModelJabatanPenerimaan;
use App\Models\Referensi\ModelJabatanTupoksi;
use App\Models\Kepegawaian\ModelPengguna;
use App\Models\Referensi\ModelJabatanAbsensi;

class Jabatan extends BaseController
{
    protected $modelJabatan;
    protected $modelJabatanPenerimaan;
    protected $modelPengguna;
    protected $modelJabatanTupoksi;
    protected $modelJabatanAbsensi;

    public function __construct()
    {
        $this->modelJabatan = new ModelJabatan();
        $this->modelJabatanPenerimaan = new ModelJabatanPenerimaan();
        $this->modelPengguna = new ModelPengguna();
        $this->modelJabatanTupoksi = new ModelJabatanTupoksi();
        $this->modelJabatanAbsensi = new ModelJabatanAbsensi();
    }

    public function index()
    {
        return view('referensi/jabatan/index', ['title' => '🎖️ Jabatan']);
    }

    
    public function form()
    {
        return view('referensi/jabatan/modals_form');
    }
    public function formPengaturan()
    {
        return view('referensi/jabatan/modals_pengaturan');
    }

    public function ajaxList()
    {
        $request = service('request');
        $db = \Config\Database::connect();
        $builder = $this->modelJabatan->builder(); // contoh: ganti sesuai model kamu

        $draw = intval($request->getGet('draw'));
        $start = intval($request->getGet('start'));
        $length = intval($request->getGet('length'));
        $searchValue = $request->getGet('search')['value'] ?? '';

        // Total records tanpa filter
        $totalRecords = $builder->countAllResults(false);

        // Jika ada pencarian
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('JABATAN', $searchValue)
                    ->orLike('STATUS', $searchValue)
                    ->groupEnd();
        }

        // Total records setelah filter
        $totalFiltered = $builder->countAllResults(false);

        // Sorting
        $order = $request->getGet('order');
        $columns = ['ID', 'JABATAN', 'STATUS'];
        if (!empty($order)) {
            $colIndex = intval($order[0]['column']);
            $dir = $order[0]['dir'];
            $orderCol = $columns[$colIndex] ?? 'JABATAN';
            $builder->orderBy($orderCol, $dir);
        }

        // Limit dan offset (paging)
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        // Ambil data
        $list = $builder->get()->getResultArray();

        $response = [
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $list
        ];

        return $this->response->setJSON($response);
    }

    public function store()
    {
        $this->modelJabatan->insert([
            'JABATAN' => $this->request->getPost('jabatan'),
            'STATUS' => $this->request->getPost('status')
        ]);
        return $this->response->setJSON(['status' => 'saved']);
    }
    public function simpan()
    {
        $data = [
            'JABATAN' => $this->request->getPost('jabatan'),
            'STATUS' => $this->request->getPost('status')
        ];
        $saved = $this->modelJabatan->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $data = [
            'JABATAN' => $this->request->getPost('jabatan'),
            'STATUS' => $this->request->getPost('status')
        ];
        $updated = $this->modelJabatan->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }


    public function get($id)
    {
        return $this->response->setJSON($this->modelJabatan->find($id));
    }

    public function delete($id)
    {
        $this->modelJabatan->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }


    // khusus penerimaan jabatan
    function simpanPenerimaanJabatan(){
        $data = [
            'ID_JABATAN' => $this->request->getPost('id'),
            'JENIS_PENERIMAAN' => $this->request->getPost('penerimaan'),
            'JUMLAH' => $this->request->getPost('jumlah'),
            'OLEH' => session('user_id')
        ];
        $saved = $this->modelJabatanPenerimaan->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }

    function updatePenerimaan(){
        $id = $this->request->getPost('id_penerimaan');
        $data = [
            'JENIS_PENERIMAAN' => $this->request->getPost('penerimaan'),
            'JUMLAH' => $this->request->getPost('jumlah'),
            'OLEH' => session('user_id')
        ];
        $updated = $this->modelJabatanPenerimaan->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }

    public function ajaxListPenerimaan()
    {
        $request = service('request');
        $builder = $this->modelJabatanPenerimaan->builder();
        $builder->select('m_pegawai_penerimaan.*, pengguna.NAMA as NAMA_PENGGUNA'); // tambahkan kolom nama user
        $builder->join('pengguna', 'pengguna.PEGAWAI_ID = m_pegawai_penerimaan.OLEH', 'left');

        $draw       = intval($request->getGet('draw'));
        $start      = intval($request->getGet('start'));
        $length     = intval($request->getGet('length'));
        $searchValue= $request->getGet('search')['value'] ?? '';
        $idJabatan  = $request->getGet('id');

        if (!empty($idJabatan)) {
            $builder->where('m_pegawai_penerimaan.ID_JABATAN', $idJabatan);
        }

        // Total records tanpa filter
        $totalRecords = $builder->countAllResults(false);

        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('JENIS_PENERIMAAN', $searchValue)
                    ->orLike('STATUS', $searchValue)
                    ->orLike('users.nama', $searchValue) // cari juga di nama user
                    ->groupEnd();
        }

        $totalFiltered = $builder->countAllResults(false);

        $order = $request->getGet('order');
        $columns = ['ID', 'JENIS_PENERIMAAN', 'JUMLAH', 'NAMA_PENGGUNA', 'STATUS'];
        if (!empty($order)) {
            $colIndex = intval($order[0]['column']);
            $dir      = $order[0]['dir'];
            $orderCol = $columns[$colIndex] ?? 'JENIS_PENERIMAAN';
            $builder->orderBy($orderCol, $dir);
        }

        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $list = $builder->get()->getResultArray();

        $response = [
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $list
        ];

        return $this->response->setJSON($response);
    }

    public function deletePenerimaan($id)
    {
        $this->modelJabatanPenerimaan->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }


    // halaman halaman pada modals jabatan
    public function penerimaan()
    {
        $idJabatan = $this->request->getGet('id'); // ambil id jabatan dari ?id=...
        $data = [];

        if ($idJabatan) {
            $data['id_jabatan'] = $idJabatan;
            $jabatanRow = $this->modelJabatan->where('ID', $idJabatan)->first();
            $data['jabatan'] = $jabatanRow ? $jabatanRow['JABATAN'] : '';
        }

        return view('referensi/jabatan/penerimaan', $data);
    }

    public function tupoksi()
    {
         $idJabatan = $this->request->getGet('id'); // ambil id jabatan dari ?id=...
        $data = [];

        if ($idJabatan) {
            $data['id_jabatan'] = $idJabatan;
            $jabatanRow = $this->modelJabatan->where('ID', $idJabatan)->first();
            $data['jabatan'] = $jabatanRow ? $jabatanRow['JABATAN'] : '';
        }


        return view('referensi/jabatan/tupoksi', $data);
    }
    
    public function absensi()
    {
        $idJabatan = $this->request->getGet('id'); // ambil id jabatan dari ?id=...
        $data = [];

        if ($idJabatan) {
            $data['id_jabatan'] = $idJabatan;

            // ambil nama jabatan
            $jabatanRow = $this->modelJabatan->where('ID', $idJabatan)->first();
            $data['jabatan'] = $jabatanRow ? $jabatanRow['JABATAN'] : '';

            // ambil data absensi yang sudah ada untuk jabatan ini
            $absensiRows = $this->modelJabatanAbsensi
                                ->where('ID_JABATAN', $idJabatan)
                                ->findAll();

            // susun array agar mudah diakses berdasarkan hari
            $absensiByHari = [];
            foreach ($absensiRows as $row) {
                $absensiByHari[$row['HARI']] = $row;
            }

            $data['absensi'] = $absensiByHari;
        }

        return view('referensi/jabatan/absensi', $data);
    }


    public function saveAbsensi()
    {
        if(!$this->request->isAJAX()){
            return redirect()->back();
        }

        $request = $this->request;
        $id_jabatan = $request->getPost('id_jabatan');
        $hariList   = $request->getPost('hari');     
        $datangList = $request->getPost('datang');  
        $pulangList = $request->getPost('pulang');  
        $statusList = $request->getPost('status');  
        $oleh       = session()->get('user_id');

        if(!$id_jabatan || !$hariList){
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap!'
            ]);
        }

        try {
            foreach($hariList as $i => $hari){
                $datang = $datangList[$i];
                $pulang = $pulangList[$i];
                $status = $statusList[$i];

                $cek = $this->modelJabatanAbsensi
                            ->where('ID_JABATAN', $id_jabatan)
                            ->where('HARI', $hari)
                            ->first();

                $data = [
                    'ID_JABATAN' => $id_jabatan,
                    'HARI'       => $hari,
                    'DATANG'     => $datang,
                    'PULANG'     => $pulang,
                    'STATUS'     => $status,
                    'OLEH'       => $oleh
                ];

                if($cek){
                    $this->modelJabatanAbsensi->update($cek['ID'], $data);
                } else {
                    $this->modelJabatanAbsensi->insert($data);
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pengaturan absensi berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }





    // khusus proses tupoksi
    public function ajaxListTupoksi()
    {
        $request = service('request');
        $db = \Config\Database::connect();
        $builder = $this->modelJabatanTupoksi->builder(); // contoh: ganti sesuai model kamu
        $builder->select('m_pegawai_tupoksi.*, pengguna.NAMA as NAMA_PENGGUNA'); // tambahkan kolom nama user
        $builder->join('pengguna', 'pengguna.PEGAWAI_ID = m_pegawai_tupoksi.OLEH', 'left');

        $draw = intval($request->getGet('draw'));
        $start = intval($request->getGet('start'));
        $length = intval($request->getGet('length'));
        $searchValue = $request->getGet('search')['value'] ?? '';

        // Total records tanpa filter
        $totalRecords = $builder->countAllResults(false);

        // Jika ada pencarian
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('URAIAN_TUPOKSI', $searchValue)
                    ->orLike('STATUS', $searchValue)
                    ->groupEnd();
        }

        // Total records setelah filter
        $totalFiltered = $builder->countAllResults(false);

        // Sorting
        $order = $request->getGet('order');
        $columns = ['ID', 'URAIAN_TUPOKSI', 'STATUS'];
        if (!empty($order)) {
            $colIndex = intval($order[0]['column']);
            $dir = $order[0]['dir'];
            $orderCol = $columns[$colIndex] ?? 'URAIAN_TUPOKSI';
            $builder->orderBy($orderCol, $dir);
        }

        // Limit dan offset (paging)
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        // Ambil data
        $list = $builder->get()->getResultArray();

        $response = [
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $list
        ];

        return $this->response->setJSON($response);
    }

    function simpanTupoksiJabatan(){
        $data = [
            'ID_JABATAN' => $this->request->getPost('id'),
            'URAIAN_TUPOKSI' => $this->request->getPost('tupoksi'),
            'BEBAN' => $this->request->getPost('beban_waktu'),
            'OLEH' => session('user_id'),
            'STATUS' => $this->request->getPost('status')
        ];
        $saved = $this->modelJabatanTupoksi->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }

    function updateTupoksi(){
        $id = $this->request->getPost('id_tupoksi');
        $data = [
            'URAIAN_TUPOKSI' => $this->request->getPost('tupoksi'),
            'BEBAN' => $this->request->getPost('beban_waktu'),
            'OLEH' => session('user_id'),
            'STATUS' => $this->request->getPost('status')
        ];
        $updated = $this->modelJabatanTupoksi->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }

    public function deleteTupoksi($id)
    {
        $this->modelJabatanTupoksi->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }

}

?>