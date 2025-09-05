<?php 
namespace App\Controllers\Api;

use App\Controllers\BaseController;

class Api extends BaseController
{
    
    public function login()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $token = $this->request->getVar('token');
        if ($token != '71qISoMM3VULwBsX0rMOIosgTkLF96A3j') {
            $response = [
                'success' => false,
                'code'    => 403,
                'message' => 'Akses ditolak. Token tidak valid.',
                'data'    => null
            ];
            
            return $this->response->setJSON($response)->setStatusCode(403);
        }
    
        // $user = $this->db->table('pengguna usr')
        //     ->select('usr.PEGAWAI_ID, usr.NAMA, usr.USERNAME, usr.PASSWORD, usr.NIK, usr.ACTIVE')
        //     ->select('pg.NIP, pg.TEMPAT_LAHIR, pg.TANGGAL_LAHIR, pg.JENIS_KELAMIN')
        //     ->select('pg.TMT_SK, pg.TM_SK, pg.AKTIF AS STATUS_PEGAWAI')
        //     ->select('pg.AGAMA_ID, agm.AGAMA, pg.PENDIDIKAN_ID, pdk.PENDIDIKAN, pg.JABATAN_ID, jbt.JABATAN, pg.PROFESI_ID, prf.PROFESI, pg.JENIS_PEGAWAI_ID, jnp.JENIS_PEGAWAI')
        //     ->join('m_pegawai pg', 'pg.ID = usr.PEGAWAI_ID')
        //     ->join('m_agama agm', 'agm.ID = pg.AGAMA_ID')
        //     ->join('m_pendidikan pdk', 'pdk.ID = pg.PENDIDIKAN_ID')
        //     ->join('m_pegawai_jabatan jbt', 'jbt.ID = pg.JABATAN_ID')
        //     ->join('m_pegawai_profesi prf', 'prf.ID = pg.PROFESI_ID')
        //     ->join('m_pegawai_jenis jnp', 'jnp.ID = pg.JENIS_PEGAWAI_ID')
        //     ->where('usr.USERNAME', $username)
        //     ->get()
        //     ->getRowArray();

        $user = $this->db->table('pengguna usr')
            ->select('usr.USERNAME as userid, usr.NAMA as namalengkap, usr.PASSWORD as password')
            ->select('pg.JABATAN_ID as idjabatan, jbt.JABATAN as jabatan')
            ->select('pg.NIP as niy')
            ->select('pg.EMAIL as email, pg.TELP as telp')
            ->select('usr.PEGAWAI_ID as pegawaiId')
            ->join('m_pegawai pg', 'pg.ID = usr.PEGAWAI_ID')
            ->join('m_agama agm', 'agm.ID = pg.AGAMA_ID')
            ->join('m_pendidikan pdk', 'pdk.ID = pg.PENDIDIKAN_ID')
            ->join('m_pegawai_jabatan jbt', 'jbt.ID = pg.JABATAN_ID')
            ->join('m_pegawai_profesi prf', 'prf.ID = pg.PROFESI_ID')
            ->join('m_pegawai_jenis jnp', 'jnp.ID = pg.JENIS_PEGAWAI_ID')
            ->where('usr.USERNAME', $username)
            ->get()
            ->getRowArray();
            
        if ($user) {
            // if ($user['ACTIVE'] == 1) {
                if (password_verify($password, $user['password'])) {
                    // Hapus password dari respons
                    unset($user['PASSWORD']);
    
                    $msg = [
                        'success' => true,
                        'code' => 200,
                        'message' => 'Login Berhasil',
                        'data' => $user
                    ];
                } else {
                    $msg = [
                        'success' => false,
                        'code' => 401,
                        'message' => 'Username atau Password Anda salah',
                        'data' => null
                    ];
                }
            // } else {
            //     $msg = [
            //         'success' => false,
            //         'code' => 401,
            //         'message' => 'Akun Anda tidak aktif, silakan hubungi admin!',
            //         'data' => null
            //     ];
            // }
        } else {
            $msg = [
                'success' => false,
                'code' => 401,
                'message' => 'Username atau Password Anda salah',
                'data' => null
            ];
        }
    
