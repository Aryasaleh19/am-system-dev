<?php 
namespace App\Controllers\Keuangan;

use App\Controllers\BaseController;
use App\Models\Keuangan\ModelRekeningBank;

class RekeningBank extends BaseController
{
    protected $modelRekeningBank;

    public function __construct()
    {
        $this->modelRekeningBank = new ModelRekeningBank();
    }

    public function index()
    {
        return view('keuangan/rekening/index', ['title' => '🏦 Rekening Bank']);
    }

    
    public function form()
    {
        return view('keuangan/rekening/modals_form');
    }

    public function ajaxList()
    {
        $request = service('request');
        $db = \Config\Database::connect();
        $builder = $this->modelRekeningBank->builder(); // contoh: ganti sesuai model kamu

        $draw = intval($request->getGet('draw'));
        $start = intval($request->getGet('start'));
        $length = intval($request->getGet('length'));
        $searchValue = $request->getGet('search')['value'] ?? '';

        // Total records tanpa filter
        $totalRecords = $builder->countAllResults(false);

        // Jika ada pencarian
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('NAMA_BANK', $searchValue)
                    ->orLike('STATUS', $searchValue)
                    ->groupEnd();
        }

        // Total records setelah filter
        $totalFiltered = $builder->countAllResults(false);

        // Sorting
        $order = $request->getGet('order');
        $columns = ['ID', 'NO_REKENING', 'NAMA_BANK', 'SALDO_AWAL', 'STATUS'];
        if (!empty($order)) {
            $colIndex = intval($order[0]['column']);
            $dir = $order[0]['dir'];
            $orderCol = $columns[$colIndex] ?? 'NO_REKENING';
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
        $this->modelRekeningBank->insert([
            'NO_REKENING' => $this->request->getPost('no_rekening'),
            'NAMA_BANK' => $this->request->getPost('bank'),
            'SALDO_AWAL' => $this->request->getPost('saldo_awal'),
            'SALDO_AKHIR' => $this->request->getPost('saldo_awal'),
            'STATUS' => $this->request->getPost('status')
        ]);
        return $this->response->setJSON(['status' => 'saved']);
    }
    public function save()
    {
        $data = [
            'NO_REKENING' => $this->request->getPost('no_rekening'),
            'NAMA_BANK' => $this->request->getPost('bank'),
            'SALDO_AWAL' => $this->request->getPost('saldo_awal'),
            'SALDO_AKHIR' => $this->request->getPost('saldo_awal'),
            'STATUS' => $this->request->getPost('status')
        ];
        $saved = $this->modelRekeningBank->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }


    public function update()
    {
        $id = $this->request->getPost('id');
        $data = [
            'NO_REKENING' => $this->request->getPost('no_rekening'),
            'NAMA_BANK' => $this->request->getPost('bank'),
            'SALDO_AWAL' => $this->request->getPost('saldo_awal'),
            'SALDO_AKHIR' => $this->request->getPost('saldo_awal'),
            'STATUS' => $this->request->getPost('status')
        ];
        $updated = $this->modelRekeningBank->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }


    public function get($id)
    {
        return $this->response->setJSON($this->modelRekeningBank->find($id));
    }

    public function delete($id)
    {
        $this->modelRekeningBank->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }
}

?>