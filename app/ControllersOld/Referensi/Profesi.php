<?php 
namespace App\Controllers\Referensi;

use App\Controllers\BaseController;
use App\Models\Referensi\ModelProfesi;

class Profesi extends BaseController
{
    protected $modelProfesi;

    public function __construct()
    {
        $this->modelProfesi = new ModelProfesi();
    }

    public function index()
    {
        return view('referensi/profesi/index', ['title' => '👨‍🏫 Profesi']);
    }

    
    public function form()
    {
        return view('referensi/profesi/modals_form');
    }

public function ajaxList()
{
    $request = service('request');
    $db = \Config\Database::connect();
    $builder = $this->modelProfesi->builder(); // contoh: ganti sesuai model kamu

    $draw = intval($request->getGet('draw'));
    $start = intval($request->getGet('start'));
    $length = intval($request->getGet('length'));
    $searchValue = $request->getGet('search')['value'] ?? '';

    // Total records tanpa filter
    $totalRecords = $builder->countAllResults(false);

    // Jika ada pencarian
    if (!empty($searchValue)) {
        $builder->groupStart()
                ->like('PROFESI', $searchValue)
                ->orLike('STATUS', $searchValue)
                ->groupEnd();
    }

    // Total records setelah filter
    $totalFiltered = $builder->countAllResults(false);

    // Sorting
    $order = $request->getGet('order');
    $columns = ['ID', 'PROFESI', 'STATUS'];
    if (!empty($order)) {
        $colIndex = intval($order[0]['column']);
        $dir = $order[0]['dir'];
        $orderCol = $columns[$colIndex] ?? 'PROFESI';
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
        $this->modelProfesi->insert([
            'PROFESI' => $this->request->getPost('profesi'),
            'STATUS' => $this->request->getPost('status')
        ]);
        return $this->response->setJSON(['status' => 'saved']);
    }
    public function simpan()
    {
        $data = [
            'PROFESI' => $this->request->getPost('profesi'),
            'STATUS' => $this->request->getPost('status')
        ];
        $saved = $this->modelProfesi->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $data = [
            'PROFESI' => $this->request->getPost('profesi'),
            'STATUS' => $this->request->getPost('status')
        ];
        $updated = $this->modelProfesi->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }


    public function get($id)
    {
        return $this->response->setJSON($this->modelProfesi->find($id));
    }

    public function delete($id)
    {
        $this->modelProfesi->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }
}

?>