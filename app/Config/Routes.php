<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dashboard::index');
$routes->get('dashboard', 'Dashboard::index');

$routes->get('login', 'Login::index');
$routes->post('login', 'Login::proses');
$routes->get('logout', 'Login::logout');

$routes->group('pengaturan', function ($routes) {
    $routes->get('group_modul', 'Pengaturan\Group_modul::index');
    $routes->get('group_modul/ajaxList', 'Pengaturan\Group_modul::ajaxList');
    $routes->get('group_modul/form', 'Pengaturan\Group_modul::form');
    $routes->post('group_modul/simpan', 'Pengaturan\Group_modul::simpan');
    $routes->post('group_modul/store', 'Pengaturan\Group_modul::store');
    $routes->post('group_modul/update', 'Pengaturan\Group_modul::update');
    $routes->get('group_modul/get/(:num)', 'Pengaturan\Group_modul::get/$1');
    $routes->get('group_modul/delete/(:num)', 'Pengaturan\Group_modul::delete/$1');

    // moduls
    $routes->get('modul', 'Pengaturan\Modul::index');
    $routes->get('modul/ajaxList', 'Pengaturan\Modul::ajaxList');
    $routes->get('modul/form', 'Pengaturan\Modul::form');
    $routes->post('modul/simpan', 'Pengaturan\Modul::simpan');
    $routes->post('modul/store', 'Pengaturan\Modul::store');
    $routes->post('modul/update', 'Pengaturan\Modul::update');
    $routes->get('modul/get/(:num)', 'Pengaturan\Modul::get/$1');
    $routes->get('modul/delete/(:num)', 'Pengaturan\Modul::delete/$1');

    // profil lembaga
    $routes->get('profil', 'Pengaturan\Profil::index');
    $routes->post('profil/update', 'Pengaturan\Profil::update');

    // maping modul / managemen modul
    $routes->get('managemen-modul', 'Pengaturan\ManagemenModul::index');
    $routes->post('managemen-modul/mapModul', 'Pengaturan\ManagemenModul::mapModul');
    $routes->get('managemen-modul/mappedModul/(:num)', 'Pengaturan\ManagemenModul::getMappedModul/$1');
    $routes->post('managemen-modul/updateStatusMapping', 'Pengaturan\ManagemenModul::updateStatusMapping');
});

$routes->group('referensi', function ($routes) {
    $routes->get('gedung', 'Referensi\Gedung::index');
    $routes->get('gedung/ajaxList', 'Referensi\Gedung::ajaxList');
    $routes->get('gedung/form', 'Referensi\Gedung::form');
    $routes->post('gedung/simpan', 'Referensi\Gedung::simpan');
    $routes->post('gedung/store', 'Referensi\Gedung::store');
    $routes->post('gedung/update', 'Referensi\Gedung::update');
    $routes->get('gedung/get/(:num)', 'Referensi\Gedung::get/$1');
    $routes->get('gedung/mapingruangan/(:num)', 'Referensi\Gedung::get/$1');
    $routes->get('gedung/formMaping', 'Referensi\Gedung::formMaping');
    $routes->get('gedung/delete/(:num)', 'Referensi\Gedung::delete/$1');
});

// Ruangan
$routes->group('referensi/ruangan', function ($routes) {
    $routes->post('simpan', 'Referensi\Gedung::simpanRuangan');
    $routes->post('update', 'Referensi\Gedung::updateRuangan');
    $routes->get('ajaxListRuangan/(:num)', 'Referensi\Gedung::ajaxListRuangan/$1');
    $routes->get('get/(:num)', 'Referensi\Gedung::getRuangan/$1');
    $routes->get('delete/(:num)', 'Referensi\Gedung::deleteRuangan/$1');
});

