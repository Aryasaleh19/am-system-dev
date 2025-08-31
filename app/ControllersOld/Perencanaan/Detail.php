<?php 
namespace App\Controllers\Perencanaan;

use App\Controllers\BaseController;
use App\Models\Perencanaan\ProgramModel;
use App\Models\Perencanaan\KegiatanModel;
use App\Models\Perencanaan\SubKegiatanModel;
use App\Models\Perencanaan\BelanjaModel;


class Detail extends BaseController
{
    protected $modelProgram;
    protected $modelKegiatan;
    protected $modelSubkegiatan;
    protected $modelBelanja;

    public function __construct()
    {
        $this->modelProgram = new ProgramModel();
        $this->modelKegiatan = new KegiatanModel();
        $this->modelSubkegiatan = new SubKegiatanModel();
        $this->modelBelanja = new BelanjaModel();
    }

    public function index()
    {
        return view('perencanaan/detail/index', ['title' => '🗓️ Perencanaan']);
    }

    public function ajaxList()
    {
        $tahun = $this->request->getGet('tahun');

        // Kalau tidak ada parameter tahun, langsung kembalikan error
        if (empty($tahun)) {
            return $this->response->setJSON([
                'data' => [],
                'error' => 'Parameter tahun wajib diisi'
            ]);
        }

        $data = [];
        $programs = $this->modelProgram->getByTahun($tahun);

        foreach ($programs as $p) {
            $p['level']   = 'program';
            $p['id']      = $p['ID_PROGRAM'];
            $p['nama']    = $p['NAMA_PROGRAM'];
            $p['anggaran']= $p['ANGGARAN'];
            $p['tahun']   = $p['TAHUN'];
            $p['status']  = $p['STATUS'];
            $p['aksi']    = '-';
            $data[] = $p;
        }

        return $this->response->setJSON(['data' => $data]);
    }


    public function ajaxChildren()
    {
        $parent_id = $this->request->getGet('parent_id');
        $level = $this->request->getGet('level');

        $children = [];
        // ambil data program
        if ($level === 'program') {
            $kegiatans = $this->modelKegiatan->getByProgram($parent_id);
            foreach ($kegiatans as $k) {
                $k['level'] = 'kegiatan';
                $k['parent'] = $parent_id;
                $k['id'] = $k['ID_KEGIATAN'];
                $k['nama'] = $k['NAMA_KEGIATAN'];
                $k['anggaran'] = $k['ANGGARAN'] ?? null;
                $k['tahun'] = $k['TAHUN'] ?? null;
                $k['status'] = $k['STATUS'] ?? null;
                $children[] = $k;
            }
        } elseif ($level === 'kegiatan') {  // <- perhatikan penutupan kurung if sebelumnya
            $subs = $this->modelSubkegiatan->getByKegiatan($parent_id);
            foreach ($subs as $s) {
                $s['level'] = 'subkegiatan';
                $s['parent'] = $parent_id;       // optional
                $s['id'] = $s['ID_SUB'];
                $s['nama'] = $s['NAMA_SUB_KEGIATAN'];
                $s['anggaran'] = $s['ANGGARAN'] ?? null;
                $s['status'] = $s['STATUS'] ?? null;
                $s['id_kegiatan'] = $parent_id;  // tambahkan ini untuk edit
                $children[] = $s;
            }
        } elseif ($level === 'subkegiatan') {
            $belanjas = $this->modelBelanja->getBySub($parent_id);
            foreach ($belanjas as $b) {
                $b['level'] = 'belanja';
                $b['parent'] = $parent_id;
                $b['id'] = $b['ID_BELANJA'];
                $b['nama'] = $b['URAIAN_BELANJA'];
                $b['anggaran'] = $b['ANGGARAN'] ?? null;
                $b['status'] = $b['STATUS'] ?? null;
                $children[] = $b;
            }
        }

        return $this->response->setJSON(['data' => $children]);
    }

    
    // panggil modals
    public function form()
    {
        $id_sub = $this->request->getGet('id_sub');
        $id_kegiatan = $this->request->getGet('id_kegiatan');

        if (!$id_kegiatan) {
            return redirect()->back()->with('error', 'Parameter id_kegiatan wajib');
        }

        $kegiatan = $this->modelKegiatan->find($id_kegiatan);

        if ($id_sub) {
            // Edit subkegiatan
            $subkegiatan = $this->modelSubkegiatan->find($id_sub);
            $data = [
                'id_sub' => $id_sub,
                'id_kegiatan' => $id_kegiatan,
                'parent_nama' => $kegiatan['NAMA_KEGIATAN'],
                'subkegiatan' => $subkegiatan['NAMA_SUB_KEGIATAN'],
                'anggaran' => $subkegiatan['ANGGARAN'],
                'status' => $subkegiatan['STATUS']
            ];
        } else {
            // Tambah subkegiatan
            $data = [
                'id_sub' => null,
                'subkegiatan' => null,
                'anggaran' => null,
                'status' => null,
                'id_kegiatan' => $id_kegiatan,
                'parent_nama' => $kegiatan['NAMA_KEGIATAN']
            ];
        }

        return view('perencanaan/detail/modals_form', $data);
    }

