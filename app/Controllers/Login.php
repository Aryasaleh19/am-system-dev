<?php

namespace App\Controllers;

use App\Models\Kepegawaian\ModelPengguna;
use CodeIgniter\Controller;

class Login extends Controller
{
    public function index()
    {
        if (session()->get('login')) {
            return redirect()->to('/dashboard'); 
        }

        return view('login');
    }


    public function proses()
{
    $session = session();
    $request = service('request');

    $username = $request->getPost('email-username');
    $password = $request->getPost('password');
    $captcha  = $request->getPost('captcha');

    $captcha_session = $session->get('captcha_login');

    if ((int)$captcha !== (int)$captcha_session) {
        return redirect()->to(base_url('login'))->with('error', 'Jawaban captcha salah!');
    }

    $model = new ModelPengguna();
    $user = $model->where('USERNAME', $username)->first();

    if ($user) {
        if (password_verify($password, $user['PASSWORD'])) {

            // Ambil info jenis kelamin dan jabatan aktif dari tabel m_pegawai JOIN m_pegawai_jabatan
            $pegawai = $model->db->table('m_pegawai p')
                        ->select('p.JENIS_KELAMIN, j.JABATAN')
                        ->join('m_pegawai_jabatan j', 'p.JABATAN_ID = j.ID', 'left')
                        ->where('p.ID', $user['PEGAWAI_ID'])
                        ->get()
                        ->getRowArray();

            $session->set([
                'user_id'       => $user['PEGAWAI_ID'],
                'nama'          => $user['NAMA'],
                'login'         => true,
                'jenis_kelamin' => $pegawai['JENIS_KELAMIN'] ?? 'L', // default laki-laki
                'jabatan'       => $pegawai['JABATAN'] ?? 'Super Admin' // default role
            ]);

            return redirect()->to('/dashboard'); 
        } else {
            return redirect()->to(base_url('login'))->with('error', 'Password salah');
        }
    } else {
        return redirect()->to(base_url('login'))->with('error', 'Username tidak ditemukan');
    }
}



    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}