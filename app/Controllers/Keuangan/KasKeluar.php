<?php 
namespace App\Controllers\Keuangan;


use App\Controllers\BaseController;
use App\Models\Keuangan\ModelRekeningBank;
use App\Models\Keuangan\ModelKasKeluar;
use App\Models\Referensi\ModelJenisPenerimaan;
use App\Models\Keuangan\ModelKasMasuk;
use App\Models\Referensi\ModelSekolah;
use App\Models\Referensi\ModelJenisPengeluaran;

class KasKeluar extends BaseController
{
    protected $modelRekeningBank;
    protected $modeJenisPenerimaan;
    protected $modeKasMasuk;
    protected $modelSekolah;
    protected $modelJenisPengeluaran;
    protected $modelKasKeluar;

    public function __construct()
    {
        $this->modelRekeningBank = new ModelRekeningBank();
        $this->ModelJenisPenerimaan = new ModelJenisPenerimaan();
        $this->modeKasMasuk = new ModelKasMasuk();
        $this->modelSekolah = new ModelSekolah();
        $this->modelJenisPengeluaran = new ModelJenisPengeluaran();
        $this->modelKasKeluar = new ModelKasKeluar();

    }

    public function index()
    {
     
        
        $jenis_penerimaan = $this->ModelJenisPenerimaan->getDataJenisPenerimaanYayasan()->get()->getResultArray();
        $jenis_pengeluaran = $this->modelJenisPengeluaran->findAll();
        $rekening_bank = $this->modelRekeningBank->findAll();
        $sekolah = $this->modelSekolah->findAll();
        $data = [
            'title' => '⬅️ Transaksi Kas Keluar',
            'jenis_penerimaan' => $jenis_penerimaan,
            'jenis_pengeluaran' => $jenis_pengeluaran,
            'rekening_bank' => $rekening_bank,
            'sekolah' => $sekolah
        ];
        return view('keuangan/kaskeluar/index', $data);
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
            'penerima'        => 'required|string',
            'keterangan'      => 'required|string',
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
            'ID_JENIS_PENGELUARAN' => $this->request->getPost('id_jenis'),
            'ID_KAS_BANK_PEMBAYAR' => $this->request->getPost('id_rekening'),
            'PENERIMA' => $this->request->getPost('penerima'),
            'JUMLAH' => (int) str_replace('.', '', $this->request->getPost('jumlah')),
            'BUKTI' => null, // default null, nanti diisi jika ada file
            'KETERANGAN' => $this->request->getPost('keterangan'),
            'OLEH' => session('user_id'),
            'STATUS' => 1,
        ];

        // Upload bukti jika ada
        $file = $this->request->getFile('upload');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName(); // random nama file
            $file->move(FCPATH . 'file_uploads/kaskeluar', $newName);
            $data['BUKTI'] = $newName; // pastikan huruf besar sesuai kolom
        }

        // Insert ke database
        $this->modelKasKeluar->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Kas Keluar berhasil disimpan'
        ]);
    }

    public function getriwayat()
    {
        $postData = $this->request->getPost();

        $columns = [
            0 => 'ID',
            1 => 'TANGGAL',
            2 => 'ID_JENIS_PENGELUARAN',
            3 => 'ID_KAS_BANK_PEMBAYAR',
            4 => 'PENERIMA',
            5 => 'JUMLAH',
            6 => 'BUKTI',
            7 => 'KETERANGAN',
            8 => 'OLEH',
            9 => 'STATUS',
        ];

        $start  = $postData['start'] ?? 0;
        $length = $postData['length'] ?? 10;
        $orderColumn = $columns[$postData['order'][0]['column']] ?? 'TANGGAL';
        $orderDir    = $postData['order'][0]['dir'] ?? 'desc';
        $search      = $postData['search']['value'] ?? null;

        $result = $this->modelKasKeluar->getRiwayatKasKeluar($start, $length, $search, $orderColumn, $orderDir);

        $rows = [];
        $no = $start + 1;
        foreach ($result['data'] as $row) {
            $rows[] = [
                $no++,
                $row['ID'],
                $row['TANGGAL'],
                $row['JENIS_PENGELUARAN'], // nama jenis penerimaan
                $row['PENERIMA'],
                number_format($row['JUMLAH'], 0, ',', '.'),
                $row['NO_REKENING'] . ' - ' . $row['NAMA_BANK'], // nama rekening
                $row['BUKTI'] ? "<a href='".base_url("file_uploads/kaskeluar/".$row['BUKTI'])."' target='_blank'>Lihat</a>" : '-',
                $row['KETERANGAN'],
                $row['OLEH'],
                '
                    <div class="btn-group"> 
                        <a href="'.base_url("laporan/keuangan/kwitansikaskeluar?id=".$row['ID']).'" target="_blank" class="btn btn-sm btn-outline-info">
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
            'recordsTotal' => $this->modelKasKeluar->countAll(),
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
        $kas = $this->modelKasKeluar->find($id);

        if (!$kas) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        // Cek apakah user yang input sama dengan user yang login
        if ($kas['OLEH'] != session('user_id')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak berhak menghapus transaksi ini'
            ]);
        }

        // Kalau valid, hapus
        $this->modelKasKeluar->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Transaksi berhasil dihapus'
        ]);
    }


}