    //panggil modal form kegiatan
    public function modalFormKegiatan()
    {
        $id = $this->request->getGet('id');
        $idProgram = $this->request->getGet('idProgram');
        $nama_program = $this->request->getGet('nama_program');

        if ($id) {
            $kegiatan = $this->modelKegiatan->find($id);
            if (!$kegiatan) {
                return redirect()->back()->with('error', 'Kegiatan tidak ditemukan');
            }

            $program = $this->modelProgram->find($idProgram);
            if (!$program) {
                return redirect()->back()->with('error', 'Program tidak ditemukan');
            }

            $data = [
                'id_kegiatan' => $kegiatan['ID_KEGIATAN'],
                'kegiatan' => $kegiatan['NAMA_KEGIATAN'],
                'anggaran' => $kegiatan['ANGGARAN'],
                'status' => $kegiatan['STATUS'],
                'tahun' => $kegiatan['TAHUN'],
                'program' => $program['NAMA_PROGRAM'],
                'idProgram' => $kegiatan['ID_PROGRAM']
            ];
        } else {
            // ini jalan kalau add (tidak ada $id)
            $data = [
                'id_kegiatan' => null,
                'kegiatan' => null,
                'anggaran' => null,
                'status' => null,
                'tahun' => null,
                'program' => $nama_program,
                'idProgram' => $idProgram
            ];
        }

        return view('perencanaan/detail/modals_form_kegiatan', $data);

    }
    
    // panggil modal form program
    public function modalFormProgram()
    {
        $idProgram = $this->request->getGet('idProgram');

        // ambil nama program
        $program = $this->modelProgram->find($idProgram);
        
        if ($program) {
           $data = [
                'idProgram' => $program['ID_PROGRAM'],
                'program' => $program['NAMA_PROGRAM'],
                'anggaran' => $program['ANGGARAN'],
                'tahun' => $program['TAHUN'],
                'status' => $program['STATUS'],
            ];
        }else{
            $data = [
                'idProgram' => null,
                'program' => null,
                'anggaran' => null,
                'tahun' => null,
                'status' => 1,
            ];
        }
        
        return view('perencanaan/detail/modals_form_program', $data);
    }

    public function save()
    {
        $data = [
            'ID_KEGIATAN' => $this->request->getPost('ID_KEGIATAN'),
            'NAMA_SUB_KEGIATAN' => $this->request->getPost('NAMA_SUB_KEGIATAN'),
            'ANGGARAN' => $this->request->getPost('ANGGARAN'),
            'STATUS' => $this->request->getPost('STATUS')
        ];

        $id = $this->request->getPost('id');

        try {
            if($id) {
                $this->modelSubkegiatan->update($id, $data);
                $msg = "Sub Kegiatan berhasil diperbarui!";
            } else {
                $this->modelSubkegiatan->insert($data);
                $msg = "Sub Kegiatan berhasil disimpan!";
            }

            return $this->response->setJSON(['success' => true, 'message' => $msg]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Modal form untuk belanja
    public function modalFormBelanja()
    {
        $id_sub = $this->request->getGet('id_sub');
        $id_belanja = $this->request->getGet('id_belanja');

        // Jika ada id_belanja → edit, else → add
        $belanja = $id_belanja ? $this->modelBelanja->find($id_belanja) : [];

        return view('perencanaan/detail/modals_form_belanja', [
            'id_sub' => $id_sub,
            'id_belanja' => $id_belanja,
            'belanja' => $belanja
        ]);
    }

    // Simpan belanja
    public function saveBelanja()
    {
        $data = [
            'ID_SUB'   => $this->request->getPost('ID_SUB'),
            'URAIAN_BELANJA' => $this->request->getPost('URAIAN_BELANJA'),
            'ANGGARAN' => $this->request->getPost('ANGGARAN'),
            'TANGGAL'  => $this->request->getPost('TANGGAL'),
            'STATUS'  => $this->request->getPost('STATUS')
        ];

        $this->modelBelanja->insert($data);
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Belanja berhasil ditambahkan'
        ]);
    }

    // Update belanja
    public function updateBelanja()
    {
        $id = $this->request->getPost('ID_BELANJA');
        $data = [
            'URAIAN_BELANJA' => $this->request->getPost('URAIAN_BELANJA'),
            'ANGGARAN' => $this->request->getPost('ANGGARAN'),
            'TANGGAL'  => $this->request->getPost('TANGGAL'),
            'STATUS'  => $this->request->getPost('STATUS')
        ];

        $this->modelBelanja->update($id, $data);
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Belanja berhasil diupdate'
        ]);
    }

    // Delete sub
    public function delete($id)
    {
        $this->modelSubkegiatan->delete($id);
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Sub Kegiatan berhasil dihapus'
        ]);
    }
    // Delete belanja
    public function deleteBelanja($id)
    {
        $this->modelBelanja->delete($id);
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Belanja berhasil dihapus'
        ]);
    }


}