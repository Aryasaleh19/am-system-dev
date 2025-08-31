<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-1">
        <div class="col-lg-3">
            <div class="card">
                <h6 class="card-header bg-warning text-white p-2">Laporan Keuangan</h6>
                <div class="demo-inline-spacing small">
                    <div class="list-group list-group-flush">
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action laporan-link">
                            <i class="fa fa-print" aria-hidden="true"></i> Rekapitulasi Transaksi Harian
                        </a>
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action laporan-link">
                            <i class="fa fa-print" aria-hidden="true"></i> Kartu Kontrol Siswa
                        </a>
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action laporan-link">
                            <i class="fa fa-print" aria-hidden="true"></i> Daftar Pembayaran Siswa
                        </a>
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action laporan-link">
                            <i class="fa fa-print" aria-hidden="true"></i> Laporan Penerimaan Kas
                        </a>
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action laporan-link">
                            <i class="fa fa-print" aria-hidden="true"></i> Laporan Pengeluaran Kas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card">
                <div class="card-header bg-warning text-white p-2 title-laporan">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Titel Laporan
                </div>
                <div class="card-body p-2">
                    <div class="row g-1">
                        <div class="col-12" id="parameterLaporan">
                            <div class="alert alert-warning mt-3">
                                <strong>Informasi:</strong> klik Tombol Laporan untuk melihat laporan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-warning text-white p-2 title-laporan">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Preview Laporan
                </div>
                <div class="card-body p-2">
                    <div class="row g-1">
                        <div class="col-12 text-center" id="tampilLaporanPDF">
                            <h6 class="text-muted text-center p-5">Silahkan pilih Jenis Laporan dan Parameter untuk
                                menampikan Preview Laporan.</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script src="<?= base_url('js/laporan-keuangan.js') ?>"></script>

<?= $this->endSection() ?>