// Sekolah
$routes->group('referensi/sekolah', function ($routes) {
    $routes->get('/', 'Referensi\Sekolah::index');
    $routes->get('form', 'Referensi\Sekolah::form');
    $routes->post('simpan', 'Referensi\Sekolah::simpan');
    $routes->post('update', 'Referensi\Sekolah::update');
    $routes->get('ajaxList', 'Referensi\Sekolah::ajaxList');
    $routes->get('get/(:num)', 'Referensi\Sekolah::get/$1');
    $routes->get('delete/(:num)', 'Referensi\Sekolah::delete/$1');
});
// Agama
$routes->group('referensi/agama', function ($routes) {
    $routes->get('/', 'Referensi\Agama::index');
    $routes->get('form', 'Referensi\Agama::form');
    $routes->post('simpan', 'Referensi\Agama::simpan');
    $routes->post('update', 'Referensi\Agama::update');
    $routes->get('ajaxList', 'Referensi\Agama::ajaxList');
    $routes->get('get/(:num)', 'Referensi\Agama::get/$1');
    $routes->get('delete/(:num)', 'Referensi\Agama::delete/$1');
});
// Jabatan
$routes->group('referensi/jabatan', function ($routes) {
    $routes->get('/', 'Referensi\Jabatan::index');
    // halaman halaman
    $routes->get('penerimaan', 'Referensi\Jabatan::penerimaan');
    $routes->get('tupoksi', 'Referensi\Jabatan::tupoksi');
    $routes->get('absensi', 'Referensi\Jabatan::absensi');

    // proses pengaturan absensi
    $routes->post('saveAbsensi', 'Referensi\Jabatan::saveAbsensi'); // <-- save absensi

    // proses jabatan
    $routes->get('form', 'Referensi\Jabatan::form');
    $routes->post('simpan', 'Referensi\Jabatan::simpan');
    $routes->post('update', 'Referensi\Jabatan::update');
    $routes->get('ajaxList', 'Referensi\Jabatan::ajaxList');
    $routes->get('get/(:num)', 'Referensi\Jabatan::get/$1');
    $routes->get('delete/(:num)', 'Referensi\Jabatan::delete/$1');


    // pengaturan penerimaan
    $routes->get('formPengaturan', 'Referensi\Jabatan::formPengaturan');
    $routes->post('simpanPenerimaanJabatan', 'Referensi\Jabatan::simpanPenerimaanJabatan');
    $routes->post('updatePenerimaan', 'Referensi\Jabatan::updatePenerimaan');
    $routes->get('ajaxListPenerimaan', 'Referensi\Jabatan::ajaxListPenerimaan');
    $routes->get('deletePenerimaan/(:num)', 'Referensi\Jabatan::deletePenerimaan/$1');

    // pengaturan tupoksi
    $routes->get('ajaxListTupoksi', 'Referensi\Jabatan::ajaxListTupoksi');
    $routes->post('simpanTupoksiJabatan', 'Referensi\Jabatan::simpanTupoksiJabatan');
    $routes->post('updateTupoksi', 'Referensi\Jabatan::updateTupoksi');
    $routes->get('deleteTupoksi/(:num)', 'Referensi\Jabatan::deleteTupoksi/$1');
});
// Profesi
$routes->group('referensi/profesi', function ($routes) {
    $routes->get('/', 'Referensi\Profesi::index');
    $routes->get('form', 'Referensi\Profesi::form');
    $routes->post('simpan', 'Referensi\Profesi::simpan');
    $routes->post('update', 'Referensi\Profesi::update');
    $routes->get('ajaxList', 'Referensi\Profesi::ajaxList');
    $routes->get('get/(:num)', 'Referensi\Profesi::get/$1');
    $routes->get('delete/(:num)', 'Referensi\Profesi::delete/$1');
});

