<?php 
namespace App\Controllers\Referensi;

use App\Controllers\BaseController;
use App\Models\Referensi\ModelSekolah;

class Sekolah extends BaseController
{
    protected $modelSekolah;

    public function __construct()
    {
        $this->modelSekolah = new ModelSekolah();
    }

    public function index()
    {
        return view('referensi/sekolah/index', ['title' => '🏫 Sekolah / Pendidikan']);
    }

    
    public function form()
    {
        return view('referensi/sekolah/modals_form');
    }

    public function ajaxList()
    {
        $request = service('request');
        $db = \Config\Database::connect();
        $builder = $this->modelSekolah->builder(); // contoh: ganti sesuai model kamu

        $draw = intval($request->getGet('draw'));
        $start = intval($request->getGet('start'));
        $length = intval($request->getGet('length'));
        $searchValue = $request->getGet('search')['value'] ?? '';

        // Total records tanpa filter
        $totalRecords = $builder->countAllResults(false);

        // Jika ada pencarian
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('NAMA_SEKOLAH', $searchValue)
                    ->orLike('KODE', $searchValue)
                    ->orLike('STATUS', $searchValue)
                    ->groupEnd();
        }

        // Total records setelah filter
        $totalFiltered = $builder->countAllResults(false);

        // Sorting
        $order = $request->getGet('order');
        $columns = ['ID', 'NAMA_SEKOLAH', 'KODE', 'STATUS'];
        if (!empty($order)) {
            $colIndex = intval($order[0]['column']);
            $dir = $order[0]['dir'];
            $orderCol = $columns[$colIndex] ?? 'NAMA_SEKOLAH';
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
        $this->modelSekolah->insert([
            'KODE' => $this->request->getPost('KODE'),
            'NAMA_SEKOLAH' => $this->request->getPost('NAMA_SEKOLAH'),
            'STATUS' => $this->request->getPost('STATUS')
        ]);
        return $this->response->setJSON(['status' => 'saved']);
    }
    public function simpan()
    {
        $data = [
            'KODE' => $this->request->getPost('kode'),
            'NAMA_SEKOLAH' => $this->request->getPost('nama_sekolah'),
            'STATUS' => $this->request->getPost('status')
        ];
        $saved = $this->modelSekolah->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $data = [
            'KODE' => $this->request->getPost('kode'),
            'NAMA_SEKOLAH' => $this->request->getPost('nama_sekolah'),
            'STATUS' => $this->request->getPost('status')
        ];
        $updated = $this->modelSekolah->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }


    public function get($id)
    {
        return $this->response->setJSON($this->modelSekolah->find($id));
    }

    public function delete($id)
    {
        $this->modelSekolah->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }
}

?>