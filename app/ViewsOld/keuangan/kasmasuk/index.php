<?= $this->extend('templates/default') ?>
<?= $this->section('content') ?>

<?php
// Ambil tab dari query string, default kaskeluar
$activeTab = $_GET['tab'] ?? 'pembayaransiswa';
if (!in_array($activeTab, ['pembayaransiswa', 'lainnya', 'setoranpinjaman'])) {
    $activeTab = 'pembayaransiswa';
}
?>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item">
                <a href="?tab=pembayaransiswa" class="nav-link <?= ($activeTab=='pembayaransiswa') ? 'active' : '' ?>">
                    Pembayaran Siswa
                </a>
            </li>
            <li class="nav-item">
                <a href="?tab=lainnya" class="nav-link <?= ($activeTab=='lainnya') ? 'active' : '' ?>">
                    Kas Masuk Lainnya
                </a>
            </li>
            <li class="nav-item">
                <a href="?tab=setoranpinjaman" class="nav-link <?= ($activeTab=='setoranpinjaman') ? 'active' : '' ?>">
                    Setoran Pinjaman
                </a>
            </li>
        </ul>

        <div class="tab-content p-0">
            <?php
                    if ($activeTab == 'pembayaransiswa') {
                        echo view('keuangan/kasmasuk/pembayaransiswa');
                    } elseif ($activeTab == 'lainnya') {
                        echo view('keuangan/kasmasuk/lainnya');
                    } elseif ($activeTab == 'setoranpinjaman') {
                        echo view('keuangan/kasmasuk/setoranpinjaman');
                    }
                    ?>
        </div>
    </div>

</div>

<?= $this->endSection() ?>