// Jenis Pegawai
$routes->group('referensi/jenispegawai', function ($routes) {
    $routes->get('/', 'Referensi\Jenispegawai::index');
    $routes->get('form', 'Referensi\Jenispegawai::form');
    $routes->post('simpan', 'Referensi\Jenispegawai::simpan');
    $routes->post('update', 'Referensi\Jenispegawai::update');
    $routes->get('ajaxList', 'Referensi\Jenispegawai::ajaxList');
    $routes->get('get/(:num)', 'Referensi\Jenispegawai::get/$1');
    $routes->get('delete/(:num)', 'Referensi\Jenispegawai::delete/$1');
});
// Jenis Penerimaan
$routes->group('referensi/jenispenerimaan', function ($routes) {
    $routes->get('/', 'Referensi\JenisPenerimaan::index');
    $routes->get('form', 'Referensi\JenisPenerimaan::form');
    $routes->post('simpan', 'Referensi\JenisPenerimaan::simpan');
    $routes->post('update', 'Referensi\JenisPenerimaan::update');
    $routes->get('ajaxList', 'Referensi\JenisPenerimaan::ajaxList');
    $routes->get('get/(:num)', 'Referensi\JenisPenerimaan::get/$1');
    $routes->get('delete/(:num)', 'Referensi\JenisPenerimaan::delete/$1');
});

$routes->group('api/wilayah', function ($routes) {
    $routes->get('provinsi', 'Api\Wilayah::provinsi');
    $routes->get('kabupaten/(:num)', 'Api\Wilayah::kabupaten/$1');
    $routes->get('kecamatan/(:num)', 'Api\Wilayah::kecamatan/$1');
    $routes->get('kelurahan/(:num)', 'Api\Wilayah::kelurahan/$1');
});
$routes->group('api/referensi', function ($routes) {
    $routes->get('agama', 'Api\Referensi::agama');
    $routes->get('angkatan', 'Api\Referensi::angkatan');
    $routes->get('sekolah', 'Api\Referensi::sekolah');
    $routes->get('ruangan', 'Api\Referensi::ruangan');
    $routes->get('koplaporan', 'Api\Referensi::koplaporan');
});

// Kepegawaian (pegawai)
$routes->group('kepegawaian/pegawai', function ($routes) {
    $routes->get('/', 'Kepegawaian\Pegawai::index');
    $routes->get('form', 'Kepegawaian\Pegawai::form');
    $routes->post('simpan', 'Kepegawaian\Pegawai::simpan');
    $routes->post('update', 'Kepegawaian\Pegawai::update');

    $routes->get('ajaxList', 'Kepegawaian\Pegawai::ajaxList');
    $routes->get('get/(:num)', 'Kepegawaian\Pegawai::get/$1');
    $routes->get('delete/(:num)', 'Kepegawaian\Pegawai::delete/$1');

    // untuk akun
    $routes->get('formakun', 'Kepegawaian\Pegawai::formakun');
    $routes->post('simpan_akun', 'Kepegawaian\Pegawai::simpan_akun');
    $routes->get('get_akun/(:num)', 'Kepegawaian\Pegawai::get_akun/$1');
});
// Kepegawaian (pegawai)
$routes->group('kepegawaian/managemenakses', function ($routes) {
    $routes->get('/', 'Kepegawaian\Managemenakses::index');
    $routes->get('ajaxList', 'Kepegawaian\Managemenakses::ajaxList');
    $routes->get('formakun', 'Kepegawaian\Managemenakses::formakun');
    $routes->get('get_akun/(:num)', 'Kepegawaian\Managemenakses::get_akun/$1');
    $routes->post('save_akses', 'Kepegawaian\Managemenakses::save_akses');
});
// Kepegawaian (absensi)
$routes->group('kepegawaian/absensi', function ($routes) {
    $routes->get('/', 'Kepegawaian\Absensi::index');
});
// Kesiswaan (angkatan)
$routes->group('kesiswaan/angkatan', function ($routes) {
    $routes->get('/', 'Kesiswaan\Angkatan::index');
    $routes->get('ajaxList', 'Kesiswaan\Angkatan::ajaxList');
    $routes->get('form', 'Kesiswaan\Angkatan::form');
    $routes->get('get/(:num)', 'Kesiswaan\Angkatan::get/$1');
    $routes->get('delete/(:num)', 'Kesiswaan\Angkatan::delete/$1');
    $routes->post('update', 'Kesiswaan\Angkatan::update');
    $routes->post('save', 'Kesiswaan\Angkatan::save');
});

