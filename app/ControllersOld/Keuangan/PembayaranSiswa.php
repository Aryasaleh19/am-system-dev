<?php 
namespace App\Controllers\Keuangan;

use App\Controllers\BaseController;
use App\Models\Keuangan\ModelRekeningBank;
use App\Models\Keuangan\ModelPembayaranSiswa;
use App\Models\Referensi\ModelJenisPenerimaan;
use App\Models\Kesiswaan\ModelSiswa;
use App\Models\Referensi\ModelMapingJenisPembayaran;

class PembayaranSiswa extends BaseController
{
    protected $modelRekeningBank;
    protected $modelJenisPenerimaan;
    protected $modelSiswa;
    protected $modelPembayaranSiswa;
    protected $modelMapingJenisPembayaran;


    public function __construct()
    {
        $this->modelRekeningBank = new ModelRekeningBank();
        $this->modelJenisPenerimaan = new ModelJenisPenerimaan();
        $this->modelSiswa = new ModelSiswa();
        $this->modelPembayaranSiswa = new ModelPembayaranSiswa();
        $this->modelMapingJenisPembayaran = new ModelMapingJenisPembayaran();

        helper('tgl_indo');
        helper('terbilang');

    }

    public function index()
    {
        $data['rekening_bank'] = $this->modelRekeningBank->findAll();
        return view('keuangan/pembayaransiswa/index', ['title' => '➡️ Pembayaran Siswa', 'data' => $data]);
    }

