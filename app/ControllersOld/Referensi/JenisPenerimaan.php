<?php 
namespace App\Controllers\Referensi;

use App\Controllers\BaseController;
use App\Models\Referensi\ModelJenisPenerimaan;
use App\Models\Referensi\ModelSekolah;

class JenisPenerimaan extends BaseController
{
    protected $modelJenisPenerimaan;
    protected $modelSekolah;


    public function __construct()
    {
        $this->modelJenisPenerimaan = new ModelJenisPenerimaan();
        $this->modelSekolah = new ModelSekolah();
    }

    public function index()
    {
        return view('referensi/penerimaan/index', ['title' => '💵 Jenis Penerimaan']);
    }

    
    public function form()
    {
        $sekolah = $this->modelSekolah->findAll();
        
        $data = [
            'sekolah' => $sekolah
        ];
        return view('referensi/penerimaan/modals_form', $data);
    }

    public function ajaxList()
    {
        $request = service('request');

        $draw = intval($request->getGet('draw'));
        $start = intval($request->getGet('start'));
        $length = intval($request->getGet('length'));
        $searchValue = $request->getGet('search')['value'] ?? '';

        // Builder utama
        $baseBuilder = $this->modelJenisPenerimaan->getDataJenisPenerimaan();

        // Clone untuk hitung total records tanpa filter
        $totalBuilder = clone $baseBuilder;
        $totalRecords = $totalBuilder->countAllResults();

        // Clone untuk filter
        $filteredBuilder = clone $baseBuilder;

        if (!empty($searchValue)) {
            $filteredBuilder->groupStart()
                ->like('m_penerimaan_jenis.JENIS_PENERIMAAN', $searchValue)
                ->orLike('m_penerimaan_jenis.KATEGORI', $searchValue)
                ->orLike('m_penerimaan_jenis.STATUS', $searchValue)
                ->groupEnd();
        }

        $totalFiltered = $filteredBuilder->countAllResults();

        // Clone untuk query data utama
        $dataBuilder = clone $baseBuilder;

        if (!empty($searchValue)) {
            $dataBuilder->groupStart()
                ->like('m_penerimaan_jenis.JENIS_PENERIMAAN', $searchValue)
                ->orLike('m_penerimaan_jenis.KATEGORI', $searchValue)
                ->orLike('m_penerimaan_jenis.STATUS', $searchValue)
                ->groupEnd();
        }

        // Sorting
        $order = $request->getGet('order');
        $columns = [
            'm_penerimaan_jenis.ID',
            'm_sekolah.NAMA_SEKOLAH',
            'm_penerimaan_jenis.JENIS_PENERIMAAN',
            'm_penerimaan_jenis.JUMLAH',
            'm_penerimaan_jenis.KATEGORI',
            'm_penerimaan_jenis.TENOR',
            'm_penerimaan_jenis.SATUAN',
            'm_penerimaan_jenis.STATUS',
        ];
        if (!empty($order)) {
            foreach ($order as $ord) {
                $colIndex = intval($ord['column']);
                $dir = $ord['dir'];
                $orderCol = $columns[$colIndex] ?? 'm_penerimaan_jenis.JENIS_PENERIMAAN';
                $dataBuilder->orderBy($orderCol, $dir);
            }
        }


        // Limit dan offset (paging)
        if ($length != -1) {
            $dataBuilder->limit($length, $start);
        }

        // Ambil data
        $list = $dataBuilder->get()->getResultArray();

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
        $this->modelJenisPenerimaan->insert([
            'JENIS_PENERIMAAN' => $this->request->getPost('jenis'),
            'JUMLAH' => $this->request->getPost('jumlah'),
            'KATEGORI' => $this->request->getPost('kategori'),
            'TENOR' => $this->request->getPost('tenor'),
            'SATUAN' => $this->request->getPost('satuan'),
            'SEKOLAH_ID' => $this->request->getPost('sekolah_id'),
            'STATUS' => $this->request->getPost('status')
        ]);
        return $this->response->setJSON(['status' => 'saved']);
    }
    public function simpan()
    {
        $data = [
            'JENIS_PENERIMAAN' => $this->request->getPost('jenis'),
            'JUMLAH' => $this->request->getPost('jumlah'),
            'KATEGROI' => $this->request->getPost('kategori'),
            'TENOR' => $this->request->getPost('tenor'),
            'SATUAN' => $this->request->getPost('satuan'),
            'SEKOLAH_ID' => $this->request->getPost('sekolah_id'),
            'STATUS' => $this->request->getPost('status')
        ];
        $saved = $this->modelJenisPenerimaan->insert($data);
        if ($saved) {
            return $this->response->setJSON(['status' => 'saved', 'message' => 'Data berhasil disimpan']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $data = [
            'JENIS_PENERIMAAN' => $this->request->getPost('jenis'),
            'JUMLAH' => $this->request->getPost('jumlah'),
            'KATEGORI' => $this->request->getPost('kategori'),
            'TENOR' => $this->request->getPost('tenor'),
            'SATUAN' => $this->request->getPost('satuan'),
            'SEKOLAH_ID' => $this->request->getPost('sekolah_id'),
            'STATUS' => $this->request->getPost('status')
        ];
        $updated = $this->modelJenisPenerimaan->update($id, $data);
        if ($updated) {
            return $this->response->setJSON(['status' => 'updated', 'message' => 'Data berhasil diupdate']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update data']);
    }


    public function get($id)
    {
        return $this->response->setJSON($this->modelJenisPenerimaan->find($id));
    }

    public function delete($id)
    {
        $this->modelJenisPenerimaan->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }
}

?>