// Kesiswaan (siswa)
$routes->group('kesiswaan/siswa', function ($routes) {
    $routes->get('/', 'Kesiswaan\Siswa::index');
    $routes->get('ajaxList', 'Kesiswaan\Siswa::ajaxList');
    $routes->get('formsiswabaru', 'Kesiswaan\Siswa::formsiswabaru');
    $routes->get('get/(:segment)', 'Kesiswaan\Siswa::get/$1');
    $routes->post('delete/(:segment)', 'Kesiswaan\Siswa::delete/$1');
    $routes->post('update', 'Kesiswaan\Siswa::update');
    $routes->post('save', 'Kesiswaan\Siswa::save');
    $routes->post('mapJenisPembayaran', 'Kesiswaan\Siswa::mapJenisPembayaran');
    $routes->post('batalMapJenisPembayaran', 'Kesiswaan\Siswa::batalMapJenisPembayaran');
    $routes->post('aktifkanMapJenisPembayaran', 'Kesiswaan\Siswa::aktifkanMapJenisPembayaran');

    // Detail siswa
    $routes->get('getDetail/(:segment)', 'Kesiswaan\Siswa::getDetail/$1');
    $routes->get('modaldetailsiswa', 'Kesiswaan\Siswa::modaldetailsiswa');
    $routes->get('detail/berkas', 'Kesiswaan\Siswa::detailBerkas');
    $routes->get('detail/orangtua', 'Kesiswaan\Siswa::detailOrangtua');
    $routes->get('detail/profile', 'Kesiswaan\Siswa::detailProfile');
    $routes->get('detail/pendaftaran', 'Kesiswaan\Siswa::pendaftaran');
    $routes->get('detail/prestasi', 'Kesiswaan\Siswa::prestasi');

    // detail siswa (pembayaran dan map pembayaran)
    $routes->get('detail/pembayaran', 'Kesiswaan\Siswa::detailPembayaran');
    $routes->post('detail/updatetenor', 'Kesiswaan\Siswa::updatetenor');

    // pendaftaran sekolah
    $routes->get('riwayatSekolahTable', 'Kesiswaan\Siswa::riwayatSekolahTable');
    $routes->post('savePendaftaranSekolah', 'Kesiswaan\Siswa::savePendaftaranSekolah');
    $routes->post('updatestatussekolah', 'Kesiswaan\Siswa::updatestatussekolah');
    $routes->post('hapusRiwayatSekolah', 'Kesiswaan\Siswa::hapusRiwayatSekolah');

    // prestasi kenaikan kelas
    $routes->get('getRiwayatById', 'Kesiswaan\Siswa::getRiwayatById');
    $routes->get('riwayatPrestasi', 'Kesiswaan\Siswa::riwayatPrestasi');
    $routes->post('savePrestasi', 'Kesiswaan\Siswa::savePrestasi');
    $routes->post('updatePrestasi', 'Kesiswaan\Siswa::updatePrestasi');
    $routes->post('deletePrestasi', 'Kesiswaan\Siswa::deletePrestasi');
});

