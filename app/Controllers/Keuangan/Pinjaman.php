<?php 
namespace App\Controllers\Keuangan;


use App\Controllers\BaseController;
use App\Models\Keuangan\ModelRekeningBank;
use App\Models\Keuangan\ModelKasKeluar;
use App\Models\Referensi\ModelJenisPenerimaan;
use App\Models\Keuangan\ModelKasMasuk;
use App\Models\Keuangan\ModelPinjaman;
use App\Models\Keuangan\ModelPinjamanSetoran;
use App\Models\Referensi\ModelSekolah;
use App\Models\Referensi\ModelJenisPengeluaran;
use App\Models\Kepegawaian\ModelPegawai;

class Pinjaman extends BaseController
{
    protected $modelRekeningBank;
    protected $modeJenisPenerimaan;
    protected $modeKasMasuk;
    protected $modelSekolah;
    protected $modelJenisPengeluaran;
    protected $modelKasKeluar;
    protected $modelPegawai;
    protected $modelPinjaman;
    protected $modelPinjamanSetoran;

    public function __construct()
    {
        $this->modelRekeningBank = new ModelRekeningBank();
        $this->ModelJenisPenerimaan = new ModelJenisPenerimaan();
        $this->modeKasMasuk = new ModelKasMasuk();
        $this->modelSekolah = new ModelSekolah();
        $this->modelJenisPengeluaran = new ModelJenisPengeluaran();
        $this->modelKasKeluar = new ModelKasKeluar();
        $this->modelPegawai = new ModelPegawai();
        $this->modelPinjaman = new ModelPinjaman();
        $this->modelPinjamanSetoran = new ModelPinjamanSetoran();
    }

    public function getJabatanByIdPegawai()
    {
        $id = $this->request->getVar('id');
        $data = $this->modelPegawai->getJabatanByIdPegawai($id);
        echo json_encode($data);
    }
    public function getCekPinjamanPegawai()
    {
        $id = $this->request->getVar('id');
        $data = $this->modelPinjaman->cekPinjamanByIdPegawai($id);
        echo json_encode($data);
    }


    public function savePinjaman()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'id_kas'        => 'required|integer',
            'pegawai'       => 'required|integer',
            'noTransaksi'   => 'required',
            'tglTransaksi'  => 'required|valid_date',
            'jumlahPinjaman'=> 'required',
            'tenor'         => 'required|integer'
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $idKas      = $this->request->getPost('id_kas');
        $pegawaiId  = $this->request->getPost('pegawai');
        $noTransaksi= $this->request->getPost('noTransaksi');
        $tglTransaksi= $this->request->getPost('tglTransaksi');
        $keterangan= $this->request->getPost('keterangan');
        $jumlahPinjaman = str_replace('.', '', $this->request->getPost('jumlahPinjaman')); // hapus format rupiah
        $tenor      = $this->request->getPost('tenor');

        // ambil saldo kas saat ini
        $kas = $this->modelRekeningBank->find($idKas);
        if (!$kas) {
            return $this->response->setJSON([
                'status' => false,
                'message'=> 'Kas tidak ditemukan!'
            ]);
        }

        if ($jumlahPinjaman > $kas['SALDO_AKHIR']) {
            return $this->response->setJSON([
                'status' => false,
                'message'=> 'Jumlah pinjaman melebihi saldo kas!'
            ]);
        }

        // siapkan data pinjaman
        $data = [
            'ID_KAS_PEMBAYAR' => $idKas,
            'ID_PEGAWAI'      => $pegawaiId,
            'NO_TRANSAKSI'    => $noTransaksi,
            'TMT'             => $tglTransaksi,
            'JUMLAH_AKAD'     => $jumlahPinjaman,
            'SISA'            => $jumlahPinjaman,
            'TENOR'           => $tenor,
            'STATUS'          => 1, // aktif
            'CREATE_AT'       => date('Y-m-d H:i:s'),
            'OLEH'            => session()->get('user_id'), // misal pakai session user
            'KETERANGAN'      => $keterangan
        ];

        try {
            // simpan pinjaman
            $this->modelPinjaman->insert($data);

            // update saldo kas
            $this->modelRekeningBank->update($idKas, [
                'SALDO_AKHIR' => $kas['SALDO_AKHIR'] - $jumlahPinjaman
            ]);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Pinjaman berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal menyimpan pinjaman: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePinjaman()
    {
        $id = $this->request->getPost('id');

        if (!$id) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'ID pinjaman tidak ditemukan!'
            ]);
        }

        try {
            $this->modelPinjaman->delete($id);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Pinjaman berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

     public function getDataSetoranByIdPinjaman() {
        $idpinjaman = $this->request->getVar('idpinjaman');
        $dataSetoran = $this->modelPinjamanSetoran->cekSetoranByPinjaman($idpinjaman);
        return $this->response->setJSON($dataSetoran);
    }


    public function simpanSetoranPinjaman() 
    {
        $request = $this->request;

        $data = [
            'NO_TRANSAKSI' => $request->getVar('noTransaksi'),
            'ID_PINJAMAN' => $request->getVar('idpinjaman'), // sesuaikan bila ID_PINJAMAN berbeda dari pegawai
            'ID_KAS_PENERIMA' => $request->getVar('idKas'),
            'TANGGAL' => $request->getVar('tglTransaksi'),
            'TENOR_KE' => $request->getVar('tenor'),
            'BULAN' => date('m', strtotime($request->getVar('blnBayar'))),
            'TAHUN' => date('Y', strtotime($request->getVar('blnBayar'))),
            'JUMLAH_SETOR' => $request->getVar('jumlah'),
            'OLEH' => session()->get('user_id'),
            'STATUS' => 1,
            'CREATE_AT' => date('Y-m-d H:i:s')
        ];

        try {
            $this->modelPinjamanSetoran->insert($data);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Setoran berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan setoran: ' . $e->getMessage()
            ]);
        }
    }


    public function deleteSetoranByIdSetoran()
    {
        $idSetoran = $this->request->getPost('idSetoran');
        $userId = session()->get('user_id'); // ambil ID user yang login

        // Ambil data setoran dari DB
        $setoran = $this->modelPinjamanSetoran->find($idSetoran);

        if (!$setoran) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data setoran tidak ditemukan'
            ]);
        }

        // Cek apakah user yang login sama dengan OLEH
        if ($setoran['OLEH'] != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak berhak menghapus data ini'
            ]);
        }

        // Jika lolos pengecekan, hapus setoran
        $this->modelPinjamanSetoran->delete($idSetoran);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Setoran berhasil dihapus'
        ]);
    }

}