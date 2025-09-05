<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class Api extends BaseController
{
    private $tokenAPI = '71qISoMM3VULwBsX0rMOIosgTkLF96A3j';

    // Helper untuk validasi token
    private function validateToken($token)
    {
        if ($token !== $this->tokenAPI) {
            return $this->respondJSON(false, 403, 'Akses ditolak. Token tidak valid.');
        }
        return true;
    }

    // Helper respon JSON
    private function respondJSON($success, $code, $message, $data = null)
    {
        return $this->response->setJSON([
            'success' => $success,
            'code'    => $code,
            'message' => $message,
            'data'    => $data
        ])->setStatusCode($code);
    }

    // Login
    public function login()
    {
        $token = $this->request->getVar('token');
        if ($this->validateToken($token) !== true) {
            return $this->validateToken($token);
        }

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $this->db->table('pengguna usr')
            ->select('usr.USERNAME as userid, usr.NAMA as namalengkap, usr.PASSWORD as password, usr.ACTIVE')
            ->select('pg.JABATAN_ID as idjabatan, jbt.JABATAN as jabatan')
            ->select('pg.NIP as niy, pg.EMAIL as email, pg.TELP as telp')
            ->select('usr.PEGAWAI_ID as pegawaiId')
            ->join('m_pegawai pg', 'pg.ID = usr.PEGAWAI_ID')
            ->join('m_pegawai_jabatan jbt', 'jbt.ID = pg.JABATAN_ID')
            ->where('usr.USERNAME', $username)
            ->get()
            ->getRowArray();

        // Jika user tidak ditemukan
        if (!$user) {
            return $this->respondJSON(false, 401, 'Username atau Password Anda salah');
        }

        // Jika akun nonaktif
        if ((int)$user['ACTIVE'] !== 1) {
            return $this->respondJSON(false, 401, 'Akun Anda tidak aktif, hubungi admin!');
        }

        // Verifikasi password (perhatikan key lowercase)
        if (!password_verify($password, $user['password'])) {
            return $this->respondJSON(false, 401, 'Username atau Password Anda salah');
        }

        // Hilangkan field sensitif
        unset($user['password'], $user['ACTIVE']);

        return $this->respondJSON(true, 200, 'Login Berhasil', $user);
    }


    // Submit Absensi (Datang / Pulang)
    public function submit_absensi()
    {
        $token = $this->request->getVar('token');

        // Validasi token
        if ($token !== $this->tokenAPI) {
            return $this->respondJSON(false, 403, 'Akses ditolak. Token tidak valid.');
        }

        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'pegawai_id'        => 'required',
            'tanggal'           => 'required',
            'hari'              => 'required',
            'jam_datang'        => 'required',
            'latitude'          => 'required|numeric',
            'longitude'         => 'required|numeric',
            'gedung_lat'        => 'required|numeric',
            'gedung_lng'        => 'required|numeric',
            'distance_in_meter' => 'required|numeric',
            'gedung_radius'     => 'required|numeric',
            'keterangan'        => 'required|in_list[Datang,Pulang]',
            'foto'              => 'uploaded[foto]|is_image[foto]|max_size[foto,30000]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->respondJSON(false, 400, 'Validasi gagal.', $validation->getErrors());
        }

        // Ambil input
        $pegawai_id        = $this->request->getVar('pegawai_id');
        $tanggal           = $this->request->getVar('tanggal');
        $hari              = $this->request->getVar('hari');
        $jamInput          = $this->request->getVar('jam_datang');
        $latitude          = $this->request->getVar('latitude');
        $longitude         = $this->request->getVar('longitude');
        $gedung_lat        = $this->request->getVar('gedung_lat');
        $gedung_lng        = $this->request->getVar('gedung_lng');
        $distanceInMeter   = (int)$this->request->getVar('distance_in_meter');
        $gedungRadius      = (int)$this->request->getVar('gedung_radius');
        $keterangan        = $this->request->getVar('keterangan'); // Datang / Pulang
        $foto              = $this->request->getFile('foto');

        // Cek jarak aman
        if ($distanceInMeter > $gedungRadius) {
            return $this->respondJSON(
                false,
                403,
                "Anda berada di luar radius absen ({$gedungRadius} m). Jarak Anda saat ini: {$distanceInMeter} m"
            );
        }

        // Cek apakah sudah absen dengan keterangan yang sama
        $absensi = $this->db->table('absensi')
            ->where('PEGAWAI_ID', $pegawai_id)
            ->where('TANGGAL', $tanggal)
            ->where('KETERANGAN', $keterangan)
            ->get()->getRowArray();

        if ($absensi) {
            $msg = ($keterangan === 'Datang')
                ? 'Anda sudah melakukan absen Datang hari ini.'
                : 'Anda sudah melakukan absen Pulang hari ini.';
            return $this->respondJSON(false, 200, $msg);
        }

        // Ambil jabatan pegawai
        $m_pegawai = $this->db->table('m_pegawai')
            ->select('JABATAN_ID')
            ->where('ID', $pegawai_id)
            ->get()->getRowArray();

        if (!$m_pegawai) {
            return $this->respondJSON(false, 404, 'Pegawai tidak ditemukan.');
        }

        $id_jabatan = $m_pegawai['JABATAN_ID'];

        // Ambil pengaturan absensi
        $absensi_pengaturan = $this->db->table('absensi_pengaturan')
            ->where('ID_JABATAN', $id_jabatan)
            ->where('HARI', $hari)
            ->where('STATUS', 1)
            ->get()->getRowArray();

        if (!$absensi_pengaturan) {
            return $this->respondJSON(false, 404, 'Tidak ada pengaturan absensi untuk hari ini.');
        }

        $jamDatangMaster = $absensi_pengaturan['DATANG'];
        $jamPulangMaster = $absensi_pengaturan['PULANG'];

        // Tentukan status absensi
        if ($keterangan === 'Datang') {
            $bukaAbsen = date('H:i:s', strtotime('-1 hour', strtotime($jamDatangMaster)));

            if (strtotime($jamInput) < strtotime($bukaAbsen)) {
                return $this->respondJSON(false, 422, 'Absensi Datang belum dibuka.');
            }

            if (strtotime($jamInput) > strtotime($jamPulangMaster)) {
                return $this->respondJSON(false, 422, 'Anda sudah melewati Jam Absen ' . $keterangan . ', dianggap tidak hadir.');
            }

            $statusAbsen = (strtotime($jamInput) <= strtotime($jamDatangMaster))
                ? 'TEPAT WAKTU'
                : 'TERLAMBAT';
        } else if ($keterangan === 'Pulang') {
            // Wajib sudah absen datang
            $absenDatang = $this->db->table('absensi')
                ->where('PEGAWAI_ID', $pegawai_id)
                ->where('TANGGAL', $tanggal)
                ->where('KETERANGAN', 'Datang')
                ->get()->getRowArray();

            if (!$absenDatang) {
                return $this->respondJSON(false, 422, 'Anda belum melakukan presensi Datang hari ini.');
            }

            $batasPulang = date('H:i:s', strtotime('+2 hour', strtotime($jamPulangMaster)));
            if (strtotime($jamInput) > strtotime($batasPulang)) {
                return $this->respondJSON(false, 422, 'Anda sudah melewati batas maksimal absensi Pulang.');
            }

            $statusAbsen = (strtotime($jamInput) < strtotime($jamPulangMaster))
                ? 'CEPAT PULANG'
                : 'TEPAT WAKTU';
        }

        // Simpan foto
        $namaFoto = $foto->getRandomName();
        $foto->move(FCPATH . 'foto_absen', $namaFoto);

        // Simpan absensi
        $data = [
            'PEGAWAI_ID' => $pegawai_id,
            'TANGGAL'    => $tanggal,
            'HARI'       => $hari,
            'JAM_MASTER' => ($keterangan === 'Datang') ? $jamDatangMaster : $jamPulangMaster,
            'JAM_DATANG' => $jamInput,
            'LATITUDE'   => $latitude,
            'LONGITUDE'  => $longitude,
            'GEDUNG_LAT' => $gedung_lat,
            'GEDUNG_LNG' => $gedung_lng,
            'JARAK'      => $distanceInMeter,
            'RADIUS'     => $gedungRadius,
            'FOTO'       => $namaFoto,
            'KETERANGAN' => $keterangan,
            'STATUS'     => $statusAbsen,
            'CREATE_AT'  => date('Y-m-d H:i:s')
        ];

        $this->db->table('absensi')->insert($data);

        return $this->respondJSON(true, 201, 'Absensi berhasil disimpan.', [
            'pegawai_id' => $pegawai_id,
            'keterangan' => $keterangan,
            'status'     => $statusAbsen,
            'jam'        => $jamInput
        ]);
    }

    public function update_profile()
    {
        $pegawaiId = $this->request->getVar('pegawai_id');
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

        $email = $this->request->getVar('email');
        $telp  = $this->request->getVar('telp');

        // Validasi input
        if (empty($email) && empty($telp)) {
            return $this->response->setJSON([
                'success' => false,
                'code'    => 400,
                'message' => 'Tidak ada data untuk diperbarui.',
                'data'    => null
            ])->setStatusCode(400);
        }

        $data = [];
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setJSON([
                    'success' => false,
                    'code'    => 400,
                    'message' => 'Email tidak valid.',
                    'data'    => null
                ])->setStatusCode(400);
            }
            $data['EMAIL'] = $email;
        }

        if (!empty($telp)) {
            $data['TELP'] = $telp;
        }

        try {
            $update = $this->db->table('m_pegawai')->where('ID', $pegawaiId)->update($data);

            if ($update) {
                // Ambil data terbaru
                $profile = $this->db->table('m_pegawai')->where('ID', $pegawaiId)->get()->getRowArray();

                return $this->response->setJSON([
                    'success' => true,
                    'code'    => 200,
                    'message' => 'Profil berhasil diperbarui.',
                    'data'    => $profile
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'code'    => 500,
                    'message' => 'Gagal memperbarui profil. Silakan coba lagi.',
                    'data'    => null
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'code'    => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data'    => null
            ]);
        }
    }

    public function change_password()
    {
        $pegawaiId = $this->request->getVar('pegawai_id');
        $token     = $this->request->getVar('token');
        $oldPass   = $this->request->getVar('old_password');
        $newPass   = $this->request->getVar('new_password');

        // Validasi token
        if ($token != '71qISoMM3VULwBsX0rMOIosgTkLF96A3j') {
            return $this->response->setJSON([
                'success' => false,
                'code'    => 403,
                'message' => 'Akses ditolak. Token tidak valid.',
                'data'    => null
            ])->setStatusCode(403);
        }

        // Cek input
        if (!$oldPass || !$newPass) {
            return $this->response->setJSON([
                'success' => false,
                'code'    => 400,
                'message' => 'Password lama dan baru wajib diisi.',
                'data'    => null
            ]);
        }

        try {
            // Ambil data pengguna
            $user = $this->db->table('pengguna')->where('PEGAWAI_ID', $pegawaiId)->get()->getRowArray();

            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'code'    => 404,
                    'message' => 'Pengguna tidak ditemukan.',
                    'data'    => null
                ]);
            }

            // Cek password lama
            if (!password_verify($oldPass, $user['PASSWORD'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'code'    => 401,
                    'message' => 'Password lama salah.',
                    'data'    => null
                ]);
            }

            // Hash password baru
            $hashNew = password_hash($newPass, PASSWORD_BCRYPT);

            // Update password
            $update = $this->db->table('pengguna')->where('PEGAWAI_ID', $pegawaiId)->update(['PASSWORD' => $hashNew]);

            if ($update) {
                return $this->response->setJSON([
                    'success' => true,
                    'code'    => 200,
                    'message' => 'Password berhasil diperbarui.',
                    'data'    => null
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'code'    => 500,
                    'message' => 'Gagal memperbarui password.',
                    'data'    => null
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'code'    => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data'    => null
            ]);
        }
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



    // Fungsi cek lokasi gedung
    private function checkLokasiGedung($lat, $long)
    {
        $gedungList = $this->db->table('m_r_gedung')->where('STATUS', 1)->get()->getResultArray();

        foreach ($gedungList as $gedung) {
            $jarak = $this->calculateDistance($lat, $long, $gedung['LATITUDE'], $gedung['LONGITUDE']);
            if ($jarak <= $gedung['MAXJARAK']) return true;
        }
        return false;
    }

    // Ambil pengaturan absensi
    private function getAbsensiPengaturan($pegawai_id, $hari)
    {
        $m_pegawai = $this->db->table('m_pegawai')->select('JABATAN_ID')->where('ID', $pegawai_id)->get()->getRowArray();
        if (!$m_pegawai) return null;

        return $this->db->table('absensi_pengaturan')
            ->where('ID_JABATAN', $m_pegawai['JABATAN_ID'])
            ->where('HARI', $hari)
            ->where('STATUS', 1)
            ->get()
            ->getRowArray();
    }

    // Fungsi hitung jarak Haversine
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;
        $a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
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

    // Di dalam class Api

    public function get_tupoksi()
    {
        $token = $this->request->getVar('token');

        // Validasi token
        if ($token !== $this->tokenAPI) {
            return $this->respondJSON(false, 403, 'Akses ditolak. Token tidak valid.');
        }

        // Ambil parameter opsional: id_jabatan
        $id_jabatan = $this->request->getVar('id_jabatan');

        $builder = $this->db->table('m_pegawai_tupoksi')->select('*')->where('STATUS', 1);

        if ($id_jabatan) {
            $builder->where('ID_JABATAN', $id_jabatan);
        }

        $tupoksi = $builder->orderBy('ID', 'ASC')->get()->getResultArray();

        return $this->respondJSON(true, 200, 'Data Tupoksi ditemukan.', $tupoksi);
    }

    // sub kinerja pegawai
    public function submit_kinerja()
    {
        $token = $this->request->getVar('token');

        // Validasi token
        if ($token !== $this->tokenAPI) {
            return $this->respondJSON(false, 403, 'Akses ditolak. Token tidak valid.');
        }

        // Validasi input (foto tidak wajib)
        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_pegawai' => 'required|numeric',
            'id_tupoksi' => 'required|numeric',
            'tanggal'    => 'required|valid_date[Y-m-d]',
            'keterangan' => 'required',
            'foto'       => 'if_exist|is_image[foto]|max_size[foto,30000]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->respondJSON(false, 400, 'Validasi gagal.', $validation->getErrors());
        }

        $id_pegawai = $this->request->getVar('id_pegawai');
        $id_tupoksi = $this->request->getVar('id_tupoksi');
        $tanggal    = $this->request->getVar('tanggal');
        $keterangan = $this->request->getVar('keterangan');
        $fotoFile   = $this->request->getFile('foto');

        // Default foto = noimage.jpg
        $fileName = 'noimage.jpg';

        // Jika ada file yang diupload dan valid
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $uploadPath = FCPATH . 'upload_kegiatan';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Penamaan file unik: IDPEGAWAI_YYYYMMDD_HHMMSS.jpg
            $timePart = date('His');
            $extension = $fotoFile->getExtension();
            $fileName = $id_pegawai . '_' . str_replace('-', '', $tanggal) . '_' . $timePart . '.' . $extension;

            // Upload file
            if (!$fotoFile->move($uploadPath, $fileName)) {
                return $this->respondJSON(false, 500, 'Gagal mengupload foto.');
            }
        }

        // Data untuk disimpan
        $data = [
            'ID'          => uniqid(),
            'ID_TUPOKSI'  => $id_tupoksi,
            'ID_PEGAWAI'  => $id_pegawai,
            'TANGGAL'     => $tanggal . ' ' . date('H:i:s'),
            'KETERANGAN'  => $keterangan,
            'FOTO'        => $fileName,
            'CREATE_AT'   => date('Y-m-d H:i:s')
        ];

        try {
            $this->db->table('m_pegawai_kinerja')->insert($data);
            return $this->respondJSON(true, 201, 'Kinerja berhasil disimpan.', $data);
        } catch (\Exception $e) {
            // Hapus file jika gagal simpan DB
            if ($fileName !== 'noimage.jpg' && file_exists($uploadPath . '/' . $fileName)) {
                unlink($uploadPath . '/' . $fileName);
            }
            return $this->respondJSON(false, 500, 'Gagal menyimpan data kinerja: ' . $e->getMessage());
        }
    }


    public function get_kinerja()
    {
        $token = $this->request->getVar('token');
        $pegawai_id = $this->request->getVar('id_pegawai');
        $bulan = $this->request->getVar('bulan');   // 1-12
        $tahun = $this->request->getVar('tahun');   // 4 digit, misal 2025

        // Validasi token
        if ($token !== $this->tokenAPI) {
            return $this->respondJSON(false, 403, 'Akses ditolak. Token tidak valid.');
        }

        if (!$pegawai_id) {
            return $this->respondJSON(false, 400, 'Parameter id_pegawai wajib diisi.');
        }

        $builder = $this->db->table('m_pegawai_kinerja')->select('*')
            ->where('ID_PEGAWAI', $pegawai_id);

        if ($bulan) {
            $builder->where('MONTH(TANGGAL)', $bulan);
        }

        if ($tahun) {
            $builder->where('YEAR(TANGGAL)', $tahun);
        }

        $kinerjaList = $builder->orderBy('TANGGAL', 'DESC')->get()->getResultArray();

        // Tambahkan URL foto jika ada
        foreach ($kinerjaList as &$item) {
            if (!empty($item['FOTO'])) {
                $item['FOTO_URL'] = base_url('upload_kegiatan/' . $item['FOTO']);
            } else {
                $item['FOTO_URL'] = null;
            }
        }

        return $this->respondJSON(true, 200, 'Data kinerja ditemukan.', $kinerjaList);
    }

    public function get_kinerja_by_tupoksi()
    {
        $token      = $this->request->getVar('token');
        $pegawai_id = $this->request->getVar('id_pegawai');
        $tupoksi_id = $this->request->getVar('id_tupoksi');
        $tanggal    = $this->request->getVar('tanggal'); // format: YYYY-MM-DD



        // Validasi token
        if ($token !== $this->tokenAPI) {
            return $this->respondJSON(false, 403, 'Akses ditolak. Token tidak valid.');
        }

        // Validasi parameter wajib
        if (!$pegawai_id || !$tupoksi_id) {
            return $this->respondJSON(false, 400, 'Parameter id_pegawai dan id_tupoksi wajib diisi.');
        }

        $builder = $this->db->table('m_pegawai_kinerja')
            ->where('ID_PEGAWAI', $pegawai_id)
            ->where('ID_TUPOKSI', $tupoksi_id);

        // Filter opsional berdasarkan tanggal
        if (!empty($tanggal)) {
            $builder->where('TANGGAL >=', $tanggal . ' 00:00:00');
            $builder->where('TANGGAL <=', $tanggal . ' 23:59:59');
        }

        $builder->orderBy('TANGGAL', 'DESC');
        $kinerja = $builder->get()->getResultArray();

        // Tambahkan URL foto atau default
        foreach ($kinerja as &$item) {
            if (!empty($item['FOTO'])) {
                $item['FOTO_URL'] = base_url('upload_kegiatan/' . $item['FOTO']);
            } else {
                $item['FOTO_URL'] = base_url('upload_kegiatan/noimage.jpg');
            }
        }

        $response = [
            'total' => count($kinerja),
            'data'  => $kinerja
        ];

        return $this->respondJSON(true, 200, 'Data kinerja ditemukan.', $response);
    }


    public function get_kinerja_bulanan()
    {
        $token = $this->request->getVar('token');
        $pegawai_id = $this->request->getVar('id_pegawai');
        $bulan = $this->request->getVar('bulan');
        $tahun = $this->request->getVar('tahun');

        // Validasi token
        if ($token !== $this->tokenAPI) {
            return $this->respondJSON(false, 403, 'Akses ditolak. Token tidak valid.');
        }

        if (!$pegawai_id || !$bulan || !$tahun) {
            return $this->respondJSON(false, 400, 'Parameter id_pegawai, bulan, dan tahun wajib diisi.');
        }

        try {
            $builder = $this->db->table('m_pegawai_kinerja');
            $builder->select('m_pegawai_kinerja.*, m_pegawai_tupoksi.URAIAN_TUPOKSI AS TUPOKSI');
            $builder->join('m_pegawai_tupoksi', 'm_pegawai_tupoksi.ID = m_pegawai_kinerja.ID_TUPOKSI', 'left');
            $builder->where('ID_PEGAWAI', $pegawai_id);
            $builder->where('MONTH(TANGGAL) =', $bulan);
            $builder->where('YEAR(TANGGAL) =', $tahun);
            $builder->orderBy('TANGGAL', 'DESC');

            $kinerja = $builder->get()->getResultArray();



            // Tambahkan URL foto
            foreach ($kinerja as &$item) {
                if (!empty($item['FOTO'])) {
                    $item['FOTO_URL'] = base_url('upload_kegiatan/' . $item['FOTO']);
                } else {
                    $item['FOTO_URL'] = null; // biarkan kosong, Flutter yang handle default icon
                }
            }


            return $this->respondJSON(true, 200, 'Data kinerja bulanan ditemukan.', $kinerja);
        } catch (\Exception $e) {
            return $this->respondJSON(false, 500, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete_kinerja()
    {
        $token = $this->request->getVar('token');
        $id_kinerja = $this->request->getVar('id_kinerja');

        // Validasi token
        if ($token !== $this->tokenAPI) {
            return $this->respondJSON(false, 403, 'Akses ditolak. Token tidak valid.');
        }

        if (!$id_kinerja) {
            return $this->respondJSON(false, 400, 'Parameter id_kinerja wajib diisi.');
        }

        try {
            // Ambil data kinerja dulu
            $builder = $this->db->table('m_pegawai_kinerja');
            $kinerja = $builder->where('ID', $id_kinerja)->get()->getRowArray();

            if (!$kinerja) {
                return $this->respondJSON(false, 404, 'Data kinerja tidak ditemukan.');
            }

            // Hapus data dari database
            $deleted = $builder->delete(['ID' => $id_kinerja]);

            if ($deleted) {
                // Hapus file kalau ada
                if (!empty($kinerja['FOTO']) && $kinerja['FOTO'] !== 'noimage.jpg') {
                    $filePath = FCPATH . 'upload_kegiatan/' . $kinerja['FOTO'];
                    if (file_exists($filePath)) {
                        if (!@unlink($filePath)) {
                            log_message('error', "Gagal menghapus file: $filePath");
                        }
                    } else {
                        log_message('error', "File tidak ditemukan: $filePath");
                    }
                }

                return $this->respondJSON(true, 200, 'Kinerja dan foto berhasil dihapus.');
            }

            if ($deleted) {
                return $this->respondJSON(true, 200, 'Kinerja dan foto berhasil dihapus.');
            } else {
                return $this->respondJSON(false, 500, 'Gagal menghapus kinerja.');
            }
        } catch (\Exception $e) {
            return $this->respondJSON(false, 500, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