// Keuangan (jenis pengeluaran)
$routes->group('referensi/jenispengeluaran', function ($routes) {
    $routes->get('/', 'Referensi\JenisPengeluaran::index');
    $routes->get('form', 'Referensi\JenisPengeluaran::form');
    $routes->post('simpan', 'Referensi\JenisPengeluaran::simpan');
    $routes->post('update', 'Referensi\JenisPengeluaran::update');
    $routes->get('ajaxList', 'Referensi\JenisPengeluaran::ajaxList');
    $routes->get('get/(:num)', 'Referensi\JenisPengeluaran::get/$1');
    $routes->get('delete/(:num)', 'Referensi\JenisPengeluaran::delete/$1');
});
// Keuangan (Rekening Bank)
$routes->group('keuangan/rekeningbank', function ($routes) {
    $routes->get('/', 'Keuangan\RekeningBank::index');
    $routes->get('form', 'Keuangan\RekeningBank::form');
    $routes->post('save', 'Keuangan\RekeningBank::save');
    $routes->post('update', 'Keuangan\RekeningBank::update');
    $routes->get('ajaxList', 'Keuangan\RekeningBank::ajaxList');
    $routes->get('get/(:num)', 'Keuangan\RekeningBank::get/$1');
    $routes->get('delete/(:num)', 'Keuangan\RekeningBank::delete/$1');
});
// Keuangan (Kas Masuk)
$routes->group('keuangan/kasmasuk', function ($routes) {
    $routes->get('/', 'Keuangan\KasMasuk::index');
    $routes->post('save', 'Keuangan\KasMasuk::save');
    $routes->post('getriwayat', 'Keuangan\KasMasuk::getriwayat');
    $routes->post('delete', 'Keuangan\KasMasuk::delete');
});
// Keuangan (Kas Keluar)
$routes->group('keuangan/kaskeluar', function ($routes) {
    $routes->get('/', 'Keuangan\KasKeluar::index');
    $routes->post('save', 'Keuangan\KasKeluar::save');
    $routes->post('getriwayat', 'Keuangan\KasKeluar::getriwayat');
    $routes->post('delete', 'Keuangan\KasKeluar::delete');
});
// Keuangan (Penggajian)
$routes->group('keuangan/penggajian', function ($routes) {
    $routes->get('getJabatanByIdPegawai', 'Keuangan\Penggajian::getJabatanByIdPegawai');
    $routes->get('getPenerimaanByIdPegawai', 'Keuangan\Penggajian::getPenerimaanByIdPegawai');
    $routes->post('save', 'Keuangan\Penggajian::save');
    $routes->post('getriwayat', 'Keuangan\Penggajian::getriwayat');
    $routes->post('delete', 'Keuangan\Penggajian::delete');
});
// Keuangan (Pinjaman)
$routes->group('keuangan/pinjaman', function ($routes) {
    $routes->get('getCekPinjamanPegawai', 'Keuangan\Pinjaman::getCekPinjamanPegawai');
    $routes->post('savePinjaman', 'Keuangan\Pinjaman::savePinjaman');
    $routes->post('deletePinjaman', 'Keuangan\Pinjaman::deletePinjaman');

    // setoran pinjaman
    $routes->get('getDataSetoranByIdPinjaman', 'Keuangan\Pinjaman::getDataSetoranByIdPinjaman'); //<- riwayat setoran
    $routes->post('simpanSetoranPinjaman', 'Keuangan\Pinjaman::simpanSetoranPinjaman');
    $routes->post('deleteSetoranByIdSetoran', 'Keuangan\Pinjaman::deleteSetoranByIdSetoran');
});


// Keuangan (Pemabayaran Siswa)
$routes->group('keuangan/pembayaransiswa', function ($routes) {
    $routes->get('/', 'Keuangan\PembayaranSiswa::index');
    $routes->get('ajaxList', 'Keuangan\PembayaranSiswa::ajaxList');

    $routes->get('getDetail/(:segment)', 'Keuangan\PembayaranSiswa::getDetail/$1');
    $routes->get('modaldetailsiswa', 'Keuangan\PembayaranSiswa::modaldetailsiswa');
    $routes->get('detail/berkas', 'Keuangan\PembayaranSiswa::detailBerkas');
    $routes->get('detail/orangtua', 'Keuangan\PembayaranSiswa::detailOrangtua');
    $routes->get('detail/pembayaran', 'Keuangan\PembayaranSiswa::detailPembayaran');
    $routes->get('detail/profile', 'Keuangan\PembayaranSiswa::detailProfile');
    $routes->get('detail/kartukontrol', 'Keuangan\PembayaranSiswa::kartukontrol');
    $routes->get('detail/prestasi', 'Keuangan\PembayaranSiswa::prestasi');

    $routes->match(['post'], 'simpanPembayaran', 'Keuangan\PembayaranSiswa::simpanPembayaran');
    $routes->post('hapusPembayaran', 'Keuangan\PembayaranSiswa::deletePembayaranSiswa');
    $routes->get('getRiwayatPembayaranByNis', 'Keuangan\PembayaranSiswa::getRiwayatPembayaranByNis');

    $routes->get('formpembayaranangkatan', 'Keuangan\PembayaranSiswa::modalPembayaranAngkatan');
    $routes->get('getAngkatanTableEditContent', 'Keuangan\PembayaranSiswa::getAngkatanTableEditContent');
    $routes->post('simpanPembayaranAngkatan', 'Keuangan\PembayaranSiswa::simpanPembayaranAngkatan');
    $routes->get('getRiwayatPembayaranByNisInfo', 'Keuangan\PembayaranSiswa::getRiwayatPembayaranByNisInfo');
});

