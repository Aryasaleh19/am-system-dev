<?php 
namespace App\Controllers\Perencanaan;

use App\Controllers\BaseController;
use App\Models\Referensi\ModelAgama;

class Belanja extends BaseController
{
    protected $modelAgama;

    public function __construct()
    {
        $this->modelAgama = new ModelAgama();
    }

    public function index()
    {
        return view('perencanaan/kegiatan/index', ['title' => '💰 Perencanaan Belanja']);
    }
}