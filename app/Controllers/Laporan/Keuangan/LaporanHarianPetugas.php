<?php

namespace App\Controllers\Laporan\Keuangan;

use App\Controllers\BaseController;
use App\Models\Keuangan\ModelRekeningBank;
use App\Models\Keuangan\ModelPembayaranSiswa;
use App\Models\Keuangan\ModelKasMasuk;
use App\Models\Keuangan\ModelKasKeluar;
use App\Models\Referensi\ModelJenisPenerimaan;
use App\Models\Kesiswaan\ModelSiswa;
use App\Models\Referensi\ModelMapingJenisPembayaran;
use App\Models\Pengaturan\ProfilModel;
use Mpdf\Mpdf;

class LaporanHarianPetugas extends BaseController
{
    protected $modelRekeningBank;
    protected $modelJenisPenerimaan;
    protected $modelSiswa;
    protected $modelPembayaranSiswa;
    protected $modelMapingJenisPembayaran;
    protected $modelProfil;
    protected $modelKasMasuk;
    protected $modelKasKeluar;


    public function __construct()
    {
        $this->modelRekeningBank = new ModelRekeningBank();
        $this->modelJenisPenerimaan = new ModelJenisPenerimaan();
        $this->modelSiswa = new ModelSiswa();
        $this->modelPembayaranSiswa = new ModelPembayaranSiswa();
        $this->modelMapingJenisPembayaran = new ModelMapingJenisPembayaran();
        $this->modelProfil = new ProfilModel();
        $this->modelKasMasuk = new ModelKasMasuk();
        $this->modelKasKeluar = new ModelKasKeluar();
        helper('tgl_indo');
        helper('terbilang');
    }


    public function pdfLaporanHarianPetugas()
    {
        $tanggal = $this->request->getGet('tgl');   // ex: 2025-01-01
        $tanggal2 = $this->request->getGet('tgl2');   // ex: 2025-01-01
        $petugas  = $this->request->getGet('petugas');   // pegawai_id atau null

        // Ambil nama petugas dari model pengguna
        if ($petugas) {
            $model = new \App\Models\Kepegawaian\ModelPengguna();
            $petugasData = $model->find($petugas);
            $petugasNama = $petugasData['NAMA'] ?? 'SEMUA PETUGAS';
        } else {
            $petugasNama = 'SEMUA PETUGAS';
        }

        // Ambil semua data sekaligus (header ada di tiap baris)
        $query = $this->db->query("CALL CetakLaporanTransaksiHarianPetugas(?, ?, ?)", [
            $tanggal,
            $tanggal2,
            $petugas ?: NULL
        ]);
        $transaksi = $query->getResultArray();
        $query->freeResult();

        // Ambil header dari baris pertama
        $header = $transaksi[0] ?? [];

        // Hitung rekap total KAS MASUK / KELUAR
        $rekap = [
            'TOTAL_KAS_MASUK'  => array_sum(array_column($transaksi, 'KAS_MASUK')),
            'TOTAL_KAS_KELUAR' => array_sum(array_column($transaksi, 'KAS_KELUAR')),
            'TOTAL_TRANSAKSI'  => array_sum(array_column($transaksi, 'JUMLAH')),
        ];

        // Gabungkan data untuk view
        $data = [
            'header'     => $header,
            'transaksi'  => $transaksi,
            'rekap'      => $rekap,
            'tanggal'    => $tanggal,
            'petugas'    => $petugasNama,
        ];

        // Render HTML view laporan
        $html = view('laporan/keuangan/laporan-harian-petugas', $data);

        // Generate PDF dengan mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'folio',
            'orientation' => 'L',
            'margin_top' => 5,    // margin atas
            'margin_bottom' => 5, // margin bawah
            'margin_left' => 5,   // margin kiri
            'margin_right' => 5,  // margin kanan
        ]);

        // Tambah watermark teks
        $mpdf->SetWatermarkText('Al-Muhajrin System');
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.1;


        // Tulis HTML
        $mpdf->WriteHTML($html);

        // footer    
        $mpdf->SetHTMLFooter('Laporan Periodik Rekapitulasi Transaksi Petugas - Hal. {PAGENO} / {nb} | Dicetak: {DATE j F Y}');
        // Output PDF ke browser
        $pdfContent = $mpdf->Output('', 'S'); // return sebagai string

        return $this->response->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="laporan-harian.pdf"')
            ->setBody($pdfContent);
    }
}
