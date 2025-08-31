<?= $this->extend('templates/default') ?>
<?= $this->section('content') ?>

<?php
// Ambil tab dari query string, default kaskeluar
$activeTab = $_GET['tab'] ?? 'kaskeluar';
if (!in_array($activeTab, ['kaskeluar', 'penggajian', 'tagihan', 'pinjaman'])) {
    $activeTab = 'kaskeluar';
}
?>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item">
                <a href="?tab=kaskeluar" class="nav-link <?= ($activeTab=='kaskeluar') ? 'active' : '' ?>">
                    Kas Keluar
                </a>
            </li>
            <li class="nav-item">
                <a href="?tab=penggajian" class="nav-link <?= ($activeTab=='penggajian') ? 'active' : '' ?>">
                    Penggajian
                </a>
            </li>
            <li class="nav-item">
                <a href="?tab=pinjaman" class="nav-link <?= ($activeTab=='pinjaman') ? 'active' : '' ?>">
                    Pinjaman Pegawai
                </a>
            </li>
        </ul>

        <div class="tab-content row p-0">
            <?php
                    if ($activeTab == 'kaskeluar') {
                        echo view('keuangan/kaskeluar/kaskeluar');
                    } elseif ($activeTab == 'penggajian') {
                        echo view('keuangan/kaskeluar/penggajian');
                    } elseif ($activeTab == 'tagihan') {
                        echo view('keuangan/kaskeluar/tagihan');
                    } elseif ($activeTab == 'pinjaman') {
                        echo view('keuangan/kaskeluar/pinjaman');
                    }
                    ?>
        </div>
    </div>

</div>

<?= $this->endSection() ?>