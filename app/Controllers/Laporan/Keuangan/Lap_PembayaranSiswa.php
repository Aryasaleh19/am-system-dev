<?php 
namespace App\Controllers\Laporan\Keuangan;

use App\Controllers\BaseController;
use App\Models\Keuangan\ModelRekeningBank;
use App\Models\Keuangan\ModelPembayaranSiswa;
use App\Models\Keuangan\ModelKasMasuk;
use App\Models\Referensi\ModelJenisPenerimaan;
use App\Models\Kesiswaan\ModelSiswa;
use App\Models\Referensi\ModelMapingJenisPembayaran;
use App\Models\Kesiswaan\ModelAngkatan;
use App\Models\Pengaturan\ProfilModel;
use Mpdf\Mpdf; 
class Lap_PembayaranSiswa extends BaseController
{
    protected $modelRekeningBank;
    protected $modelJenisPenerimaan;
    protected $modelSiswa;
    protected $modelPembayaranSiswa;
    protected $modelMapingJenisPembayaran;
    protected $modelProfil;
    protected $modelKasMasuk;
    protected $modelAngkatan;


    public function __construct()
    {
        $this->modelRekeningBank = new ModelRekeningBank();
        $this->modelJenisPenerimaan = new ModelJenisPenerimaan();
        $this->modelSiswa = new ModelSiswa();
        $this->modelPembayaranSiswa = new ModelPembayaranSiswa();
        $this->modelMapingJenisPembayaran = new ModelMapingJenisPembayaran();
        $this->modelProfil = new ProfilModel();
        $this->modelKasMasuk = new ModelKasMasuk();
        $this->modelAngkatan = new ModelAngkatan();
        helper('tgl_indo');
        helper('terbilang');

    }
    public function index(){
        $data['title'] = '📚 Laporan Keuangan';
        return view('laporan/keuangan/index', $data);
    }

