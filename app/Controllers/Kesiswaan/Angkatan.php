<?php 
namespace App\Controllers\Kesiswaan;

use App\Controllers\BaseController;
use App\Models\Kesiswaan\ModelAngkatan;

class Angkatan extends BaseController
{
    protected $modelAngkatan;

    public function __construct()
    {
        $this->modelAngkatan = new ModelAngkatan();
    }

    public function index()
    {
        return view('kesiswaan/angkatan/index', ['title' => '📆 Tahun Angkatan']);
    }

    
    public function form()
    {
        return view('kesiswaan/angkatan/modals_form');
    }

    public function ajaxList()
    {
        $request = service('request');
        $db = \Config\Database::connect();
        $builder = $this->modelAngkatan->builder(); // contoh: ganti sesuai model kamu

        $draw = intval($request->getGet('draw'));
        $start = intval($request->getGet('start'));
        $length = intval($request->getGet('length'));
        $searchValue = $request->getGet('search')['value'] ?? '';

        // Total records tanpa filter
        $totalRecords = $builder->countAllResults(false);

        // Jika ada pencarian
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('ANGKATAN', $searchValue)
                    ->orLike('STATUS', $searchValue)
                    ->groupEnd();
        }

        // Total records setelah filter
        $totalFiltered = $builder->countAllResults(false);

        // Sorting
        $order = $request->getGet('order');
        $columns = ['ID', 'ANGKATAN', 'STATUS'];
        if (!empty($order)) {
            $colIndex = intval($order[0]['column']);
            $dir = $order[0]['dir'];
            $orderCol = $columns[$colIndex] ?? 'ANGKATAN';
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
        $this->modelAngkatan->insert([
            'ANGKATAN' => $this->request->getPost('angkatan'),
            'STATUS' => $this->request->getPost('status')
        ]);
        return $this->response->setJSON(['status' => 'saved']);
    }
    public function save()
    {
        $data = [
            'ANGKATAN' => $this->request->getPost('angkatan'),
            'STATUS' => $this->request->getPost('status')
        ];
        $saved = $this->modelAngkatan->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $data = [
            'ANGKATAN' => $this->request->getPost('angkatan'),
            'STATUS' => $this->request->getPost('status')
        ];
        $updated = $this->modelAngkatan->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }


    public function get($id)
    {
        return $this->response->setJSON($this->modelAngkatan->find($id));
    }

    public function delete($id)
    {
        $this->modelAngkatan->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }

    public function getAngkatan()
    {
        $angkatanList = $this->modelAngkatan->findAll();
        return $this->response->setJSON(array_map(function($item) {
            return [
                'id' => $item['ID'],
                'angkatan' => $item['ANGKATAN']
            ];
        }, $angkatanList));
    }
}

?>