<?php 
namespace App\Controllers\Pengaturan;

use App\Controllers\BaseController;
use App\Models\Pengaturan\ProfilModel;

class Profil extends BaseController
{
    protected $modulModel;

    public function __construct()
    {
        $this->modulModel = new ProfilModel();
    }

    public function index()
    {
        $profil = $this->modulModel->find(1); // ambil data ID 1

        return view('pengaturan/profil/index', [
            'title' => '🕌 Profil Lembaga',
            'profil' => $profil
        ]);
    }
    public function update()
    {
        $model = new ProfilModel();
        $id = 1; // selalu update ID = 1

        $profilLama = $model->find($id);
        
        $data = [
            'NAMA_LENGKAP' => $this->request->getPost('nama_lengkap'),
            'NAMA_SINGKAT' => $this->request->getPost('nama_singkat'),
            'ALAMAT'       => $this->request->getPost('alamat'),
            'TELP'         => $this->request->getPost('telp'),
            'FAX'          => $this->request->getPost('fax'),
            'EMAIL'        => $this->request->getPost('email'),
        ];

        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            // Hapus file logo lama jika ada
            if (!empty($profilLama['LOGO']) && file_exists(WRITEPATH . '../public/' . $profilLama['LOGO'])) {
                unlink(WRITEPATH . '../public/' . $profilLama['LOGO']);
            }

            $newName = $logo->getRandomName();
            $logo->move('assets/img/', $newName);
            $data['LOGO'] = 'assets/img/' . $newName;
        }

        $success = $model->update($id, $data);

        refreshUserSession(session('user_id'));
        return $this->response->setJSON([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? 'Data berhasil diperbarui.' : 'Gagal memperbarui data.'
        ]);
    }

}

?>