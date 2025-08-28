<?php

namespace App\Models\Referensi;

use CodeIgniter\Model;

class ModelGedung extends Model
{
    protected $table      = 'm_r_gedung';
    protected $primaryKey = 'ID';

    protected $allowedFields = ['GEDUNG', 'STATUS'];

    protected $useTimestamps = false;

    public function countAllRecords()
    {
        return $this->countAllResults(false);
    }

    public function countFilteredRecords($searchValue)
    {
        $builder = $this->builder();
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('GEDUNG', $searchValue)
                    ->orLike('STATUS', $searchValue)
                    ->groupEnd();
        }
        return $builder->countAllResults(false);
    }

    public function getDatatables($start, $length, $searchValue, $orderColumn, $orderDir)
    {
        $builder = $this->builder();

        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('GEDUNG', $searchValue)
                    ->orLike('STATUS', $searchValue)
                    ->groupEnd();
        }

        $columns = ['ID', 'GEDUNG', 'STATUS'];
        if (isset($columns[$orderColumn])) {
            $builder->orderBy($columns[$orderColumn], $orderDir);
        }

        $builder->limit($length, $start);

        $list = $builder->get()->getResultArray();

        $db = \Config\Database::connect();
        foreach ($list as &$item) {
            $item['JML_RUANGAN'] = $db->table('m_r_ruangan')
                                      ->where('GEDUNG_ID', $item['ID'])
                                      ->countAllResults();
        }

        return $list;
    }
}