<?php 
namespace App\Controllers\Pengaturan;

use App\Controllers\BaseController;
use App\Models\Pengaturan\ModelModul;

class Modul extends BaseController
{
    protected $modulModel;

    public function __construct()
    {
        $this->modulModel = new ModelModul();
    }

    public function index()
    {
        return view('pengaturan/modul/index', ['title' => '🧩 Modul']);
    }

    public function form()
    {
        return view('pengaturan/modul/modals_form');
    }

    public function ajaxList()
    {
        $request = service('request');
        $draw = intval($request->getGet('draw'));
        $start = intval($request->getGet('start'));
        $length = intval($request->getGet('length'));
        $searchValue = $request->getGet('search')['value'] ?? '';

        $builder = $this->modulModel->builder();

        // Total records sebelum filter
        $totalRecords = $builder->countAllResults(false);

        // Filter pencarian
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('MODUL', $searchValue)
                    ->orLike('STATUS', $searchValue)
                    ->groupEnd();
        }

        // Total records setelah filter
        $totalFiltered = $builder->countAllResults(false);

        // Sorting
        $order = $request->getGet('order');
        $columns = ['ID', 'MODUL', 'STATUS'];
        if (!empty($order)) {
            $colIndex = intval($order[0]['column']);
            $dir = $order[0]['dir'];
            $orderCol = $columns[$colIndex] ?? 'MODUL';
            $builder->orderBy($orderCol, $dir);
        }

        // Paging
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

    public function store()
    {
        $this->modulModel->insert([
            'MODUL' => $this->request->getPost('MODUL'),
            'LINK' => $this->request->getPost('link'),
            'STATUS' => $this->request->getPost('STATUS')
        ]);
        return $this->response->setJSON(['status' => 'saved']);
    }
    public function simpan()
    {
        $data = [
            'MODUL' => $this->request->getPost('modul'),
            'LINK' => $this->request->getPost('link'),
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
            'MODUL' => $this->request->getPost('modul'),
            'LINK' => $this->request->getPost('link'),
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