$routes->group('laporan/keuangan', function ($routes) {
    $routes->get('/', 'Laporan\Keuangan\Lap_PembayaranSiswa::index');
    $routes->get('kartukontrol', 'Laporan\Keuangan\Lap_PembayaranSiswa::kartuKontrol');
    $routes->get('kwitansipembayaransiswa', 'Laporan\Keuangan\Lap_PembayaranSiswa::kwitansipembayaransiswa');
    $routes->get('kwitansikasmasuk', 'Laporan\Keuangan\Lap_PembayaranSiswa::kwitansikasmasuk');
    $routes->get('LaporanTahunanPembayaranSiswa', 'Laporan\Keuangan\Lap_PembayaranSiswa::LaporanTahunanPembayaranSiswa');


    // laporan harian petugas
    $routes->get('pdfLaporanHarianPetugas', 'Laporan\Keuangan\LaporanHarianPetugas::pdfLaporanHarianPetugas');
});
$routes->group('keuangan/laporankas', function ($routes) {
    $routes->get('LaporanPenerimaanKas', 'Laporan\Keuangan\LaporanKas::LaporanPenerimaanKas');
    $routes->get('LaporanPengeluaranKas', 'Laporan\Keuangan\LaporanKas::LaporanPengeluaranKas');
});

$routes->group('api/select2', function ($routes) {
    $routes->get('agama', 'Api\Referensi::agama');
    $routes->get('angkatan', 'Api\Referensi::angkatan');
    $routes->get('sekolah', 'Api\Referensi::sekolah');
    $routes->get('ruangan', 'Api\Referensi::ruangan');
    $routes->get('siswa', 'Api\Referensi::datasiswa');
    $routes->get('petugas', 'Api\Referensi::petugas');
    $routes->get('pegawai', 'Api\Referensi::pegawai');
    $routes->get('bank', 'Api\Referensi::bank');
    $routes->get('jenispenerimaan', 'Api\Referensi::jenispenerimaan_pembayaran');

    $routes->get('getSiswaByAngkatan', 'Api\Referensi::getSiswaByAngkatan');
    $routes->get('getSiswaBySekolah', 'Api\Referensi::getSiswaBySekolah');
    $routes->get('getJenisPenerimaanBySekolah', 'Api\Referensi::getJenisPenerimaanBySekolah');

    // terkait select2 perencanaan
    $routes->get('program', 'Api\Referensi::program');
    $routes->get('kegiatan', 'Api\Referensi::kegiatan');
    $routes->get('subkegiatan', 'Api\Referensi::subkegiatan');
    $routes->get('belanja', 'Api\Referensi::belanja');
});

// Perencanaan (program)
$routes->group('perencanaan/program', ['namespace' => 'App\Controllers\Perencanaan'], function ($routes) {
    $routes->get('/', 'Program::index');           // halaman daftar program
    $routes->get('ajaxList', 'Program::ajaxList'); // data table ajax
    $routes->get('get/(:num)', 'Program::get/$1'); // ambil 1 program untuk edit
    $routes->post('save', 'Program::save');        // simpan program baru
    $routes->post('update', 'Program::update');    // update program
    $routes->get('delete/(:num)', 'Program::delete/$1'); // hapus program
});

