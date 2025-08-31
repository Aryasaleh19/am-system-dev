<?php 
namespace App\Controllers\Perencanaan;

use App\Controllers\BaseController;
use App\Models\Perencanaan\KegiatanModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Kegiatan extends BaseController
{
    protected $kegiatanModel;

    public function __construct()
    {
        $this->kegiatanModel = new KegiatanModel();
    }

    // Halaman utama
    public function index()
    {

        return view('perencanaan/kegiatan/index', [
            'title' => '🗓️ Perencanaan Kegiatan'
        ]);
    }

    // DataTables ajax
    public function ajaxList()
    {
        $request = service('request');
        $draw = intval($request->getGet('draw'));
        $start = intval($request->getGet('start'));
        $length = intval($request->getGet('length'));
        $search = $request->getGet('search')['value'] ?? '';
        $orderInput = $request->getGet('order')[0] ?? null;

        $columns = ['ID_KEGIATAN', 'NAMA_KEGIATAN', 'TAHUN', 'ANGGARAN', 'STATUS', 'NAMA_PROGRAM'];

        $order = null;
        if ($orderInput) {
            $colIndex = intval($orderInput['column']);
            $dir = $orderInput['dir'];
            $order = ['column' => $columns[$colIndex], 'dir' => $dir];
        }

        $model = $this->kegiatanModel;

        $dataList = $model->getKegiatanWithProgram($search, $order, $length, $start);
        $recordsTotal = $model->countAllResults();
        $recordsFiltered = $model->countFiltered($search);

        $data = [];
        foreach ($dataList as $i => $row) {
            $data[] = [
                'no' => $start + $i + 1,
                'ID_KEGIATAN' => $row['ID_KEGIATAN'],
                'NAMA_KEGIATAN' => $row['NAMA_KEGIATAN'],
                'TAHUN' => $row['TAHUN'],
                'ANGGARAN' => $row['ANGGARAN'],
                'STATUS' => $row['STATUS'],
                'NAMA_PROGRAM' => $row['NAMA_PROGRAM']
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }


    // Ambil 1 data berdasarkan ID
   public function get($id)
    {
        $data = $this->kegiatanModel->getKegiatanProgramById($id);

        if($data) {
            // langsung return $data, tidak perlu $data[0]
            return $this->response->setJSON($data);
        } else {
        return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }

    // Simpan data baru
    public function save()
    {
        try {
            $data = [
                'ID_PROGRAM'    => $this->request->getPost('ID_PROGRAM'),
                'NAMA_KEGIATAN' => $this->request->getPost('NAMA_KEGIATAN'),
                'TAHUN'         => $this->request->getPost('TAHUN'),
                'ANGGARAN'      => $this->request->getPost('ANGGARAN'),
                'STATUS'        => $this->request->getPost('STATUS'),
            ];

            $this->kegiatanModel->insert($data);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Kegiatan berhasil ditambahkan'
            ]);
        } catch (DatabaseException $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal menambahkan kegiatan: ' . $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        try {
            $id = $this->request->getPost('ID_KEGIATAN');

            $data = [
                'NAMA_KEGIATAN' => $this->request->getPost('NAMA_KEGIATAN'),
                'TAHUN'         => $this->request->getPost('TAHUN'),
                'ANGGARAN'      => $this->request->getPost('ANGGARAN'),
                'STATUS'        => $this->request->getPost('STATUS'),
            ];

            $this->kegiatanModel->update($id, $data);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Kegiatan berhasil diupdate'
            ]);
        } catch (DatabaseException $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal update kegiatan: ' . $e->getMessage()
            ]);
        }
    }


    // Hapus data
    public function delete($id)
    {
        try {
            $this->kegiatanModel->delete($id);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Kegiatan berhasil dihapus'
            ]);
        } catch (DatabaseException $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
        }
    }
}