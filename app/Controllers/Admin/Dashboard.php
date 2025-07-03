<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\PetugasModel;
use App\Models\PresensiSiswaModel;
use CodeIgniter\I18n\Time;
use Config\AbsensiSekolah as ConfigAbsensiSekolah;

class Dashboard extends BaseController
{
  protected SiswaModel $siswaModel;

  protected KelasModel $KelasModel;

  protected PresensiSiswaModel $presensiSiswaModel;

  protected PetugasModel $petugasModel;

  public function __construct()
  {
    $this->siswaModel = new SiswaModel();
    $this->KelasModel = new KelasModel();
    $this->presensiSiswaModel = new PresensiSiswaModel();
    $this->petugasModel = new PetugasModel();
  }

  public function index()
  {
    $now = Time::now();

    $dateRange = [];
    $siswaKehadiranArray = [];

    for ($i = 6; $i >= 0; $i--) {
      $date = $now->subDays($i)->toDateString();
      if ($i == 0) {
        $formattedDate = "Hari ini";
      } else {
        $t = $now->subDays($i);
        $formattedDate = "{$t->getDay()} " . substr($t->toFormattedDateString(), 0, 3);
      }
      array_push($dateRange, $formattedDate);
      array_push(
        $siswaKehadiranArray,
        count($this->presensiSiswaModel
          ->join('tb_siswa', 'tb_presensi_siswa.id_siswa = tb_siswa.id_siswa', 'left')
          ->where(['tb_presensi_siswa.tanggal' => "$date", 'tb_presensi_siswa.id_kehadiran' => '1'])->findAll())
      );
    }

    $today = $now->toDateString();

    $data = [
      'title' => 'Dashboard',
      'ctx' => 'dashboard',

      'siswa' => $this->siswaModel->getAllSiswaWithKelas(),

      'kelas' => $this->KelasModel->getDataKelas(),

      'dateRange' => $dateRange,
      'dateNow' => $now->toLocalizedString('d MMMM Y'),

      'grafikKehadiranSiswa' => $siswaKehadiranArray,

      'jumlahKehadiranSiswa' => [
        'hadir' => count($this->presensiSiswaModel->getPresensiByKehadiran('1', $today)),
        'sakit' => count($this->presensiSiswaModel->getPresensiByKehadiran('2', $today)),
        'izin' => count($this->presensiSiswaModel->getPresensiByKehadiran('3', $today)),
        'alfa' => count($this->presensiSiswaModel->getPresensiByKehadiran('4', $today))
      ],

      'petugas' => $this->petugasModel->getAllPetugas(),
    ];

    return view('admin/dashboard', $data);
  }
}