// Perencanaan (kegiatan)
$routes->group('perencanaan/kegiatan', function ($routes) {
    $routes->get('/', 'Perencanaan\Kegiatan::index');
    $routes->get('ajaxList', 'Perencanaan\Kegiatan::ajaxList');
    $routes->get('get/(:num)', 'Perencanaan\Kegiatan::get/$1');
    $routes->post('save', 'Perencanaan\Kegiatan::save');
    $routes->post('update', 'Perencanaan\Kegiatan::update');
    $routes->get('delete/(:num)', 'Perencanaan\Kegiatan::delete/$1');
});


// Perencanaan (subkegiatan)
$routes->group('perencanaan/detail', function ($routes) {
    $routes->get('/', 'Perencanaan\Detail::index');
    $routes->get('form', 'Perencanaan\Detail::form');
    $routes->post('save', 'Perencanaan\Detail::save');
    $routes->post('update', 'Perencanaan\Detail::update');
    $routes->get('ajaxList', 'Perencanaan\Detail::ajaxList');
    $routes->get('ajaxChildren', 'Perencanaan\Detail::ajaxChildren');
    $routes->get('get/(:num)', 'Perencanaan\Detail::get/$1');
    $routes->get('delete/(:num)', 'Perencanaan\Detail::delete/$1');


    // hirarki program -> kegiatan -> Detail
    $routes->get('modalFormKegiatan', 'Perencanaan\Detail::modalFormKegiatan');
    $routes->get('modalFormProgram', 'Perencanaan\Detail::modalFormProgram');

    // hirarki belanja
    $routes->get('modalFormBelanja', 'Perencanaan\Detail::modalFormBelanja');
    $routes->post('saveBelanja', 'Perencanaan\Detail::saveBelanja');
    $routes->post('updateBelanja', 'Perencanaan\Detail::updateBelanja');
    $routes->get('deleteBelanja/(:num)', 'Perencanaan\Detail::deleteBelanja/$1');
});


// Perencanaan (belanja)
$routes->group('perencanaan/belanja', function ($routes) {
    $routes->get('/', 'Perencanaan\Belanja::index');
    $routes->get('form', 'Perencanaan\Belanja::form');
    $routes->post('simpan', 'Perencanaan\Belanja::simpan');
    $routes->post('update', 'Perencanaan\Belanja::update');
    $routes->get('ajaxList', 'Perencanaan\Belanja::ajaxList');
    $routes->get('get/(:num)', 'Perencanaan\Belanja::get/$1');
    $routes->get('delete/(:num)', 'Perencanaan\Belanja::delete/$1');
});


// API ABSEN
$routes->group('api/absensi', function ($routes) {
    // Auth
    $routes->post('login', 'Api\Api::login');
    $routes->post('update_profile', 'Api\Api::update_profile');
    $routes->post('change_password', 'Api\Api::change_password');

    $routes->get('get/lembaga', 'Api\Api::get_data_lembaga');

    $routes->post('submit', 'Api\Api::submit_absensi');
    $routes->get('get/absen', 'Api\Api::get_absen');

    // Referensi
    $routes->get('get/agama', 'Api\Api::get_data_agama');
    $routes->get('get/pendidikan', 'Api\Api::get_data_pendidikan');
    $routes->get('get/jabatan', 'Api\Api::get_data_jabatan');
    $routes->get('get/profesi', 'Api\Api::get_data_profesi');
    $routes->get('get/jenis-pegawai', 'Api\Api::get_data_jenis_pegawai');
    $routes->get('get/gedung', 'Api\Api::get_data_gedung');

    $routes->get('get/absen-today', 'Api\Api::get_data_absen_pengaturan');
    $routes->get('get/tupoksi', 'Api\Api::get_tupoksi');
    $routes->post('submit_kinerja', 'Api\Api::submit_kinerja');
    $routes->post('delete/kinerja', 'Api\Api::delete_kinerja');
    $routes->get('get/get_kinerja', 'Api\Api::get_kinerja');
    $routes->get('get/get_kinerja_by_tupoksi', 'Api\Api::get_kinerja_by_tupoksi');
    $routes->get('get/get_kinerja_bulanan', 'Api\Api::get_kinerja_bulanan');
});