    public function kartuKontrol()
    {
        $nis = $this->request->getGet('nis');
        $id_jenis_pembayaran = $this->request->getGet('id_jenis_pembayaran') ?: null;
        $sekolah = $this->request->getGet('sekolah') ?: null;

        if (!$nis) {
            return redirect()->back()->with('error', 'NIS tidak boleh kosong');
        }

        // Panggil stored procedure
        $query = $this->db->query("CALL CetakKartuKontrol(?,?,?)", [$nis, $id_jenis_pembayaran, $sekolah]);
        $rows = $query->getResultArray();

        if (empty($rows)) {
            return "<h3 style='text-align: center; margin-top: 20px; color: red;'>Belum ada riwayat pembayaran.</h3>";
        }

        // Ambil info siswa dari baris pertama
        $siswa = [
            'NIY'          => $rows[0]['NIY'],
            'NISN'         => $rows[0]['NISN'],
            'NAMA_SISWA'   => $rows[0]['NAMA_SISWA'],
        ];

        $grouped = [];
        foreach ($rows as $item) {
            $sekolah = $item['NAMA_SEKOLAH'];
            $jenis   = $item['GROUP_JENIS_PENERIMAAN'];

            if (!isset($grouped[$sekolah])) {
                $grouped[$sekolah] = [];
            }

            if (!isset($grouped[$sekolah][$jenis])) {
                $grouped[$sekolah][$jenis] = [
                    'JUMLAH_TOTAL'  => $item['GROUP_JUMLAH_MASTER'],
                    'TELAH_DIBAYAR' => $item['GROUP_JUMLAH_RINCIAN_DIBAYAR'],
                    'SISA_DIBAYAR'  => $item['SISA_DIBAYAR'],
                    'items'         => [],
                ];
            }

            $grouped[$sekolah][$jenis]['items'][] = [
                'TANGGAL'       => $item['TANGGAL'],
                'BULAN_TAGIHAN' => bulan($item['BULAN_TAGIHAN']),
                'TAHUN_TAGIHAN' => $item['TAHUN_TAGIHAN'],
                'JUMLAH'        => $item['JUMLAH_RINCIAN_DIBAYAR'],
                'NAMA_BANK'     => $item['NAMA_BANK'] ?? 'Tunai',
                'CATATAN'       => $item['CATATAN'] ?? '',
                'NAMA_PENGGUNA' => $item['NAMA_PENGGUNA_PEMBAYARAN'],
                'STATUS_BAYAR'  => $item['STATUS_BAYAR'],
            ];
        }

        // Render view HTML
        $html = view('laporan/keuangan/format_kartukontrol', [
            'siswa'   => $siswa,
            'grouped' => $grouped
        ]);

        // Generate PDF
        $mpdf = new \Mpdf\Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'margin_left'  => 10,
            'margin_right' => 10,
            'margin_top'   => 10,
            'margin_bottom'=> 10,
        ]);

        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S');

        return $this->response->setContentType('application/pdf')
                            ->setHeader('Content-Disposition', 'inline; filename="kartukontrol.pdf"')
                            ->setBody($pdfContent);
    }


    public function kwitansipembayaransiswa()
    {
        $idPembayaran = $this->request->getGet('id');

        if (!$idPembayaran) {
            return "ID Pembayaran tidak ditemukan.";
        }

        $data['pembayaran'] = $this->db
            ->query("CALL CetakKwitansiPembayaranSiswa(?)", [$idPembayaran])
            ->getRowArray();

        if (!$data['pembayaran']) {
            return "Data pembayaran tidak ditemukan.";
        }

        $html = view('laporan/keuangan/kwitansi', $data);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'folio',
            'margin_top' => 2,
            'margin_bottom' => 2,
            'margin_left' => 2,
            'margin_right' => 2,
            'margin_header' => 2,
            'margin_footer' => 2,
            'orientation' => 'P',
            'setAutoPageBreak' => true
        ]);

        $mpdf->WriteHTML($html);
        return $this->response
            ->setContentType('application/pdf')
            ->setBody($mpdf->Output('', 'S'));
    }

    public function kwitansikasmasuk()
    {
        $idKas = $this->request->getGet('id'); // ambil dari tombol data-id

        if (!$idKas) {
            return "ID Kas Masuk tidak ditemukan.";
        }

        // Ambil data kas masuk + join
        $kas = $this->modelKasMasuk
            ->select('keuangan_kas_masuk.*, 
                    keuangan_rekening.NO_REKENING, keuangan_rekening.NAMA_BANK,
                    m_penerimaan_jenis.JENIS_PENERIMAAN,
                    pengguna.NAMA as NAMA_OLEH')
            ->join('keuangan_rekening', 'keuangan_rekening.ID = keuangan_kas_masuk.ID_KAS_BANK_TERIMA', 'left')
            ->join('m_penerimaan_jenis', 'm_penerimaan_jenis.ID = keuangan_kas_masuk.ID_JENIS_PENERIMAAN', 'left')
            ->join('pengguna', 'pengguna.PEGAWAI_ID = keuangan_kas_masuk.OLEH', 'left')
            ->where('keuangan_kas_masuk.ID', $idKas)
            ->first();

        if (!$kas) {
            return "Data kas masuk tidak ditemukan.";
        }

        // siapkan data ke view
        $data = [
            'kas' => $kas,
            'profil' => $this->modelProfil->first(), // jika perlu data sekolah
            'terbilang' => terbilang($kas['JUMLAH'])
        ];

        $html = view('laporan/keuangan/kwitansi_kasmasuk', $data);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'Folio',
            'margin_top' => 2,
            'margin_bottom' => 2,
            'margin_left' => 2,
            'margin_right' => 2,
        ]);

        $mpdf->WriteHTML($html);
        return $this->response
            ->setContentType('application/pdf')
            ->setBody($mpdf->Output('', 'S'));
    }


    public function LaporanTahunanPembayaranSiswa()
    {
        $sekolah = $this->request->getGet('sekolah');
        $tahun = $this->request->getGet('tahun');

        $bank = $this->request->getGet('bank');
        $jenisPembayaran = $this->request->getGet('jenisPembayaran');
        $angkatan = $this->request->getGet('angkatan');

        // jika "all" maka dianggap NULL
        $bank = ($bank === 'all' || $bank === '' || $bank === null) ? null : $bank;
        $jenisPembayaran = ($jenisPembayaran === 'all' || $jenisPembayaran === '' || $jenisPembayaran === null) ? null : $jenisPembayaran;
        $angkatan = ($angkatan === 'all' || $angkatan === '' || $angkatan === null) ? null : $angkatan;

        // Panggil stored procedure
        $query = $this->db->query("CALL CetakDaftarPembayaranSiswa(?, ?, ?, ?, ?)", [
            $sekolah,
            $tahun,
            $bank,
            $jenisPembayaran,
            $angkatan
        ]);

        $result = $query->getResultArray();
        $namaSekolah = isset($result[0]['NAMA_SEKOLAH']) ? $result[0]['NAMA_SEKOLAH'] : '-';
        
        if ($jenisPembayaran === 'all' || $jenisPembayaran === '' || $jenisPembayaran === null) {
            $jenisPembayaran = null;
        }else{
            $jenisPembayaran = $this->modelJenisPenerimaan->find($jenisPembayaran)['JENIS_PENERIMAAN'] ?? null;

        }

        if ($bank === 'all' || $bank === '' || $bank === null) {
            $bank = null;
        }else{
            $bank = $this->modelRekeningBank->find($bank)['NAMA_BANK'] ?? null;
        }

        if ($angkatan === 'all' || $angkatan === '' || $angkatan === null) {
            $angkatan = null;
        }else{
            $angkatan = $this->modelAngkatan->find($angkatan)['ANGKATAN'] ?? null;
        }


        // Tutup koneksi (wajib setelah CALL di MySQL/MariaDB)
        $this->db->close(); 

        // === Transformasi data jadi nested array sesuai kebutuhan view ===
        $grouped = [];
        foreach ($result as $row) {
            $nama = $row['NAMA_SISWA'];

            if (!isset($grouped[$nama])) {
                $grouped[$nama] = [
                    'nama' => $nama,
                    'pembayaran' => []
                ];
            }

            $grouped[$nama]['pembayaran'][] = [
                'jenis' => $row['GROUP_JENIS_PENERIMAAN_SEKOLAH'],   // gabungan jenis + sekolah
                'kewajiban' => $row['SUM_GROUP_JUMLAH_MASTER'],
                'bulan' => [
                    $row['BULAN_1'], $row['BULAN_2'], $row['BULAN_3'], $row['BULAN_4'],
                    $row['BULAN_5'], $row['BULAN_6'], $row['BULAN_7'], $row['BULAN_8'],
                    $row['BULAN_9'], $row['BULAN_10'], $row['BULAN_11'], $row['BULAN_12']
                ],
                'total' => $row['SUM_GROUP_JUMLAH_RINCIAN_DIBAYAR'],
                'sisa'  => $row['SUM_SISA_DIBAYAR'],
                'status' => $row['STATUS_BAYAR']
            ];
        }

        $dataSiswa = array_values($grouped);

        // Kirim ke view
        $html = view('laporan/keuangan/laporan-tahunan-pembayaran-siswa', [
            'dataSiswa' => $dataSiswa,
            'departemen' => $namaSekolah,
            'tahun' => $tahun,
            'bank' => $bank,
            'jenisPembayaran' => $jenisPembayaran,
            'angkatan' => $angkatan
        ]);

        // Generate PDF dengan mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [240, 360], // lebar 240mm, tinggi 360mm
            'orientation' => 'L',
            'margin_top' => 5,
            'margin_bottom' => 5,
            'margin_left' => 5,
            'margin_right' => 5,
            'tempDir' => WRITEPATH . 'temp', // pastikan folder ada dan writable
        ]);

        $mpdf->SetWatermarkText('LH Care System');
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.1;

        $mpdf->WriteHTML($html);
        $mpdf->SetHTMLFooter('Laporan Tahunan Pembayaran Siswa - Hal. {PAGENO} / {nb} | Dicetak: {DATE j F Y}');

        $pdfContent = $mpdf->Output('', 'S');

        return $this->response->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="laporan-tahunan-pembayaran-siswa-'. $namaSekolah . '-tahunbayar-' . $tahun . '-angkatan-' . $angkatan . '-bank-' . $bank . '-jenispembayaran-' . $jenisPembayaran . '.pdf"')
            ->setBody($pdfContent);
    }







}