    public function formsiswabaru()
    {
        return view('keuangan/pembayaransiswa/modals_form_siswa_baru');
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
                    'aksi' => $row['NIS']
                ];
            }

            return $this->response->setJSON(['data' => $data]);
        }
    }

    public function modaldetailsiswa()
    {
        return view('keuangan/pembayaransiswa/modals_detail_siswa');
    }

    public function detailProfile()
    {
        $nis = $this->request->getGet('nis');
        $siswa = $this->modelSiswa->find($nis);

        if (!$siswa) {
            return 'Data siswa tidak ditemukan.';
        }

        return view('keuangan/pembayaransiswa/detail/profile', ['siswa' => $siswa]);
    }

    public function detailBerkas()
    {
        return view('keuangan/pembayaransiswa/detail/berkas');
    }

    public function detailOrangtua()
    {
        return view('keuangan/pembayaransiswa/detail/orangtua');
    }

    public function detailPembayaran()
    {
        $data['jenis_penerimaan'] = $this->modelJenisPenerimaan
            ->where('KATEGORI', 'Siswa')
            ->where('STATUS', '1')
            ->findAll();
        $data['rekening_bank'] = $this->modelRekeningBank->findAll();
        $data['nis'] = $this->request->getGet('nis');
        $data['siswa'] = $this->modelSiswa->find($data['nis']);

        if (!$data['siswa']) {
            return 'Data siswa tidak ditemukan.';
        }

        // Mapping berdasarkan siswa
        $mapped = $this->modelMapingJenisPembayaran->getMappingWithJenisAktif($data['nis']);
        $data['jenis_penerimaan'] = $mapped;
        // riwayat pembayaran
        $riwayat_pembayaran = $this->modelPembayaranSiswa->getRiwayatPembayaranDenganMapping($data['nis']);
        $data['riwayat_pembayaran'] = $riwayat_pembayaran;

        return view('keuangan/pembayaransiswa/detail/pembayaran', $data);
    }

    public function kartukontrol()
    {
        $data['nis'] = $this->request->getGet('nis');
        $data['siswa'] = $this->modelSiswa->find($data['nis']);

  
        return view('keuangan/pembayaransiswa/detail/kartukontrol', $data);
    }

    public function prestasi()
    {
        return view('keuangan/pembayaransiswa/detail/prestasi');
    }

    public function getDetail($nis)
    {
        $data = $this->modelSiswa->find($nis);

        if ($data) {
            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data siswa tidak ditemukan.']);
        }
    }

    public function get($nis)
    {
        if ($this->request->isAJAX()) {
            $data = $this->modelSiswa->find($nis);
            return $this->response->setJSON($data);
        }
    }

    private function generateIdPembayaran($idJenis, $nis, $tanggal)
    {
        $tglStr = date('dmY', strtotime($tanggal));

        $count = $this->modelPembayaranSiswa
            ->where('ID_JENIS_PENERIMAAN', $idJenis)
            ->where('NIS', $nis)
            ->where('TANGGAL', $tanggal)
            ->countAllResults();

        $noUrut = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return "TRX.{$idJenis}.{$nis}.{$tglStr}.{$noUrut}";
    }

    public function simpanPembayaran()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Permintaan tidak valid, Invalid access .'
            ]);
        }

        $userId = session('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User belum login atau session tidak ditemukan.'
            ]);
        }

        // Validasi input (file opsional)
        if (!$this->validate([
            'nis' => 'required',
            'id_jenis_penerimaan' => 'required|numeric',
            'jumlah_asli' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'metode' => 'required|numeric',
            'upload' => [
                'mime_in[upload,image/jpg,image/jpeg,image/png,image/gif]',
                'max_size[upload,2048]' // 2MB
            ]
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Ambil input
        $nis     = $this->request->getPost('nis');
        $idJenis = $this->request->getPost('id_jenis_penerimaan');
        $tanggal = $this->request->getPost('tanggal');
        $blnTagihan = date('m', strtotime($this->request->getPost('bulanPembayaran')));
        $thnTagihan = date('Y', strtotime($this->request->getPost('bulanPembayaran')));

        // Cek file upload
        $fileUpload = $this->request->getFile('upload');
        if ($fileUpload && $fileUpload->isValid() && !$fileUpload->hasMoved()) {
            // Ada file diunggah → simpan
            $newName = $fileUpload->getRandomName();
            $fileUpload->move(FCPATH . 'file_uploads', $newName);
        } else {
            // Tidak ada file → pakai default
            $newName = 'tanpabukti.jpg';
        }

        // Generate ID pembayaran
        $customId = $this->generateIdPembayaran($idJenis, $nis, $tanggal);

        // Data siap disimpan
        $data = [
            'ID' => $customId,
            'NIS' => $nis,
            'ID_JENIS_PENERIMAAN' => $idJenis,
            'ID_MAPING_JENIS_PENERIMAAN' => $this->request->getPost('idMapPenerimaan'),
            'JUMLAH' => $this->request->getPost('jumlah_asli'),
            'TANGGAL' => $tanggal,
            'BULAN_TAGIHAN' => $blnTagihan,
            'TAHUN_TAGIHAN' => $thnTagihan,
            'ID_REKENING_BANK' => $this->request->getPost('metode'),
            'CATATAN' => $this->request->getPost('catatan'),
            'BUKTI' => $newName,
            'OLEH' => $userId
        ];

        // Simpan ke database
        $this->modelPembayaranSiswa->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Pembayaran berhasil disimpan.'
        ]);
    }



    public function deletePembayaranSiswa()
    {
        $id = $this->request->getPost('id');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Permintaan tidak valid.'
            ]);
        }

        // Cek apakah ID pembayaran ada
        $pembayaran = $this->db->table('keuangan_pembayaran_siswa')
            ->where('ID', $id)
            ->get()
            ->getRowArray();

        if (!$pembayaran) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pembayaran tidak ditemukan.'
            ]);
        }

        // Ambil user ID yang sedang login
        $userIdLogin = session()->get('user_id');

        // Cek apakah user yang login sama dengan user yang input pembayaran
        if ($pembayaran['OLEH'] != $userIdLogin) {
            return $this->response->setJSON([
                'status' => 'warning',
                'message' => 'Anda tidak berhak menghapus pembayaran ini. Pastikan nama Anda pada Kolom Kasir.'
            ]);
        }

        // Hapus file bukti kalau ada dan bukan file default tanpbukti.jpg
        if (!empty($pembayaran['BUKTI']) && strtolower($pembayaran['BUKTI']) !== 'tanpbukti.jpg') {
            $filePath = FCPATH . 'file_uploads/' . $pembayaran['BUKTI'];
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus pembayaran dari database
        $this->modelPembayaranSiswa->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Pembayaran berhasil dihapus.'
        ]);
    }



    public function getRiwayatPembayaranByNis()
    {
        $id_jenis = $this->request->getGet('id_jenis');
        $nis = $this->request->getGet('nis');
        $idMapPenerimaan = $this->request->getGet('idMapPenerimaan');

        $data['riwayat_pembayaran'] = $this->modelPembayaranSiswa->getRiwayatPembayaranDenganMapping($nis, $id_jenis, $idMapPenerimaan);

        if (empty($data['riwayat_pembayaran'])) {
            $html = '<tr><td colspan="10" class="text-center">Tidak ada riwayat pembayaran.</td></tr>';
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $html
            ]);
        }

        $totalPembayaran = 0;
        ob_start(); // MULAI buffer output

        foreach ($data['riwayat_pembayaran'] as $i => $item) {
            $totalPembayaran += $item['JUMLAH'];
            // url bukti pembayaran
     
            if (!empty($item['BUKTI'])) {
                // URL file bukti
                $getUrl = base_url('file_uploads/' . $item['BUKTI']);
                $buktiUrl = '<a href="' . esc($getUrl) . '" class="btn btn-xs btn-outline-info" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>';
            } else {
                $buktiUrl = '<span class="text-center text-danger">Tanpa Bukti</span>';
            }


            echo '<tr>';
            echo '<td class="text-center">'.($i + 1).'</td>';
            echo '<td class="text-center">'.date('d/m/Y', strtotime($item['TANGGAL'])).'</td>';
            echo '<td class="text-center">'.bulan($item['BULAN_TAGIHAN']).'</td>';
            echo '<td class="text-center">'.date('Y', strtotime($item['TAHUN_TAGIHAN'])).'</td>';
            echo '<td class="text-end">Rp '.number_format($item['JUMLAH'], 0, ',', '.').'</td>';
            echo '<td>'.esc($item['NAMA_BANK'] ?? 'Tunai').'</td>';
            echo '<td>'.esc($item['CATATAN']).'</td>';
            echo '<td class="text-center">'. $buktiUrl .'</td>';
            echo '<td>'.esc($item['NAMA_PENGGUNA']).'</td>';

            $kwitansiUrl = base_url('laporan/keuangan/kwitansipembayaransiswa?id=' . $item['ID_PEMBAYARAN']);
            
            echo '<td class="text-center">
                    <div class="btn-group">
                        <a class="btn btn-outline-info btn-sm" 
                            href="' . $kwitansiUrl . '" 
                            target="_blank">
                            <i class="fa fa-print me-2"></i> Kwitansi
                        </a>

                        <a class="btn btn-outline-info btn-sm btn-hapus" 
                            data-id="' . $item['ID_PEMBAYARAN'] . '" 
                            href="javascript:void(0);">
                            <i class="bi bi-trash me-2"></i> Hapus
                        </a>
                    </div>
                </td>';
            echo '</tr>';

        }

        // Tambahkan baris total pembayaran di bawah tabel
        echo '<tr>';
        echo '<td colspan="4" class="text-center fw-bold">Total Pembayaran</td>';
        echo '<td class="text-end fw-bold">Rp '.number_format($totalPembayaran, 0, ',', '.').'</td>';
        echo '<td colspan="4"></td>';
        echo '</tr>';

        $html = ob_get_clean(); // Ambil isi buffer dan bersihkan

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $html
        ]);
    }

    public function modalPembayaranAngkatan()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'title' => 'Kas Siswa / Angkatan'
            ];
            return view('keuangan/pembayaransiswa/modals_pembayaran_angkatan', $data);
        }
        return redirect()->back();
    }

    public function getAngkatanTableEditContent()
    {
        if ($this->request->isAJAX()) {
            $angkatanId = $this->request->getGet('angkatanId');

            $builder = $this->modelSiswa
                ->select('siswa.NIS, siswa.NAMA, siswa.ANGKATAN_ID, angkatan.ANGKATAN')
                ->join('siswa_angkatan angkatan', 'angkatan.ID = siswa.ANGKATAN_ID', 'left')
                ->where('siswa.ANGKATAN_ID', $angkatanId);

            $siswa = $builder->findAll();

            $jenisPembayaran = $this->modelMapingJenisPembayaran->getAllJenisAktifByAngkatan($angkatanId);

            // Header tingkat 1 (main header)
            $header1 = '<tr>
                <th rowspan="2" class="text-center">NO</th>
                <th rowspan="2" class="text-center">NIS</th>
                <th rowspan="2" class="text-center" style="width: 300px;">NAMA SISWA</th>
                <th rowspan="2" class="text-center">ANGKATAN</th>';

            foreach ($jenisPembayaran as $jenis) {
                $header1 .= '<th colspan="3" class="text-center">' . esc($jenis['JENIS_PENERIMAAN']) . '<br><small class="text-info">Rp ' . number_format($jenis['JUMLAH'], 0, ',', '.') . '</small></th>';
            }
            $header1 .= '<th rowspan="2" class="text-center">TOTAL</th></tr>';

            // Header tingkat 2 (sub kolom)
            $header2 = '<tr>';
            foreach ($jenisPembayaran as $jenis) {
                $header2 .= '<th class="text-center">Dibayar</th><th class="text-center">Sisa</th><th class="text-center">Input</th>';
            }
            $header2 .= '</tr>';

            // Hitung total Telah Dibayar dan Sisa Dibayar per jenis pembayaran
            $totalTelahBayar = [];
            $totalSisaBayar = [];
            foreach ($jenisPembayaran as $jenis) {
                $totalTelahBayar[$jenis['ID']] = 0;
                $totalSisaBayar[$jenis['ID']] = 0;
            }

            foreach ($siswa as $row) {
                $mappedJenis = $this->modelMapingJenisPembayaran->getMappingWithJenisAktif($row['NIS']);
                foreach ($mappedJenis as $mj) {
                    $jenisId = $mj['ID_JENIS_PENERIMAAN'];
                    if (isset($totalTelahBayar[$jenisId])) {
                        $totalTelahBayar[$jenisId] += $mj['TELAH_DIBAYAR'];
                        $totalSisaBayar[$jenisId] += $mj['SISA_DIBAYAR'];
                    }
                }
            }

            // Body
            $body = '';
            $no = 1;
            foreach ($siswa as $row) {
                $body .= '<tr>';
                $body .= '<td class="text-center">' . $no++ . '</td>';
                $body .= '<td class="text-center">' . esc($row['NIS']) . '</td>';
                $body .= '<td class="text-left col-nama-siswa">' . esc($row['NAMA']) . '</td>';
                $body .= '<td class="text-center">' . esc($row['ANGKATAN']) . '</td>';

                $mappedJenis = $this->modelMapingJenisPembayaran->getMappingWithJenisAktif($row['NIS']);
                $mappedIds = array_column($mappedJenis, 'ID_JENIS_PENERIMAAN');
                $mappedInfo = [];
                foreach ($mappedJenis as $mj) {
                    $mappedInfo[$mj['ID_JENIS_PENERIMAAN']] = $mj;
                }

                foreach ($jenisPembayaran as $jenis) {
                    if (in_array($jenis['ID'], $mappedIds)) {
                        $mj = $mappedInfo[$jenis['ID']];
                        $lunas = $mj['LUNAS'] ?? 0;

                        // Tampilkan nilai telah dibayar dan sisa bayar (read-only)
                        $body .= '<td class="text-end" style="background-color: #FF9B00"><span class="badge bg-label-success">' . number_format($mj['TELAH_DIBAYAR'], 0, ',', '.') . '</span></td>';
                        $body .= '<td class="text-end" style="background-color: #FF9B00"><span class="badge bg-label-danger">' . number_format($mj['SISA_DIBAYAR'], 0, ',', '.') . '</span></td>';

                        // Input kolom, jika sudah lunas kunci input
                        $editable = $lunas == 1 ? 'contenteditable="false" class="bg-light text-muted"' : 'contenteditable="true"';
                        $body .= '<td class="text-center tableeditcontet" ' . $editable .
                                    ' data-nis="' . esc($row['NIS']) . '" 
                                    data-jenis-id="' . esc($jenis['ID']) . '" 
                                    data-jenis-penerimaan="' . esc($jenis['JENIS_PENERIMAAN']) . '" 
                                    data-sisa="' . $mj['SISA_DIBAYAR'] . '"
                                    title="' . esc($jenis['JENIS_PENERIMAAN']) . '"></td>';
                    } else {
                        // Jika tidak wajib, buat 3 kolom kosong / atau tanda -
                        $body .= '<td class="text-center" style="background-color: #FF9B00">-</td><td class="text-center" style="background-color: #FF9B00">-</td><td class="text-center" style="background-color: #FF9B00"></td>';
                    }
                }

                $body .= '<td class="text-center" style="background-color: #FF9B00"></td>'; // kolom TOTAL kosong dulu
                $body .= '</tr>';
            }

            if (empty($siswa)) {
                $colspan = 4 + (count($jenisPembayaran) * 3) + 1; // NO,NIS,NAMA,ANGKATAN + 3*jenis + TOTAL
                $body = '<tr><td colspan="' . $colspan . '" class="text-center">Tidak ada siswa pada angkatan ini.</td></tr>';
            }

            // Footer (2 baris: Total Telah Dibayar & Total Sisa Dibayar)
            $footer = '<tr><th colspan="4" class="text-end text-success">Total Telah Dibayar:</th>';
            foreach ($jenisPembayaran as $jenis) {
                $footer .= '<th class="text-end text-success" colspan="2">' . number_format($totalTelahBayar[$jenis['ID']], 0, ',', '.') . '</th>';
                $footer .= '<th style="background-color: #FF9B00"></th>'; // kosong untuk kolom Input
            }
            $footer .= '<th style="background-color: #FF9B00"></th></tr>';

            $footer .= '<tr><th colspan="4" class="text-end text-danger">Total Sisa Dibayar:</th>';
            foreach ($jenisPembayaran as $jenis) {
                $footer .= '<th class="text-end text-danger" colspan="2">' . number_format($totalSisaBayar[$jenis['ID']], 0, ',', '.') . '</th>';
                $footer .= '<th style="background-color: #FF9B00"></th>'; // kosong untuk kolom Input
            }
            $footer .= '<th style="background-color: #FF9B00"></th></tr>';

            return $this->response->setJSON([
                'header1' => $header1,
                'header2' => $header2,
                'body'    => $body,
                'footer'  => $footer
            ]);
        }
    }


   public function simpanPembayaranAngkatan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Permintaan tidak valid.'
            ]);
        }

        $userId = session('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User belum login atau session tidak ditemukan.'
            ]);
        }

        $jsonInput = $this->request->getJSON(true);
        $pembayaranArray = $jsonInput['pembayaran'] ?? null;

        if (!$pembayaranArray || !is_array($pembayaranArray)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data pembayaran tidak ditemukan atau format salah.'
            ]);
        }

        $tanggal = date('Y-m-d');

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            foreach ($pembayaranArray as $item) {
                if (
                    empty($item['nis']) || 
                    empty($item['id_jenis_penerimaan']) || 
                    !isset($item['jumlah']) || 
                    !is_numeric($item['jumlah']) ||
                    $item['jumlah'] <= 0
                ) {
                    throw new \Exception('Data pembayaran tidak valid.');
                }

                $customId = $this->generateIdPembayaran($item['id_jenis_penerimaan'], $item['nis'], $tanggal);

                $idJenis = $item['id_jenis_penerimaan'];
                // Cek apakah jenis penerimaan ada
                $jenisPenerimaan = $this->modelJenisPenerimaan->find($idJenis);
                $data = [
                    'ID' => $customId,
                    'NIS' => $item['nis'],
                    'ID_JENIS_PENERIMAAN' => $item['id_jenis_penerimaan'],
                    'JUMLAH' => $item['jumlah'],
                    'TANGGAL' => $item['tanggal'] ?? $tanggal, // gunakan tanggal dari input atau default ke hari ini
                    'ID_REKENING_BANK' => 3 ?? null, // default bank muamalat
                    'CATATAN' =>  'Pembayaran ' . $jenisPenerimaan['JENIS_PENERIMAAN'], // diubah agar menjadi dinamis (biaya NAMA_JENIS_PEMBAYARAN)
                    'OLEH' => $userId
                ];

                $this->modelPembayaranSiswa->insert($data);
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data pembayaran.'
                ]);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pembayaran angkatan berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getRiwayatPembayaranByNisInfo()
    {
        $nis = $this->request->getGet('nis');

        $rows = $this->modelPembayaranSiswa->getRiwayatPembayaranDenganMappingInfo($nis);

        if (empty($rows)) {
            $html = '<tr><td colspan="7" class="text-center">Tidak ada riwayat pembayaran.</td></tr>';
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $html
            ]);
        }

        $grouped = [];

        foreach ($rows as $item) {
            $jenis = $item['JENIS_PENERIMAAN'];

            if (!isset($grouped[$jenis])) {
                $grouped[$jenis] = [
                    'JUMLAH_TOTAL' => $item['JUMLAH_MASTER'], // total tagihan
                    'TELAH_DIBAYAR' => $item['TELAH_DIBAYAR'],
                    'SISA_DIBAYAR' => $item['SISA_DIBAYAR'],
                    'items' => []
                ];
            }

            $grouped[$jenis]['items'][] = $item;
        }

        $totalKeseluruhan = 0;
        ob_start();

        foreach ($grouped as $jenis => $data) {
            // Tampilkan header grup dengan data master mapping
            echo '<tr class="table-secondary fw-bold">';
            echo '<td colspan="6" class="small">💵'
                . esc($jenis)
                . ' <span class="text-danger">Rp ' . number_format($data['JUMLAH_TOTAL'], 0, ',', '.')
                . '</span> - <span class="text-info">Rp ' . number_format($data['TELAH_DIBAYAR'], 0, ',', '.')
                . '</span>'.($data['SISA_DIBAYAR'] > 0 ? ' (Sisa: Rp ' . number_format($data['SISA_DIBAYAR'], 0, ',', '.') . ')' : '(<span class="text-success">Lunas</span>)') . '</td>';
            echo '</tr>';

            $totalPerJenis = 0;
            foreach ($data['items'] as $i => $item) {
                $totalPerJenis += $item['JUMLAH'];
                $totalKeseluruhan += $item['JUMLAH'];

                echo '<tr>';
                echo '<td class="text-center small">'.($i + 1).'</td>';
                echo '<td class="text-center small">'.date('d/m/Y', strtotime($item['TANGGAL'])).'</td>';
                echo '<td class="text-end small">Rp '.number_format($item['JUMLAH'], 0, ',', '.').'</td>';
                echo '<td class="small">'.esc($item['NAMA_BANK'] ?? 'Tunai').'</td>';
                echo '<td class="small">'.esc($item['CATATAN']).'</td>';
                echo '<td class="small">'.esc($item['NAMA_PENGGUNA']).'</td>';
                echo '</tr>';
            }

            // Total per jenis pembayaran (jumlah pembayaran detail)
            echo '<tr class="fw-bold text-info">';
            echo '<td colspan="2" class="text-end small">Total '.esc($jenis).'</td>';
            echo '<td class="text-end small">Rp '.number_format($totalPerJenis, 0, ',', '.').'</td>';
            echo '<td colspan="4"></td>';
            echo '</tr>';
        }

        // Total keseluruhan semua pembayaran detail
        echo '<tr class="fw-bold table-info">';
        echo '<td colspan="2" class="text-end small">Total Keseluruhan</td>';
        echo '<td class="text-end small">Rp '.number_format($totalKeseluruhan, 0, ',', '.').'</td>';
        echo '<td colspan="4"></td>';
        echo '</tr>';

        $html = ob_get_clean();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $html
        ]);
    }


}