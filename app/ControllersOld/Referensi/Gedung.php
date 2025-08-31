<?php 
namespace App\Controllers\Referensi;

use App\Controllers\BaseController;
use App\Models\Referensi\ModelGedung;
use App\Models\Referensi\ModelRuangan;

class Gedung extends BaseController
{
    protected $modelGedung;
    protected $modelRuangan;

    public function __construct()
    {
        $this->modelGedung = new ModelGedung();
        $this->modelRuangan = new ModelRuangan();
    }

    public function index()
    {
        return view('referensi/gedung/index', ['title' => '📦 Group Modul']);
    }

    public function form()
    {
        return view('referensi/gedung/modals_form');
    }
    public function formMaping()
    {
        return view('referensi/gedung/modals_form_maping');
    }

    public function ajaxList()
{
    $request = service('request');

    $draw = intval($request->getGet('draw'));
    $start = intval($request->getGet('start'));
    $length = intval($request->getGet('length'));
    $searchValue = $request->getGet('search')['value'] ?? '';
    $order = $request->getGet('order');
    $orderColumn = $order[0]['column'] ?? 1;
    $orderDir = $order[0]['dir'] ?? 'asc';

    $totalRecords = $this->modelGedung->countAllRecords();
    $totalFiltered = $this->modelGedung->countFilteredRecords($searchValue);
    $list = $this->modelGedung->getDatatables($start, $length, $searchValue, $orderColumn, $orderDir);

    $response = [
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalFiltered,
        "data" => $list
    ];

    return $this->response->setJSON($response);
}



    public function ajaxListRuangan($gedung_id)
    {
        $data = $this->modelRuangan->where('GEDUNG_ID', $gedung_id)->findAll();
        return $this->response->setJSON(['data' => $data]);
    }

    public function store()
    {
        $this->modelGedung->insert([
            'GEDUNG' => $this->request->getPost('gedung'),
            'STATUS' => $this->request->getPost('STATUS')
        ]);
        return $this->response->setJSON(['status' => 'saved']);
    }
    public function simpan()
    {
        $data = [
            'GEDUNG' => $this->request->getPost('gedung'),
            'STATUS' => $this->request->getPost('status')
        ];
        $saved = $this->modelGedung->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }
    public function simpanRuangan()
    {
        $postData = $this->request->getPost();
        log_message('debug', 'POST simpanRuangan: ' . json_encode($postData));

        if (empty($postData['ruangan']) || empty($postData['gedung_id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ruangan dan Gedung harus diisi'
            ]);
        }

        $data = [
            'RUANGAN' => $postData['ruangan'],
            'GEDUNG_ID' => $postData['gedung_id'],
            'STATUS' => $postData['status'] ?? 1
        ];
        $saved = $this->modelRuangan->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }


    public function update()
    {
        $id = $this->request->getPost('id');
        $data = [
            'GEDUNG' => $this->request->getPost('gedung'),
            'STATUS' => $this->request->getPost('status')
        ];
        $updated = $this->modelGedung->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }
    public function updateRuangan()
    {
        $id = $this->request->getPost('id');
        $data = [
            'RUANGAN' => $this->request->getPost('ruangan'),
            'GEDUNG_ID' => $this->request->getPost('gedung_id'),
            'STATUS' => $this->request->getPost('status')
        ];
        $updated = $this->modelRuangan->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }


    public function get($id)
    {
        return $this->response->setJSON($this->modelGedung->find($id));
    }
    public function getRuangan($id)
    {
        return $this->response->setJSON($this->modelRuangan->find($id));
    }

    public function delete($id)
    {
        $this->modelGedung->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }
    public function deleteRuangan($id)
    {
        $this->modelRuangan->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }
}

?>