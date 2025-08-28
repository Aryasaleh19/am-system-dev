<?php 
namespace App\Controllers\Kesiswaan;

use App\Controllers\BaseController;
use App\Models\Kesiswaan\ModelSiswa;
use App\Models\Kesiswaan\ModelSiswaSekolah;
use App\Models\Kesiswaan\ModelPrestasiKelas;
use App\Models\Kesiswaan\ModelAngkatan;
use App\Models\Referensi\ModelJenisPenerimaan;
use App\Models\Referensi\ModelMapingJenisPembayaran;

class Siswa extends BaseController
{
    protected $modelSiswa;
    protected $modelAngkatan;
    protected $modelJenisPenerimaan;
    protected $modelMapingJenisPembayaran;
    protected $modelSiswaSekolah;
    protected $modelPrestasiKelas;

    public function __construct()
    {
        $this->modelSiswa = new ModelSiswa();
        $this->modelAngkatan = new ModelAngkatan();
        $this->modelJenisPenerimaan = new ModelJenisPenerimaan();
        $this->modelMapingJenisPembayaran = new ModelMapingJenisPembayaran();
        $this->modelSiswaSekolah = new ModelSiswaSekolah();
        $this->modelPrestasiKelas = new ModelPrestasiKelas();
    }

    public function index()
    {
        return view('kesiswaan/siswa/index', [
            'title' => '👨 Data Siswa'
        ]);
    }

    public function formsiswabaru()
    {
        return view('kesiswaan/siswa/modals_form_siswa_baru');
    }

    public function ajaxList()
    {
        if ($this->request->isAJAX()) {
             $from = $this->request->getGet('from');
            $to = $this->request->getGet('to');
            $sekolah = $this->request->getGet('sekolah');  // Ambil filter sekolah

            $builder = $this->modelSiswa
                ->select('siswa.NIS, siswa.NAMA, siswa.ANGKATAN_ID, siswa.STATUS, angkatan.ANGKATAN, siswa_riwayat_sekolah.SEKOLAH_ID, m_sekolah.NAMA_SEKOLAH, siswa_riwayat_sekolah.NIS_NEW')
                ->join('siswa_angkatan angkatan', 'angkatan.ID = siswa.ANGKATAN_ID', 'left')
                ->join('siswa_riwayat_sekolah', 'siswa_riwayat_sekolah.NIS = siswa.NIS AND siswa_riwayat_sekolah.STATUS = 1', 'left')
                ->join('m_sekolah', 'm_sekolah.ID = siswa_riwayat_sekolah.SEKOLAH_ID', 'left');

            if (!empty($from) && !empty($to)) {
                $builder->where('siswa.ANGKATAN_ID >=', $from);
                $builder->where('siswa.ANGKATAN_ID <=', $to);
            }

            if (!empty($sekolah)) {
                $builder->where('siswa_riwayat_sekolah.SEKOLAH_ID', $sekolah);
            }

            $siswa = $builder->findAll();

            $data = [];
            $no = 1;

            foreach ($siswa as $row) {
                $data[] = [
                    'no' => $no++,
                    'nis' => $row['NIS'],
                    'nis_new' => $row['NIS_NEW'],
                    'nama' => $row['NAMA'],
                    'angkatan' => $row['ANGKATAN'],
                    'nama_sekolah' => $row['NAMA_SEKOLAH'],
                    'status' => $row['STATUS'] == 1 ? 'Aktif' : 'Tidak Aktif',
                    'aksi' => '
                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                        <button class="btn btn-outline-info btn-sm" onclick="detailSiswa(\'' . $row['NIS'] . '\')">🛠️ Pengaturan</button>
                        <button class="btn btn-outline-danger btn-sm" onclick="hapus(\'' . $row['NIS'] . '\')">🗑️ Hapus</button>
                    </div>'
                ];
            }

            return $this->response->setJSON(['data' => $data]);
        }
    }

    public function modaldetailsiswa()
    {
        return view('kesiswaan/siswa/modals_detail_siswa');
    }
    public function detailProfile()
    {
        $nis = $this->request->getGet('nis');
        $siswa = $this->modelSiswa->find($nis);

        if (!$siswa) {
            return 'Data siswa tidak ditemukan.';
        }

        return view('kesiswaan/siswa/detail/profile', ['siswa' => $siswa]);
    }

    public function detailBerkas()
    {
        return view('kesiswaan/siswa/detail/berkas');
    }

    public function detailOrangtua()
    {
        return view('kesiswaan/siswa/detail/orangtua');
    }

    


