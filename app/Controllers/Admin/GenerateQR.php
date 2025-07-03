<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\KelasModel;
use App\Models\SiswaModel;

class GenerateQR extends BaseController
{
  protected SiswaModel $siswaModel;
  protected KelasModel $kelasModel;

  public function __construct()
  {
    $this->siswaModel = new SiswaModel();
    $this->kelasModel = new KelasModel();
  }

  public function index()
  {
    $siswa = $this->siswaModel->getAllSiswaWithKelas();
    $kelas = $this->kelasModel->getDataKelas();

    $data = [
      'title' => 'Generate QR Code',
      'ctx' => 'qr',
      'siswa' => $siswa,
      'kelas' => $kelas,
    ];

    return view('admin/generate-qr/generate-qr', $data);
  }

  public function getSiswaByKelas()
  {
    $idKelas = $this->request->getVar('idKelas');

    $siswa = $this->siswaModel->getSiswaByKelas($idKelas);

    return $this->response->setJSON($siswa);
  }
}
