<?php 
namespace App\Controllers\Pengaturan;

use App\Controllers\BaseController;
use App\Models\Pengaturan\ModelModul;
use App\Models\Pengaturan\Model_group_modul;
use App\Models\Pengaturan\Model_maping_modul;

class ManagemenModul extends BaseController
{
    protected $modulModel;
    protected $groupModel;

    public function __construct()
    {
        $this->modulModel = new ModelModul();
        $this->groupModel = new Model_group_modul();
        $this->mapingModel = new Model_maping_modul();
    }

    public function index()
    {
        $moduls = $this->modulModel->where('STATUS', 1)->findAll();
        $groups = $this->groupModel->where('STATUS', 1)->findAll();

        return view('pengaturan/managemenmodul/index', [
            'title'   => '🔗 Managemen Modul',
            'moduls'  => $moduls,
            'groups'  => $groups
        ]);
    }

    public function mapModul()
    {
        $json = $this->request->getJSON(true);
        $modulId = $json['modul_id'];
        $groupId = $json['group_id'];

        $model = new Model_maping_modul();
        $exists = $model->where(['MODUL_ID' => $modulId, 'GROUP_ID' => $groupId])->first();

        if (!$exists) {
            $model->insert([
                'MODUL_ID' => $modulId,
                'GROUP_ID' => $groupId,
                'STATUS' => 1
            ]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function getMappedModul($groupId)
    {
        $modelMap = new Model_maping_modul();

        $mapped = $modelMap->select('m_modul_maping.ID as mapping_id, m_modul.ID, m_modul.MODUL, m_modul_maping.STATUS')
            ->join('m_modul', 'm_modul_maping.MODUL_ID = m_modul.ID')
            ->where('GROUP_ID', $groupId)
            ->findAll();

        return $this->response->setJSON($mapped);
    }


    public function updateStatusMapping()
    {
        $json = $this->request->getJSON(true);

        $mappingId = $json['mapping_id'] ?? null;
        $status = $json['status'] ?? null;

        if (!$mappingId || !in_array($status, [0, 1])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak valid']);
        }

        $modelMap = new Model_maping_modul();

        $mapping = $modelMap->find($mappingId);
        if (!$mapping) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Mapping tidak ditemukan']);
        }

        $modelMap->update($mappingId, ['STATUS' => $status]);

        return $this->response->setJSON(['status' => 'success']);
    }

}

?>