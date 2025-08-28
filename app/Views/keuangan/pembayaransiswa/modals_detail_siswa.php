<div class="modal fade" id="modalformSiswaBaru" data-bs-backdrop="static" tabindex="-1" aria-labelledby="labelModal"
    aria-hidden="true" data-nis="">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelModal">Detail Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="NIS" name="NIS" class="siswa-nis">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <!-- <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tabProfileBtn" data-bs-toggle="tab"
                            data-bs-target="#tab-profile" type="button" role="tab" aria-controls="tab-profile"
                            aria-selected="true">👨 Profile</button>
                    </li> -->
                    <!-- <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tabOrangtuaBtn" data-bs-toggle="tab" data-bs-target="#tab-orangtua"
                            type="button" role="tab" aria-controls="tab-pembayaran" aria-selected="false">👨‍👩‍👧‍👦
                            Profil Orang Tua</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tabBerkasBtn" data-bs-toggle="tab" data-bs-target="#tab-berkas"
                            type="button" role="tab" aria-controls="tab-pembayaran" aria-selected="false">📂 Berkas
                            Siswa</button>
                    </li> -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tabPembayaranBtn" data-bs-toggle="tab"
                            data-bs-target="#tab-pembayaran" type="button" role="tab" aria-controls="tab-pembayaran"
                            aria-selected="false">💰 Form Pembayaran</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tabKartuKontrol" data-bs-toggle="tab"
                            data-bs-target="#tab-kartukontrol" type="button" role="tab" aria-controls="tab-kartukontrol"
                            aria-selected="false">📜 Kartu Kontrol</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tabKartuKontrol" data-bs-toggle="tab"
                            data-bs-target="#tab-kartukontrol" type="button" role="tab" aria-controls="tab-kartukontrol"
                            aria-selected="false">⚠️ Informasi Tunggakan</button>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content pt-3">
                    <div class="tab-pane fade show active" id="tab-profile" role="tabpanel"
                        aria-labelledby="tabProfileBtn">
                        <div class="">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="tab-orangtua" role="tabpanel" aria-labelledby="tabOrangtuaBtn">
                        <div class="orangtua-content"></div>
                    </div>
                    <div class="tab-pane fade" id="tab-berkas" role="tabpanel" aria-labelledby="tabBerkasBtn">
                        <div class="berkas-content"></div>
                    </div>
                    <div class="tab-pane fade" id="tab-pembayaran" role="tabpanel" aria-labelledby="tabPembayaranBtn">
                        <div class="pembayaran-content"></div>
                    </div>
                    <div class="tab-pane fade" id="tab-kartukontrol" role="tabpanel" aria-labelledby="tabKartuKontrol">
                        <div class="kartukontrol-content"></div>
                    </div>
                    <div class="tab-pane fade" id="tab-prestasi" role="tabpanel" aria-labelledby="tabPrestasiBtn">
                        <div class="prestasi-content"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('js/referensi.js') ?>"></script>
<script src="<?= base_url('js/wilayah.js') ?>"></script>
<script>
$(document).ready(function() {
    disableAutocomplete();
    // Fungsi untuk load tab berdasarkan NIS
    function loadTabContent(url, containerSelector, nis, callback) {
        const container = $(containerSelector);
        container.html('Loading...');

        $.ajax({
            url: url,
            method: 'GET',
            data: {
                nis: nis
            },
            dataType: 'html',
            success: function(response) {
                container.html(response);
                if (typeof callback === 'function') callback();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                container.html('<div class="alert alert-danger">Gagal memuat data.</div>');

                // Tampilkan error detail pakai SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    html: `
                        <strong>Status:</strong> ${textStatus} <br>
                        <strong>Kode:</strong> ${jqXHR.status} <br>
                        <strong>Pesan:</strong> ${errorThrown || 'Tidak diketahui'}
                    `,
                    confirmButtonText: 'Tutup'
                });
            }

        });
    }



    // Saat modal muncul (pastikan NIS sudah terisi di input hidden)
    $('#modalformSiswaBaru').on('shown.bs.modal', function() {
        const nis = $('#modalformSiswaBaru .siswa-nis').val();
        if (!nis) {
            console.warn("NIS tidak tersedia");
            return;
        }

        // Load konten tab pembayaran saat modal muncul
        loadTabContent('<?= base_url('keuangan/pembayaransiswa/detail/pembayaran') ?>',
            '.pembayaran-content', nis,
            function() {
                initReferensiSelects('.pembayaran-content');
            });

        // Set tab Pembayaran jadi aktif dan tampil
        // Hapus kelas aktif di tab lain dan tambahkan ke tab pembayaran
        $('.nav-link').removeClass('active');
        $('#tabPembayaranBtn').addClass('active');

        // Hapus class show active di semua tab-pane
        $('.tab-pane').removeClass('show active');
        // Tambahkan show active ke tab pembayaran pane
        $('#tab-pembayaran').addClass('show active');

        // Setup event click seperti biasa
        $('#tabProfileBtn').off('click').on('click', function() {
            loadTabContent('<?= base_url('keuangan/pembayaransiswa/detail/pembayaran') ?>',
                '#tab-pembayaran', nis,
                function() {
                    initReferensiSelects('#tab-pembayaran');
                });
        });

        // Event klik tab informasi detail orang tua
        $('#tabOrangtuaBtn').off('click').on('click', function() {
            loadTabContent('<?= base_url('keuangan/pembayaransiswa/detail/orangtua') ?>',
                '.orangtua-content', nis);
        });

        // Event klik tab berkas siswa
        $('#tabBerkasBtn').off('click').on('click', function() {
            loadTabContent('<?= base_url('keuangan/pembayaransiswa/detail/berkas') ?>',
                '.berkas-content', nis);
        });

        // Event klik tab Pembayaran (supaya reload konten saat klik)
        $('#tabPembayaranBtn').off('click').on('click', function() {
            loadTabContent('<?= base_url('keuangan/pembayaransiswa/detail/pembayaran') ?>',
                '.pembayaran-content', nis);
        });

        // Event klik tab kartukontrol
        $('#tabKartuKontrol').off('click').on('click', function() {
            loadTabContent('<?= base_url('keuangan/pembayaransiswa/detail/kartukontrol') ?>',
                '.kartukontrol-content', nis);
        });

        // Event klik tab Prestasi
        $('#tabPrestasiBtn').off('click').on('click', function() {
            loadTabContent('<?= base_url('keuangan/pembayaransiswa/detail/prestasi') ?>',
                '.prestasi-content', nis);
        });

        // Tidak perlu trigger klik tabProfileBtn karena tab Pembayaran sudah aktif
    });


});
</script>