        return $this->response->setJSON($msg);
    }

    
    public function update_profile()
    {
        $pegawaiId = $this->request->getVar('pegawai_id');
        
        $token = $this->request->getVar('token');
        if ($token != '71qISoMM3VULwBsX0rMOIosgTkLF96A3j') {
            $response = [
                'success' => false,
                'code'    => 403,
                'message' => 'Akses ditolak. Token tidak valid.',
                'data'    => null
            ];
            
            return $this->response->setJSON($response)->setStatusCode(403);
        }
        
        $data = [
            'NAMA'             => $this->request->getVar('nama'),
            'TEMPAT_LAHIR'     => $this->request->getVar('tempat_lahir'),
            'TANGGAL_LAHIR'    => $this->request->getVar('tanggal_lahir'),
            'JENIS_KELAMIN'    => $this->request->getVar('jenis_kelamin'),
            'AGAMA_ID'         => $this->request->getVar('agama_id'),
            'PENDIDIKAN_ID'    => $this->request->getVar('pendidikan_id'),
            'JABATAN_ID'       => $this->request->getVar('jabatan_id'),
            'PROFESI_ID'       => $this->request->getVar('profesi_id'),
            'JENIS_PEGAWAI_ID' => $this->request->getVar('jenis_pegawai_id'),
            'EMAIL'            => $this->request->getVar('email'),
            'TELP'             => $this->request->getVar('telp')
        ];
    
        try {
            $update = $this->db->table('m_pegawai')->where('ID', $pegawaiId)->update($data);
    
            if ($update) {
                $msg = [
                    'success' => true,
                    'code' => 200,
                    'message' => 'Profil berhasil diperbarui.',
                    'data'    => null
                ];
            } else {
                $msg = [
                    'success' => false,
                    'code' => 500,
                    'message' => 'Gagal memperbarui profil. Silakan coba lagi.',
                    'data'    => null
                ];
            }
        } catch (\Exception $e) {
            $msg = [
                'success' => false,
                'code' => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data'    => null
            ];
        }
    
        return $this->response->setJSON($msg);
    }

    
    // public function submit_absensi()
    // {
    //     $token = $this->request->getVar('token');
    
    //     // Validasi token statis
    //     if ($token != '71qISoMM3VULwBsX0rMOIosgTkLF96A3j') {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'code'    => 403,
    //             'message' => 'Akses ditolak. Token tidak valid.',
    //             'data'    => null
    //         ])->setStatusCode(403);
    //     }
    
    //     // Validasi sederhana: hanya wajib diisi
    //     $validation = \Config\Services::validation();
    //     $validation->setRules([
    //         'pegawai_id' => 'required',
    //         'tanggal'    => 'required',
    //         'hari'       => 'required',
    //         'jam_master' => 'required',
    //         'jam_datang' => 'required',
    //         'latitude'   => 'required',
    //         'longitude'  => 'required',
    //         'foto'       => 'required',
    //         'keterangan' => 'permit_empty', // opsional
    //     ]);
    
    //     if (!$validation->withRequest($this->request)->run()) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'code'    => 400,
    //             'message' => 'Validasi gagal.',
    //             'errors'  => $validation->getErrors()
    //         ])->setStatusCode(400);
    //     }
    
    //     $data = [
    //         'PEGAWAI_ID'  => $this->request->getVar('pegawai_id'),
    //         'TANGGAL'     => $this->request->getVar('tanggal'),
    //         'HARI'        => $this->request->getVar('hari'),
    //         'JAM_MASTER'  => $this->request->getVar('jam_master'),
    //         'JAM_DATANG'  => $this->request->getVar('jam_datang'),
    //         'LATITUDE'    => $this->request->getVar('latitude'),
    //         'LONGITUDE'   => $this->request->getVar('longitude'),
    //         'FOTO'        => $this->request->getVar('foto'),
    //         'KETERANGAN'  => $this->request->getVar('keterangan'),
    //         'CREATE_AT'   => date('Y-m-d H:i:s')
    //     ];
    
    //     $this->db->table('absensi')->insert($data);
    
    //     return $this->response->setJSON([
    //         'success' => true,
    //         'code'    => 201,
    //         'message' => 'Absensi berhasil disimpan.',
    //         'data'    => null
    //     ])->setStatusCode(201);
    // }
    
    
    public function submit_absensi()
    {
        $token = $this->request->getVar('token');
    
        // Validasi token
        if ($token !== '71qISoMM3VULwBsX0rMOIosgTkLF96A3j') {
            return $this->response->setJSON([
                'success' => false,
                'code'    => 403,
                'message' => 'Akses ditolak. Token tidak valid.',
                'data'    => null
            ])->setStatusCode(403);
        }
    
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'pegawai_id' => 'required',
            'tanggal'    => 'required',
            'hari'       => 'required',
            'jam_master' => 'required',
            'jam_datang' => 'required',
            'latitude'   => 'required',
            'longitude'  => 'required',
            'foto'       => 'uploaded[foto]|is_image[foto]|max_size[foto,2048]', // 2MB
            'keterangan' => 'permit_empty',
        ]);
    
        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'code'    => 400,
                'message' => 'Validasi gagal.',
                'errors'  => $validation->getErrors()
            ])->setStatusCode(400);
        }
    
        // Proses upload foto
        $foto = $this->request->getFile('foto');
        $namaFoto = $foto->getRandomName(); // nama acak
        $foto->move(FCPATH . 'foto_absen', $namaFoto); // simpan ke folder
    
        // Data untuk disimpan
        $data = [
            'PEGAWAI_ID'  => $this->request->getVar('pegawai_id'),
            'TANGGAL'     => $this->request->getVar('tanggal'),
            'HARI'        => $this->request->getVar('hari'),
            'JAM_MASTER'  => $this->request->getVar('jam_master'),
            'JAM_DATANG'  => $this->request->getVar('jam_datang'),
            'LATITUDE'    => $this->request->getVar('latitude'),
            'LONGITUDE'   => $this->request->getVar('longitude'),
            'FOTO'        => $namaFoto,
            'KETERANGAN'  => $this->request->getVar('keterangan'),
            'CREATE_AT'   => date('Y-m-d H:i:s')
        ];
    
        $this->db->table('absensi')->insert($data);
    
        return $this->response->setJSON([
            'success' => true,
            'code'    => 201,
            'message' => 'Absensi berhasil disimpan.',
            'data'    => null
        ])->setStatusCode(201);
    }




    // public function get_absen()
    // {
    //     $token = $this->request->getVar('token');
    
    //     if ($token != '71qISoMM3VULwBsX0rMOIosgTkLF96A3j') {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'code'    => 403,
    //             'message' => 'Akses ditolak. Token tidak valid.',
    //             'data'    => null
    //         ])->setStatusCode(403);
    //     }
    
    //     $pegawai_id = $this->request->getVar('pegawai_id');
    //     $tanggal    = $this->request->getVar('tanggal');
    
    //     $builder = $this->db->table('absensi')
    //         ->select('*');
    
    //     if ($pegawai_id) {
    //         $builder->where('PEGAWAI_ID', $pegawai_id);
    //     }
    
    //     if ($tanggal) {
    //         $builder->where('TANGGAL', $tanggal);
    //     }
    
    //     $data = $builder->orderBy('CREATE_AT', 'DESC')->get()->getResultArray();
    
    //     $msg = [
    //         'success' => true,
    //         'code'    => 200,
    //         'message' => 'Data absensi ditemukan.',
    //         'data'    => $data
    //     ];
    
    //     return $this->response->setJSON($msg);
    // }
    
    public function get_absen()
    {
        $token = $this->request->getVar('token');
    
        if ($token != '71qISoMM3VULwBsX0rMOIosgTkLF96A3j') {
            return $this->response->setJSON([
                'success' => false,
                'code'    => 403,
                'message' => 'Akses ditolak. Token tidak valid.',
                'data'    => null
            ])->setStatusCode(403);
        }
    
        $pegawai_id = $this->request->getVar('pegawai_id');
        $tanggal    = $this->request->getVar('tanggal');
    
        $builder = $this->db->table('absensi')->select('*');
    
        if ($pegawai_id) {
            $builder->where('PEGAWAI_ID', $pegawai_id);
        }
    
        if ($tanggal) {
            $builder->where('TANGGAL', $tanggal);
        }
    
        $query = $builder->orderBy('CREATE_AT', 'DESC')->get()->getResultArray();
    
        // Tambahkan base_url pada kolom FOTO
        foreach ($query as &$item) {
            if (!empty($item['FOTO'])) {
                $item['FOTO_URL'] = base_url('foto_absen/' . $item['FOTO']);
            } else {
                $item['FOTO_URL'] = null;
            }
        }
    
        return $this->response->setJSON([
            'success' => true,
            'code'    => 200,
            'message' => 'Data absensi ditemukan.',
            'data'    => $query
        ]);
    }


    
    public function get_data_agama()
    {
        $result = $this->db->table('m_agama')
            ->where('STATUS', '1')
            ->get()
            ->getResultArray();
    
        $msg = [
            'success' => true,
            'code' => 200,
            'message' => 'Data Agama',
            'data' => $result
        ];
    
        return $this->response->setJSON($msg);
    }

    public function get_data_pendidikan()
    {
        $result = $this->db->table('m_pendidikan')
            ->where('STATUS', '1')
            ->get()
            ->getResultArray();
    
        $msg = [
            'success' => true,
            'code' => 200,
            'message' => 'Data Pendidikan',
            'data' => $result
        ];
    
        return $this->response->setJSON($msg);
    }

    public function get_data_jabatan()
    {
        $result = $this->db->table('m_pegawai_jabatan')
            ->where('STATUS', '1')
            ->get()
            ->getResultArray();
    
        $msg = [
            'success' => true,
            'code' => 200,
            'message' => 'Data Jabatan',
            'data' => $result
        ];
    
        return $this->response->setJSON($msg);
    }

    public function get_data_profesi()
    {
        $result = $this->db->table('m_pegawai_profesi')
            ->where('STATUS', '1')
            ->get()
            ->getResultArray();
    
        $msg = [
            'success' => true,
            'code' => 200,
            'message' => 'Data Profesi',
            'data' => $result
        ];
    
        return $this->response->setJSON($msg);
    }

    public function get_data_jenis_pegawai()
    {
        $result = $this->db->table('m_pegawai_jenis')
            ->where('STATUS', '1')
            ->get()
            ->getResultArray();
    
        $msg = [
            'success' => true,
            'code' => 200,
            'message' => 'Data Jenis Pegawai',
            'data' => $result
        ];
    
        return $this->response->setJSON($msg);
    }

    
    public function get_data_gedung()
    {
        $result = $this->db->table('m_r_gedung')
        ->where('status', '1')
        ->get()
        ->getResultArray();
        
        $msg = [
            'success' => true,
            'code' => 200,
            'message' => 'Data Gedung',
            'data' => $result
        ];
        
        return $this->response->setJSON($msg);
    }

    public function get_data_lembaga()
    {
        $result = $this->db->table('pengaturan_profil')
        ->get()
        ->getRowArray();
        $result['LINK_LOGO'] = base_url() . '/' . $result['LOGO'];
        
        $msg = [
            'success' => true,
            'code' => 200,
            'message' => 'Data Lemabaga',
            'data' => $result
        ];
        
        return $this->response->setJSON($msg);
    }
    
    public function get_data_absen_pengaturan()
    {
        $token = $this->request->getVar('token');
        if ($token !== '71qISoMM3VULwBsX0rMOIosgTkLF96A3j') {
            return $this->response->setJSON([
                'success' => false,
                'code'    => 403,
                'message' => 'Akses ditolak. Token tidak valid.',
                'data'    => null
            ])->setStatusCode(403);
        }
    
        $id_jabatan = $this->request->getVar('id_jabatan');
        $hari       = date('l'); // default: "Monday", "Tuesday", etc.
    
        // Ubah hari jadi format Bahasa Indonesia seperti di DB
        $hari_indo = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
    
        $hari_db = $hari_indo[$hari] ?? $hari;
        
        echo $hari_db;
    
        $data = $this->db->table('absensi_pengaturan') // ganti jika nama tabel beda
            ->where('ID_JABATAN', $id_jabatan)
            ->where('HARI', $hari_db)
            ->where('STATUS', 1)
            ->get()
            ->getRowArray();
    
        if ($data) {
            return $this->response->setJSON([
                'success' => true,
                'code'    => 200,
                'message' => 'Pengaturan absen ditemukan.',
                'data'    => $data
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'code'    => 404,
                'message' => 'Tidak ada pengaturan absen untuk hari ini.',
                'data'    => null
            ])->setStatusCode(404);
        }
    }


}