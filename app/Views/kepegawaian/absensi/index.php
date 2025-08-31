<?= $this->extend('templates/default') ?>
<?= $this->section('content') ?>

<?php
// Ambil tab dari query string, default kaskeluar
$activeTab = $_GET['tab'] ?? 'monitoring';
if (!in_array($activeTab, ['monitoring', 'laporan'])) {
    $activeTab = 'monitoring';
}
?>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills mb-3" role="tablist">
            <!-- <li class="nav-item">
                <a href="?tab=pengaturan" class="nav-link <?= ($activeTab=='pengaturan') ? 'active' : '' ?>">
                    Pengaturan
                </a>
            </li> -->
            <li class="nav-item">
                <a href="?tab=monitoring" class="nav-link <?= ($activeTab=='monitoring') ? 'active' : '' ?>">
                    Monitoring
                </a>
            </li>
            <li class="nav-item">
                <a href="?tab=laporan" class="nav-link <?= ($activeTab=='laporan') ? 'active' : '' ?>">
                    Laporan
                </a>
            </li>
        </ul>
        <div class="tab-content p-0">
            <?php
                    if ($activeTab == 'monitoring') {
                        echo view('kepegawaian/absensi/tabs_lainnya');
                    } elseif ($activeTab == 'laporan') {
                        echo view('kepegawaian/absensi/tabs_laporan');
                    } 
                    ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>