    public function pendaftaran()
    {
        $nis = $this->request->getGet('nis');
        $siswa = $this->modelSiswa->find($nis);
        if (!$siswa) {
            return 'Data siswa tidak ditemukan.';
        }
        $riwayat_sekolah = $this->modelSiswa->getRiwayatSekolah($nis); // Ambil riwayat sekolah siswa

        if (!$siswa) {
            return 'Data siswa tidak ditemukan.';
        }
        $data = [
            'siswa' => $siswa,
            'riwayat_sekolah' => $riwayat_sekolah
        ];
        return view('kesiswaan/siswa/detail/pendaftaran', $data);
    }
    public function prestasi()
    {
        $nis = $this->request->getGet('nis');

        // Ambil data siswa berdasarkan NIS
        $siswa = $this->modelSiswa
                    ->where('NIS', $nis)
                    ->first();

        if (!$siswa) {
            return 'Data siswa tidak ditemukan.';
        }

        // Ambil sekolah aktif langsung dari ModelSiswa
        $sekolahAktif = $this->modelSiswa->getRiwayatSekolahAktif($nis);

        
        $data = [
            'siswa' => $siswa,
            'sekolah' => $sekolahAktif ? $sekolahAktif[0] : null
        ];

        return view('kesiswaan/siswa/detail/prestasi', $data);
    }
    public function savePrestasi()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ]);
        }

        $nis   = $this->request->getPost('NIS');
        $idRiwayatSekolah = $this->request->getPost('idRiwayatSekolah');
        $ruanganId = $this->request->getPost('id_ruangan');
        $kelas = $this->request->getPost('no_kelas');
        $tmt   = $this->request->getPost('tanggal');
        $status   = $this->request->getPost('status');

        // Validasi sederhana
        if (!$nis || !$idRiwayatSekolah || !$ruanganId || !$kelas || !$tmt || !$status) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Semua field wajib diisi'
            ]);
        }

        // Data yang akan disimpan
        $data = [
            'NIS'               => $nis,
            'ID_RIWAYAT_SEKOLAH'=> $idRiwayatSekolah,
            'RUANGAN_ID'        => $ruanganId,
            'KELAS'             => $kelas,
            'TMT'               => $tmt,
            'OLEH'              => session()->get('user_id'), // ambil dari session login
            'STATUS'            => 1
        ];

        try {
            $this->modelPrestasiKelas->insert($data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data prestasi kelas berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }
    public function updatePrestasi()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ]);
        }

        $idPrestasi = $this->request->getPost('id_prestasi'); // id data yang mau diupdate
        $nis        = $this->request->getPost('NIS');
        $idRiwayatSekolah = $this->request->getPost('idRiwayatSekolah');
        $ruanganId  = $this->request->getPost('id_ruangan');
        $kelas      = $this->request->getPost('no_kelas');
        $tmt        = $this->request->getPost('tanggal');
        $status        = $this->request->getPost('status');

        // Validasi sederhana
        if (!$idPrestasi || !$nis || !$idRiwayatSekolah || !$ruanganId || !$kelas || !$tmt || !$status) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Semua field wajib diisi'
            ]);
        }

        $data = [
            'NIS'               => $nis,
            'ID_RIWAYAT_SEKOLAH'=> $idRiwayatSekolah,
            'RUANGAN_ID'        => $ruanganId,
            'KELAS'             => $kelas,
            'TMT'               => $tmt,
            'OLEH'              => session()->get('user_id'),
            'STATUS'            => $status
        ];

        try {
            // Update data berdasarkan id
            $this->modelPrestasiKelas->update($idPrestasi, $data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data prestasi kelas berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ]);
        }
    }

    public function riwayatPrestasi()
    {
        $nis = $this->request->getGet('nis');

        if (!$nis) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'NIS tidak ditemukan'
            ]);
        }

        $result = $this->modelPrestasiKelas->getRiwayatByNIS($nis);

        return $this->response->setJSON($result);
    }
    public function deletePrestasi()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method',
                'code' => 405
            ]);
        }

        $idPrestasi = $this->request->getPost('id');

        if (!$idPrestasi) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID prestasi tidak ditemukan',
                'code' => 400
            ]);
        }

        try {
            $deleted = $this->modelPrestasiKelas->delete($idPrestasi);

            if ($deleted) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data prestasi berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menghapus data prestasi',
                    'code' => 500
                ]);
            }
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
        }
    }



    public function getRiwayatById()
    {
        $id = $this->request->getGet('id');
        $data = $this->modelPrestasiKelas->getRiwayatById($id); // langsung ambil dari model
        return $this->response->setJSON($data);
    }

    public function getDetail($nis)
    {
        $data = $this->modelSiswa->find($nis); // Sesuaikan dengan model kamu
        if ($data) {
            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data siswa tidak ditemukan.']);
        }
    }

    public function detailPembayaran()
    {
        $nis = $this->request->getGet('nis');
        // Ambil sekolah aktif langsung dari ModelSiswa
        $sekolahAktif = $this->modelSiswa->getRiwayatSekolahAktif($nis);
        // Master: semua jenis pembayaran
        $jenis_penerimaan = $this->modelJenisPenerimaan->where('SEKOLAH_ID', $sekolahAktif[0]['ID_SEKOLAH'])->findAll();

        // Mapping berdasarkan siswa
        $mapped = $this->modelMapingJenisPembayaran->getMappingWithJenis($nis);

        // Ambil hanya ID yang sudah mapping
        $mapped_ids = array_column($mapped, 'ID_JENIS_PENERIMAAN');

        $data = [
            'jenis_penerimaan' => $jenis_penerimaan,
            'mapped' => $mapped,
            'mapped_ids' => $mapped_ids,
            'nis' => $nis,
            'NAMA_SEKOLAH' => $sekolahAktif[0]['NAMA_SEKOLAH']
        ];

        return view('kesiswaan/siswa/detail/pembayaran', $data);
    }

    public function updatetenor()
    {
        $json = $this->request->getJSON(true);
        $id = $json['id'] ?? null;
        $tenor = $json['tenor'] ?? null;
        $jumlah = $json['jumlah'] ?? null;
        $sisa_dibayar = $json['sisa_dibayar'] ?? null;

        if (!$id || $tenor === null || $jumlah === null || $sisa_dibayar === null) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap.'
            ]);
        }

        try {
            $this->modelMapingJenisPembayaran->update($id, [
                'TENOR' => $tenor,
                'JUMLAH' => $jumlah,
                'SISA_DIBAYAR' => $sisa_dibayar,
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Tenor berhasil diupdate.'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function mapJenisPembayaran()
    {
        $input = $this->request->getJSON(true); // penting: true agar array, bukan object

        $nis = $input['nis'] ?? null;
        $jenis_penerimaan_id = $input['jenis_penerimaan_id'] ?? null;

        if (!$nis || !$jenis_penerimaan_id) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'NIS dan jenis pembayaran tidak boleh kosong.'
            ]);
        }

        // Cek duplikat mapping
        $exists = $this->modelMapingJenisPembayaran
            ->where('NIS', $nis)
            ->where('ID_JENIS_PENERIMAAN', $jenis_penerimaan_id)
            ->first();

        if ($exists) {
            return $this->response->setJSON([
                'status' => false,
                'code' => 201,
                'message' => 'Jenis pembayaran sudah dimapping.'
            ]);
        }

        // Ambil data jenis pembayaran
        $jenis_penerimaan = $this->modelJenisPenerimaan->find($jenis_penerimaan_id);
        if (!$jenis_penerimaan) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Jenis pembayaran tidak ditemukan.'
            ]);
        }

        // Simpan mapping baru
        $jumlahxTenor = $jenis_penerimaan['JUMLAH'] * $jenis_penerimaan['TENOR'];
        $data = [
            'NIS' => $nis,
            'TENOR' => $jenis_penerimaan['TENOR'], // beban tagihan awal
            'JUMLAH' => $jumlahxTenor, // beban tagihan awal jumlah master * tenor
            'SISA_DIBAYAR' => $jumlahxTenor, // beban tagihan awal
            'ID_JENIS_PENERIMAAN' => $jenis_penerimaan_id,
            'OLEH' => session()->get('user_id')
        ];

        if (!$this->modelMapingJenisPembayaran->insert($data)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal menyimpan mapping.'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Mapping berhasil disimpan.'
        ]);
    }
   public function batalMapJenisPembayaran()
    {
        $request = $this->request->getJSON(true); // Ambil input JSON sebagai array
        if (!$request || !isset($request['id'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak ditemukan'])->setStatusCode(400);
        }

        $id = $request['id'];
        // Update status jadi 0
        $updated = $this->modelMapingJenisPembayaran->update($id, ['STATUS' => 0]);

        if ($updated) {
            return $this->response->setJSON(['status' => true, 'message' => 'Mapping jenis pembayaran berhasil dibatalkan']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal membatalkan mapping']);
        }
    }

    public function aktifkanMapJenisPembayaran()
    {
        $request = $this->request->getJSON(true); // Ambil input JSON sebagai array
        if (!$request || !isset($request['id'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak ditemukan'])->setStatusCode(400);
        }

        $id = $request['id'];
        // Update status jadi 0
        $updated = $this->modelMapingJenisPembayaran->update($id, ['STATUS' => 1]);

        if ($updated) {
            return $this->response->setJSON(['status' => true, 'message' => 'Mapping jenis pembayaran berhasil dibatalkan']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal membatalkan mapping']);
        }
    }
    
    public function save()
    {
        if ($this->request->isAJAX()) {
            try {
                $data = [
                    'NIS' => $this->request->getPost('NIS'),
                    'NAMA' => $this->request->getPost('NAMA'),
                    'TEMPAT_LAHIR' => $this->request->getPost('TEMPAT_LAHIR'),
                    'TANGGAL_LAHIR' => $this->request->getPost('TANGGAL_LAHIR'),
                    'JENIS_KELAMIN' => $this->request->getPost('JENIS_KELAMIN'),
                    'AGAMA_ID' => $this->request->getPost('AGAMA_ID'),
                    'PROV_ID' => $this->request->getPost('PROVINSI'),
                    'KAB_ID' => $this->request->getPost('KABUPATEN'),
                    'KEC_ID' => $this->request->getPost('KECAMATAN'),
                    'KEL_ID' => $this->request->getPost('KELURAHAN'),
                    'ALAMAT' => $this->request->getPost('ALAMAT'),
                    'ANGKATAN_ID' => $this->request->getPost('ANGKATAN_ID'),
                    'NAMA_AYAH' => $this->request->getPost('NAMA_AYAH'),
                    'NAMA_IBU' => $this->request->getPost('NAMA_IBU'),
                    'KONTAK_ORANG_TUA' => $this->request->getPost('KONTAK_ORANG_TUA'),
                    'CREATED_AT' => date('Y-m-d H:i:s'),
                    'STATUS' => $this->request->getPost('STATUS'),
                ];

                $this->modelSiswa->insert($data);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }


    public function delete($nis)
    {
        if ($this->request->isAJAX()) {
            $this->modelSiswa->delete($nis);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data siswa berhasil dihapus.'
            ]);
        }
    }


    public function get($nis)
    {
        if ($this->request->isAJAX()) {
            $data = $this->modelSiswa->find($nis);
            return $this->response->setJSON($data);
        }
    }

    public function update()
    {
        if ($this->request->isAJAX()) {
            $data = $this->request->getPost();
            $nis = $data['NIS'];
            unset($data['NIS']); // karena primary key
            $data = [
                    'NAMA' => $this->request->getPost('NAMA'),
                    'TEMPAT_LAHIR' => $this->request->getPost('TEMPAT_LAHIR'),
                    'TANGGAL_LAHIR' => $this->request->getPost('TANGGAL_LAHIR'),
                    'JENIS_KELAMIN' => $this->request->getPost('JENIS_KELAMIN'),
                    'AGAMA_ID' => $this->request->getPost('AGAMA_ID'),
                    'PROV_ID' => $this->request->getPost('PROVINSI'),
                    'KAB_ID' => $this->request->getPost('KABUPATEN'),
                    'KEC_ID' => $this->request->getPost('KECAMATAN'),
                    'KEL_ID' => $this->request->getPost('KELURAHAN'),
                    'ALAMAT' => $this->request->getPost('ALAMAT'),
                    'ANGKATAN_ID' => $this->request->getPost('ANGKATAN_ID'),
                    'NAMA_AYAH' => $this->request->getPost('NAMA_AYAH'),
                    'NAMA_IBU' => $this->request->getPost('NAMA_IBU'),
                    'KONTAK_ORANG_TUA' => $this->request->getPost('KONTAK_ORANG_TUA'),
                    'CREATED_AT' => date('Y-m-d H:i:s'),
                    'STATUS' => $this->request->getPost('STATUS'),
                ];
            $this->modelSiswa->update($nis, $data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data siswa berhasil diperbarui.'
            ]);
        }
    }

    public function savePendaftaranSekolah()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $nis = $this->request->getPost('NIS');
        $sekolah_id = $this->request->getPost('SEKOLAH_ID');
        $angkatan_id = $this->request->getPost('ANGKATAN_ID');
        $tanggal = $this->request->getPost('TANGGAL');
        
        if (!$nis || !$sekolah_id || !$angkatan_id) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap']);
        }

        try {
            // 1️⃣ Set semua riwayat sekolah siswa ini menjadi tidak aktif
            $this->modelSiswaSekolah
                ->where('NIS', $nis)
                ->set(['STATUS' => 0])
                ->update();

            // 2️⃣ Insert data baru dengan STATUS = 1
            $data = [
                'ANGKATAN_NEW' => $angkatan_id,
                'NIS'         => $nis,
                'SEKOLAH_ID'  => $sekolah_id,
                'STATUS'      => 1,
                'OLEH'        => session()->get('user_id') ?? null,
                'TANGGAL'     => $tanggal,
            ];

            $this->modelSiswaSekolah->insert($data);

            return $this->response->setJSON(['status' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function riwayatSekolahTable()
    {
        $nis = $this->request->getGet('nis');
        if (!$nis) {
            return '';
        }
        $riwayat_sekolah = $this->modelSiswaSekolah->getRiwayatSekolahByNIS($nis);

        $output = '';
        if (!empty($riwayat_sekolah)) {
            foreach ($riwayat_sekolah as $index => $sekolah) {
                $output .= '<tr>';
                $output .= '<td class="text-center">' . ($index + 1) . '</td>';
                $output .= '<td class="text-center">' . esc($sekolah['ANGKATAN_NEW']) . '</td>';
                $output .= '<td class="text-center">' . esc($sekolah['NAMA_SEKOLAH']) . '</td>';
                $output .= '<td class="text-center">' . esc($sekolah['NIS_NEW']) . '</td>';
                $output .= '<td class="text-center">' . date('d-m-Y', strtotime($sekolah['TANGGAL'])) . '</td>';
                $output .= '<td class="text-center">';
                $output .= '<div class="form-check form-switch mb-0 d-inline-block">';
                $output .= '<input class="form-check-input status-switch" 
                                    type="checkbox" 
                                    id="switch_' . $sekolah['ID'] . '" 
                                    ' . ($sekolah['STATUS'] == 1 ? 'checked' : '') . ' 
                                    data-id="' . $sekolah['ID'] . '">';
                $output .= '<label class="form-check-label" for="switch_' . $sekolah['ID'] . '">'
                            . ($sekolah['STATUS'] == 1 ? 'Aktif' : 'Tidak Aktif') . 
                        '</label>';
                $output .= '</div>';
                $output .= '</td>';
                $output .= '<td class="text-center">';
                $output .= '<button class="btn btn-sm btn-outline-danger" onclick="hapusRiwayat(' . $sekolah['ID'] . ')"> <i class="fa fa-trash" aria-hidden="true"></i> Hapus</button>';
                $output .= '</td>';
                $output .= '</tr>';
            }
        } else {
            $output = '<tr><td colspan="4" class="text-center">Tidak ada riwayat sekolah....</td></tr>';
        }

        return $this->response->setContentType('text/html')->setBody($output);
    }

    public function updateStatusSekolah()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if (!$id) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'ID tidak ditemukan'
            ]);
        }

        $sekolah = $this->modelSiswaSekolah->find($id);
        if (!$sekolah) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data sekolah tidak ditemukan'
            ]);
        }

        $nis = $sekolah['NIS'];

        // Set semua sekolah siswa ini menjadi tidak aktif
        $this->modelSiswaSekolah->where('NIS', $nis)->set(['STATUS' => 0])->update();

        // Jika status = 1, aktifkan sekolah yang dipilih
        if ($status == 1) {
            $this->modelSiswaSekolah->update($id, ['STATUS' => 1]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Status berhasil diperbarui'
        ]);
    }

    public function hapusRiwayatSekolah()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => false,
                'message' => 'Forbidden',
                'code'    => 403
            ]);
        }

        $id = $this->request->getPost('id');

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => 'ID riwayat sekolah tidak ditemukan',
                'code'    => 400
            ]);
        }

        try {
            $deleted = $this->modelSiswaSekolah->delete($id);

            if ($deleted) {
                return $this->response->setJSON([
                    'status'  => true,
                    'message' => 'Riwayat sekolah berhasil dihapus',
                    'code'    => 200
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'status'  => false,
                    'message' => 'Gagal menghapus riwayat sekolah',
                    'code'    => 500
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'message' => $e->getMessage(),
                'code'    => $e->getCode() ?: 500
            ]);
        }
    }





}