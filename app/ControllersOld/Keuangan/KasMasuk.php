<?php 
namespace App\Controllers\Keuangan;

use App\Controllers\BaseController;
use App\Models\Keuangan\ModelRekeningBank;
use App\Models\Referensi\ModelJenisPenerimaan;
use App\Models\Keuangan\ModelKasMasuk;
use App\Models\Referensi\ModelSekolah;

class KasMasuk extends BaseController
{
    protected $modelRekeningBank;
    protected $modeJenisPenerimaan;
    protected $modeKasMasuk;
    protected $modelSekolah;

    public function __construct()
    {
        $this->modelRekeningBank = new ModelRekeningBank();
        $this->ModelJenisPenerimaan = new ModelJenisPenerimaan();
        $this->modeKasMasuk = new ModelKasMasuk();
        $this->modelSekolah = new ModelSekolah();

    }


    public function index()
    {
        $jenis_penerimaan = $this->ModelJenisPenerimaan->getDataJenisPenerimaanYayasan()->get()->getResultArray();
        $rekening_bank = $this->modelRekeningBank->findAll();
        $sekolah = $this->modelSekolah->findAll();
        $data = [
            'title' => '➡️ Transaksi Kas Masuk',
            'jenis_penerimaan' => $jenis_penerimaan,
            'rekening_bank' => $rekening_bank,
            'sekolah' => $sekolah
        ];
        return view('keuangan/kasmasuk/index', $data);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nomor_transaksi' => 'required',
            'tanggal'         => 'required|valid_date[Y-m-d]',
            'id_jenis'        => 'required|integer',
            'id_rekening'     => 'required|integer',
            'dari'            => 'required|string',
            'jumlah'          => 'required|numeric',
            'upload'          => 'permit_empty|uploaded[upload]|max_size[upload,2048]|ext_in[upload,jpg,jpeg,png,pdf]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors()
            ]);
        }

        // Persiapkan data insert
        $data = [
            'ID' => $this->request->getPost('nomor_transaksi'),
            'TANGGAL' => $this->request->getPost('tanggal'),
            'ID_JENIS_PENERIMAAN' => $this->request->getPost('id_jenis'),
            'ID_KAS_BANK_TERIMA' => $this->request->getPost('id_rekening'),
            'DITERIMA_DARI' => $this->request->getPost('dari'),
            'JUMLAH' => (int) str_replace('.', '', $this->request->getPost('jumlah')),
            'BUKTI' => null, // default null, nanti diisi jika ada file
            'OLEH' => session('user_id'),
            'STATUS' => 1,
        ];

        // Upload bukti jika ada
        $file = $this->request->getFile('upload');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName(); // random nama file
            $file->move(FCPATH . 'file_uploads/kasmasuk', $newName);
            $data['BUKTI'] = $newName; // pastikan huruf besar sesuai kolom
        }

        // Insert ke database
        $this->modeKasMasuk->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Kas Masuk berhasil disimpan'
        ]);
    }

    public function getriwayat()
    {
        $postData = $this->request->getPost();

        $columns = [
            0 => 'ID',
            1 => 'ID',
            2 => 'TANGGAL',
            3 => 'ID_JENIS_PENERIMAAN',
            4 => 'DITERIMA_DARI',
            5 => 'JUMLAH',
            6 => 'DITERIMA_DARI',
            7 => 'BUKTI',
            8 => 'OLEH',
        ];

        $start  = $postData['start'] ?? 0;
        $length = $postData['length'] ?? 10;
        $orderColumn = $columns[$postData['order'][0]['column']] ?? 'TANGGAL';
        $orderDir    = $postData['order'][0]['dir'] ?? 'desc';
        $search      = $postData['search']['value'] ?? null;

        $result = $this->modeKasMasuk->getRiwayatKasmasuk($start, $length, $search, $orderColumn, $orderDir);

        $rows = [];
        $no = $start + 1;
        foreach ($result['data'] as $row) {
            $rows[] = [
                $no++,
                $row['ID'],
                $row['TANGGAL'],
                $row['JENIS_PENERIMAAN'], // nama jenis penerimaan
                $row['DITERIMA_DARI'],
                number_format($row['JUMLAH'], 0, ',', '.'),
                $row['NO_REKENING'] . ' - ' . $row['NAMA_BANK'], // nama rekening
                $row['BUKTI'] ? "<a href='".base_url("file_uploads/kasmasuk/".$row['BUKTI'])."' target='_blank'>Lihat</a>" : '-',
                $row['OLEH'],
                '
                    <div class="btn-group"> 
                        <a href="'.base_url("laporan/keuangan/kwitansikasmasuk?id=".$row['ID']).'" target="_blank" class="btn btn-sm btn-outline-info">
                        Cetak
                        </a>
                        <button type="button" 
                                class="btn btn-sm btn-outline-danger btn-delete" 
                                data-id="'.$row['ID'].'">
                            Hapus
                        </button>
                    </div>
                '
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($postData['draw']),
            'recordsTotal' => $this->modeKasMasuk->countAll(),
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $rows
        ]);
    }

    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $id = $this->request->getPost('id');
        $data = $this->modeKasMasuk->find($id);

        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        // Validasi: hanya user yang input yang boleh hapus
        if ($data['OLEH'] != session('user_id')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak berhak menghapus transaksi ini'
            ]);
        }

        // Hapus file bukti jika ada
        if (!empty($data['BUKTI']) && file_exists(FCPATH . 'file_uploads/kasmasuk/' . $data['BUKTI'])) {
            unlink(FCPATH . 'file_uploads/kasmasuk/' . $data['BUKTI']);
        }

        // Hapus data
        $this->modeKasMasuk->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }






}