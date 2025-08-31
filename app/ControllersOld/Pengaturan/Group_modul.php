<?php 
namespace App\Controllers\Pengaturan;

use App\Controllers\BaseController;
use App\Models\Pengaturan\Model_group_modul;

class Group_modul extends BaseController
{
    protected $modulModel;

    public function __construct()
    {
        $this->modulModel = new Model_group_modul();
    }

    public function index()
    {
        return view('pengaturan/group_modul/index', ['title' => '📦 Group Modul']);
    }

    public function form()
    {
        return view('pengaturan/group_modul/modals_form');
    }

public function ajaxList()
{
    $request = service('request');
    $db = \Config\Database::connect();
    $builder = $this->modulModel->builder(); // contoh: ganti sesuai model kamu

    $draw = intval($request->getGet('draw'));
    $start = intval($request->getGet('start'));
    $length = intval($request->getGet('length'));
    $searchValue = $request->getGet('search')['value'] ?? '';

    // Total records tanpa filter
    $totalRecords = $builder->countAllResults(false);

    // Jika ada pencarian
    if (!empty($searchValue)) {
        $builder->groupStart()
                ->like('GROUP_MODUL', $searchValue)
                ->orLike('STATUS', $searchValue)
                ->groupEnd();
    }

    // Total records setelah filter
    $totalFiltered = $builder->countAllResults(false);

    // Sorting
    $order = $request->getGet('order');
    $columns = ['ID', 'GROUP_MODUL', 'STATUS'];
    if (!empty($order)) {
        $colIndex = intval($order[0]['column']);
        $dir = $order[0]['dir'];
        $orderCol = $columns[$colIndex] ?? 'GROUP_MODUL';
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
        $this->modulModel->insert([
            'GROUP_MODUL' => $this->request->getPost('MODUL'),
            'STATUS' => $this->request->getPost('STATUS')
        ]);
        return $this->response->setJSON(['status' => 'saved']);
    }
    public function simpan()
    {
        $data = [
            'GROUP_MODUL' => $this->request->getPost('modul'),
            'STATUS' => $this->request->getPost('status')
        ];
        $saved = $this->modulModel->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $data = [
            'GROUP_MODUL' => $this->request->getPost('modul'),
            'STATUS' => $this->request->getPost('status')
        ];
        $updated = $this->modulModel->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }


    public function get($id)
    {
        return $this->response->setJSON($this->modulModel->find($id));
    }

    public function delete($id)
    {
        $this->modulModel->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }
}

?>