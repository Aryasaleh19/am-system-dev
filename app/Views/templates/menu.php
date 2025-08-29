 <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
     <div class="app-brand bg-navbar-theme demo">
         <a href="<?= base_url() ?>" class="app-brand-link">
             <span class="app-brand-logo demo">
                 <!-- <img src="<?= base_url() ?>/assets/img/1756232933_4301eddc1a054aee1928.jpg" style="width: 50px;" alt=""> -->
             </span>
             <span class="app-brand-text demo menu-text fw-bolder ms-2">Al-Muhajirin</span>
         </a>

         <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
             <i class="bx bx-chevron-left bx-sm align-middle"></i>
         </a>
     </div>

     <div class="menu-inner-shadow"></div>

     <ul class="menu-inner py-1">
         <!-- Dashboard -->
         <li class="menu-item <?= set_active('dashboard') ?>">
             <a href="<?= base_url('dashboard') ?>" class="menu-link">
                 <i class="menu-icon tf-icons bx bx-home-circle"></i>
                 <div data-i18n="Analytics">Dashboard</div>
             </a>
         </li>

         <!-- Layouts -->
         <li
             class="menu-item <?= set_active(['pengaturan/group_modul', 'pengaturan/modul', 'pengaturan/manajemen_modul', 'pengaturan/profil', 'pengaturan/managemen-modul']) ?>">
             <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons bx bx-list-ul"></i>
                 <div data-i18n="Layouts">Pengaturan</div>
             </a>

             <ul class="menu-sub">
                 <li class="menu-item <?= set_active('pengaturan/profil') ?>">
                     <a href="<?= base_url('pengaturan/profil') ?>" class="menu-link">
                         <div data-i18n="Without menu">🕌 Profil Yayasan</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('pengaturan/group_modul') ?>">
                     <a href="<?= base_url('pengaturan/group_modul') ?>" class="menu-link">
                         <div data-i18n="Without menu">📦 Group Modul</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('pengaturan/modul') ?>">
                     <a href="<?= base_url('pengaturan/modul') ?>" class="menu-link">
                         <div data-i18n="Without navbar">🧩 Modul</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('pengaturan/managemen-modul') ?>">
                     <a href="<?= base_url('pengaturan/managemen-modul') ?>" class="menu-link">
                         <div data-i18n="Container">🔗 Managemen Modul</div>
                     </a>
                 </li>
             </ul>
         </li>
         <li
             class="menu-item <?= set_active(['referensi/gedung', 'referensi/agama', 'referensi/jabatan', 'referensi/profesi', 'referensi/jenispegawai', 'referensi/sekolah']) ?>">
             <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons bx bx-list-ul"></i>
                 <div data-i18n="Layouts">Referensi</div>
             </a>

             <ul class="menu-sub">
                 <li class="menu-item <?= set_active('referensi/sekolah') ?>">
                     <a href="<?= base_url('referensi/sekolah') ?>" class="menu-link">
                         <div data-i18n="Without menu">🏫 Sekolah</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('referensi/gedung') ?>">
                     <a href="<?= base_url('referensi/gedung') ?>" class="menu-link">
                         <div data-i18n="Without menu">🏢 Gedung</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('referensi/agama') ?>">
                     <a href="<?= base_url('referensi/agama') ?>" class="menu-link">
                         <div data-i18n="Fluid">☪️ Agama</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('referensi/jabatan') ?>">
                     <a href="<?= base_url('referensi/jabatan') ?>" class="menu-link">
                         <div data-i18n="Blank">🎖️ Jabatan</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('referensi/profesi') ?>">
                     <a href="<?= base_url('referensi/profesi') ?>" class="menu-link">
                         <div data-i18n="Blank">👨‍🏫 Profesi</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('referensi/jenispegawai') ?>">
                     <a href="<?= base_url('referensi/jenispegawai') ?>" class="menu-link">
                         <div data-i18n="Blank">✔️ Jenis Pegawai</div>
                     </a>
                 </li>

             </ul>
         </li>
         <li
             class="menu-item <?= set_active(['kepegawaian/pegawai', 'kepegawaian/managemenakses', 'kepegawaian/mutasi']) ?>">
             <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons bx bx-list-ul"></i>
                 <div data-i18n="Layouts">Kepegawaian</div>
             </a>
             <ul class="menu-sub">
                 <li class="menu-item <?= set_active('kepegawaian/pegawai') ?>">
                     <a href="<?= base_url('kepegawaian/pegawai') ?>" class="menu-link">
                         <div data-i18n="Without menu">🧕 Pegawai</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('kepegawaian/managemenakses') ?>">
                     <a href="<?= base_url('kepegawaian/managemenakses') ?>" class="menu-link">
                         <div data-i18n="Without menu">🛡️ Managemen Akses</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('kepegawaian/mutasi') ?>">
                     <a href="<?= base_url('kepegawaian/mutasi') ?>" class="menu-link">
                         <div data-i18n="Without navbar">🚶‍♂️ Mutasi Pegawai</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('kepegawaian/jadwal') ?>">
                     <a href="<?= base_url('kepegawaian/jadwal') ?>" class="menu-link">
                         <div data-i18n="Without navbar">📝 Jadwal Kegiatan</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('kepegawaian/laporanpegawai') ?>">
                     <a href="<?= base_url('kepegawaian/laporanpegawai') ?>" class="menu-link">
                         <div data-i18n="Container">📊 Laporan</div>
                     </a>
                 </li>
             </ul>
         </li>
         <li class="menu-item <?= set_active(['kesiswaan/siswa', 'kesiswaan/laporan', 'kesiswaan/angkatan']) ?>">
             <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons bx bx-list-ul"></i>
                 <div data-i18n="Layouts">Kesiswaan</div>
             </a>

             <ul class="menu-sub">
                 <li class="menu-item <?= set_active('kesiswaan/angkatan') ?>">
                     <a href="<?= base_url('kesiswaan/angkatan') ?>" class="menu-link">
                         <div data-i18n="Without menu">📆 Tahun Angkatan</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('kesiswaan/siswa') ?>">
                     <a href="<?= base_url('kesiswaan/siswa') ?>" class="menu-link">
                         <div data-i18n="Without menu">👨 Siswa</div>
                     </a>
                 </li>
                 <li class="menu-item">
                     <a href="<?= base_url('perencanaan/program') ?>" class="menu-link">
                         <div data-i18n="Without navbar">📊 Laporan</div>
                     </a>
                 </li>
             </ul>
         </li>

         <li
             class="menu-item <?= set_active(['referensi/jenispenerimaan', 'referensi/jenispengeluaran', 'keuangan/rekeningbank', 'keuangan/kasmasuk', 'keuangan/kaskeluar', 'keuangan/pembayaransiswa', 'laporan/keuangan']) ?>">
             <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons bx bx-list-ul"></i>
                 <div data-i18n="Layouts">Keuangan</div>
             </a>

             <ul class="menu-sub">
                 <li class="menu-item <?= set_active('referensi/jenispenerimaan') ?>">
                     <a href="<?= base_url('referensi/jenispenerimaan') ?>" class="menu-link">
                         <div data-i18n="Blank">💵 Jenis Penerimaan</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('referensi/jenispengeluaran') ?>">
                     <a href="<?= base_url('referensi/jenispengeluaran') ?>" class="menu-link">
                         <div data-i18n="Without menu">💸 Jenis Pengeluaran</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('keuangan/rekeningbank') ?>">
                     <a href="<?= base_url('keuangan/rekeningbank') ?>" class="menu-link">
                         <div data-i18n="Without menu">🏦 Rekening Kas</div>
                     </a>
                 </li>
                 <!-- <li class="menu-item <?= set_active('keuangan/pembayaransiswa') ?>">
                     <a href="<?= base_url('keuangan/pembayaransiswa') ?>" class="menu-link">
                         <div data-i18n="Without navbar">👨 Pembayaran Siswa</div>
                     </a>
                 </li> -->
                 <li class="menu-item <?= set_active('keuangan/kasmasuk') ?>">
                     <a href="<?= base_url('keuangan/kasmasuk') ?>" class="menu-link">
                         <div data-i18n="Without navbar">➡️ Kas Masuk</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('keuangan/kaskeluar') ?>">
                     <a href="<?= base_url('keuangan/kaskeluar') ?>" class="menu-link">
                         <div data-i18n="Container">⬅️ Kas Keluar</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('laporan/keuangan') ?>">
                     <a href="<?= base_url('laporan/keuangan') ?>" class="menu-link">
                         <div data-i18n="Fluid">📚 Laporan</div>
                     </a>
                 </li>
             </ul>
         </li>
         <li class="menu-item <?= set_active(['perencanaan/detail']) ?>">
             <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons bx bx-list-ul"></i>
                 <div data-i18n="Layouts">Perencanaan</div>
             </a>

             <ul class="menu-sub">

                 <li class="menu-item <?= set_active('perencanaan/detail') ?>">
                     <a href="<?= base_url('perencanaan/detail') ?>" class="menu-link">
                         <div data-i18n="Container">🗓️ Perencanaan</div>
                     </a>
                 </li>
                 <li class="menu-item <?= set_active('perencanaan/laporan') ?>">
                     <a href="<?= base_url('perencanaan/laporan') ?>" class="menu-link">
                         <div data-i18n="Blank">📄 Laporan</div>
                     </a>
                 </li>

             </ul>
         </li>
     </ul>
 </aside>
 <!-- / Menu -->

 <style>
 </style>