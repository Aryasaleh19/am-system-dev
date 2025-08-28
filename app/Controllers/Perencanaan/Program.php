<?php 
namespace App\Controllers\Perencanaan;

use App\Controllers\BaseController;
use App\Models\Perencanaan\ProgramModel;

class Program extends BaseController
{
    protected $programModel;

    public function __construct()
    {
        $this->programModel = new ProgramModel();
    }

    // Halaman utama
    public function index()
    {
        return view('perencanaan/program/index', [
            'title' => '📋 Perencanaan Program'
        ]);
    }
    public function ajaxList()
    {
        $request = service('request');
        $model = $this->programModel;

        $draw = intval($request->getGet('draw'));
        $start = intval($request->getGet('start'));
        $length = intval($request->getGet('length'));
        $search = $request->getGet('search')['value'] ?? '';
        $order = $request->getGet('order')[0] ?? null;

        // Clone model untuk filter
        $modelFiltered = clone $model;

        // Apply search
        if ($search) {
            $modelFiltered->groupStart()
                        ->like('NAMA_PROGRAM', $search)
                        ->orLike('TAHUN', $search)
                        ->groupEnd();
        }

        // Total sebelum filter
        $recordsTotal = $model->countAllResults(false);
        // Total setelah filter
        $recordsFiltered = $modelFiltered->countAllResults(false);

        // Apply order
        if ($order) {
            $columns = ['ID_PROGRAM', 'NAMA_PROGRAM', 'TAHUN', 'ANGGARAN', 'STATUS'];
            $colIndex = intval($order['column']);
            $dir = $order['dir'];
            if (isset($columns[$colIndex])) {
                $modelFiltered->orderBy($columns[$colIndex], $dir);
            }
        }

        // Ambil data untuk halaman ini
        if ($length > 0) {
            $dataList = $modelFiltered->findAll($length, $start);
        } else {
            $dataList = $modelFiltered->findAll();
        }

        // Format data untuk DataTables
        $data = [];
        foreach ($dataList as $i => $row) {
            $data[] = [
                'no' => $start + $i + 1,
                'ID_PROGRAM' => $row['ID_PROGRAM'],
                'NAMA_PROGRAM' => $row['NAMA_PROGRAM'],
                'TAHUN' => $row['TAHUN'],
                'ANGGARAN' => $row['ANGGARAN'],
                'STATUS' => $row['STATUS']
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
        $data = $this->programModel->find($id);
        return $this->response->setJSON($data);
    }

    // Simpan data baru
    public function save()
    {
        try {
            $data = [
                'NAMA_PROGRAM' => $this->request->getPost('NAMA_PROGRAM'),
                'TAHUN'        => $this->request->getPost('TAHUN'),
                'ANGGARAN'     => $this->request->getPost('ANGGARAN'),
            ];

            if ($this->programModel->insert($data) === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->programModel->errors()
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Program berhasil disimpan'
            ]);
        } catch (DatabaseException $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'code'    => $e->getCode()
            ]);
        }
    }

    // Update data
    public function update()
    {
        try {
            $id = $this->request->getPost('ID_PROGRAM');
            $data = [
                'NAMA_PROGRAM' => $this->request->getPost('NAMA_PROGRAM'),
                'TAHUN'        => $this->request->getPost('TAHUN'),
                'ANGGARAN'     => $this->request->getPost('ANGGARAN'),
            ];

            if ($this->programModel->update($id, $data) === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->programModel->errors()
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Program berhasil diupdate'
            ]);
        } catch (DatabaseException $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'code'    => $e->getCode()
            ]);
        }
    }

    // Hapus data
    public function delete($id)
    {
        try {
            $this->programModel->delete($id);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Program berhasil dihapus'
            ]);
        } catch (DatabaseException $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'code'    => $e->getCode()
            ]);
        }
    }
}