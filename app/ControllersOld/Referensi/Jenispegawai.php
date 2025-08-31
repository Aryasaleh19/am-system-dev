<?php 
namespace App\Controllers\Referensi;

use App\Controllers\BaseController;
use App\Models\Referensi\ModelJenispegawai;

class JenisPegawai extends BaseController
{
    protected $modelJenisPegawai;

    public function __construct()
    {
        $this->modelJenisPegawai = new ModelJenispegawai();
    }

    public function index()
    {
        return view('referensi/jenispegawai/index', ['title' => '✔️ Jenis Pegawai']);
    }

    
    public function form()
    {
        return view('referensi/jenispegawai/modals_form');
    }

public function ajaxList()
{
    $request = service('request');
    $db = \Config\Database::connect();
    $builder = $this->modelJenisPegawai->builder(); // contoh: ganti sesuai model kamu

    $draw = intval($request->getGet('draw'));
    $start = intval($request->getGet('start'));
    $length = intval($request->getGet('length'));
    $searchValue = $request->getGet('search')['value'] ?? '';

    // Total records tanpa filter
    $totalRecords = $builder->countAllResults(false);

    // Jika ada pencarian
    if (!empty($searchValue)) {
        $builder->groupStart()
                ->like('JENIS_PEGAWAI', $searchValue)
                ->orLike('STATUS', $searchValue)
                ->groupEnd();
    }

    // Total records setelah filter
    $totalFiltered = $builder->countAllResults(false);

    // Sorting
    $order = $request->getGet('order');
    $columns = ['ID', 'JENIS_PEGAWAI', 'STATUS'];
    if (!empty($order)) {
        $colIndex = intval($order[0]['column']);
        $dir = $order[0]['dir'];
        $orderCol = $columns[$colIndex] ?? 'JENIS_PEGAWAI';
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
        $this->modelJenisPegawai->insert([
            'JENIS_PEGAWAI' => $this->request->getPost('jenis'),
            'STATUS' => $this->request->getPost('status')
        ]);
        return $this->response->setJSON(['status' => 'saved']);
    }
    public function simpan()
    {
        $data = [
            'JENIS_PEGAWAI' => $this->request->getPost('jenis'),
            'STATUS' => $this->request->getPost('status')
        ];
        $saved = $this->modelJenisPegawai->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $data = [
            'JENIS_PEGAWAI' => $this->request->getPost('jenis'),
            'STATUS' => $this->request->getPost('status')
        ];
        $updated = $this->modelJenisPegawai->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }


    public function get($id)
    {
        return $this->response->setJSON($this->modelJenisPegawai->find($id));
    }

    public function delete($id)
    {
        $this->modelJenisPegawai->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }
}

?>