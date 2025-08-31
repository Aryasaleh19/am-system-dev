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

class LaporanKas extends BaseController
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

    public function LaporanPenerimaanKas()
    {
        $tanggal = $this->request->getGet('tglAwal');   // ex: 2025-01-01
        $tanggal2 = $this->request->getGet('tglAkhir');   // ex: 2025-01-01
        $bank  = $this->request->getGet('bank');   // pegawai_id atau null

        // Ambil nama nama bank dari model bank
        if ($bank) {
            $model = $this->modelRekeningBank;
            $namaBank = $model->find($bank);
            $getNamaBank = $namaBank['NAMA_BANK'] ?? 'SEMUA KAS';
        } else {
            $getNamaBank = 'SEMUA KAS';
        }

        // Ambil semua data sekaligus (header ada di tiap baris)
        $query = $this->db->query("CALL CetakLaporanKasKeluarMasuk(?, ?, ?)", [
            $tanggal,
            $tanggal2,
            $bank ?: NULL
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
            'header'        => $header,
            'transaksi'     => $transaksi,
            'rekap'         => $rekap,
            'tanggal_awal'  => date_indo($tanggal),
            'tanggal_akhir'  => date_indo($tanggal2),
            'nama_kas'      => $getNamaBank,
        ];

        // Render HTML view laporan
        $html = view('laporan/keuangan/laporan-penerimaan-kas', $data);

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
        $mpdf->SetWatermarkText('Al-Muhajirin System');
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.1;


        // Tulis HTML
        $mpdf->WriteHTML($html);

        // footer    
        $mpdf->SetHTMLFooter('Laporan Transaksi Kas Masuk - Hal. {PAGENO} / {nb} | Dicetak: {DATE j F Y}');
        // Output PDF ke browser
        $pdfContent = $mpdf->Output('', 'S'); // return sebagai string

        return $this->response->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="laporan-harian.pdf"')
            ->setBody($pdfContent);
    }



    public function LaporanPengeluaranKas()
    {
        $tanggal = $this->request->getGet('tglAwal');   // ex: 2025-01-01
        $tanggal2 = $this->request->getGet('tglAkhir');   // ex: 2025-01-01
        $bank  = $this->request->getGet('bank');   // pegawai_id atau null

        // Ambil nama nama bank dari model bank
        if ($bank) {
            $model = $this->modelRekeningBank;
            $namaBank = $model->find($bank);
            $getNamaBank = $namaBank['NAMA_BANK'] ?? 'SEMUA KAS';
        } else {
            $getNamaBank = 'SEMUA KAS';
        }

        // Ambil semua data sekaligus (header ada di tiap baris)
        $query = $this->db->query("CALL CetakLaporanKasKeluarMasuk(?, ?, ?)", [
            $tanggal,
            $tanggal2,
            $bank ?: NULL
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
            'header'        => $header,
            'transaksi'     => $transaksi,
            'rekap'         => $rekap,
            'tanggal_awal'  => date_indo($tanggal),
            'tanggal_akhir'  => date_indo($tanggal2),
            'nama_kas'      => $getNamaBank,
        ];

        // Render HTML view laporan
        $html = view('laporan/keuangan/laporan-pengeluaran-kas', $data);

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
        $mpdf->SetWatermarkText('Al-Muhajirin System');
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.1;


        // Tulis HTML
        $mpdf->WriteHTML($html);

        // footer    
        $mpdf->SetHTMLFooter('Laporan Transaksi Kas Keluar - Hal. {PAGENO} / {nb} | Dicetak: {DATE j F Y}');
        // Output PDF ke browser
        $pdfContent = $mpdf->Output('', 'S'); // return sebagai string

        return $this->response->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="laporan-harian.pdf"')
            ->setBody($pdfContent);
    }
}
