<div class="modal fade" id="modalformSiswaBaru" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="labelModal" aria-hidden="true" data-nis="">
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
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tabProfileBtn" data-bs-toggle="tab"
                            data-bs-target="#tab-profile" type="button" role="tab" aria-controls="tab-profile"
                            aria-selected="true">👨 Profile</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tabOrangtuaBtn" data-bs-toggle="tab" data-bs-target="#tab-orangtua"
                            type="button" role="tab" aria-controls="tab-pembayaran" aria-selected="false">👨‍👩‍👧‍👦
                            Profil Orang Tua</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tabBerkasBtn" data-bs-toggle="tab" data-bs-target="#tab-berkas"
                            type="button" role="tab" aria-controls="tab-pembayaran" aria-selected="false">📂 Berkas
                            Siswa</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tabPendaftaranPendidikan" data-bs-toggle="tab"
                            data-bs-target="#tab-pendaftaranpendidikan" type="button" role="tab"
                            aria-controls="tab-pendaftaranpendidikan" aria-selected="false">📅 Pendidikan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tabPrestasiBtn" data-bs-toggle="tab" data-bs-target="#tab-prestasi"
                            type="button" role="tab" aria-controls="tab-prestasi" aria-selected="false">🏆
                            Prestasi / Kenaikan Kelas</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tabPembayaranBtn" data-bs-toggle="tab"
                            data-bs-target="#tab-pembayaran" type="button" role="tab" aria-controls="tab-pembayaran"
                            aria-selected="false">💰 Pembayaran / Tagihan</button>
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
                    <div class="tab-pane fade" id="tab-pendaftaranpendidikan" role="tabpanel"
                        aria-labelledby="tabPendaftaranPendidikan">
                        <div class="pendaftaran-content"></div>
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

<script>
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
        error: function() {
            container.html('<div class="alert alert-danger">Gagal memuat data.</div>');
        }
    });
}
// Fungsi khusus reload tab pembayaran
function reloadPembayaranTab(nis) {
    loadTabContent('<?= base_url('kesiswaan/siswa/detail/pembayaran') ?>', '.pembayaran-content', nis);
}
$(document).ready(function() {
    initReferensiSelects(this);
    disableAutocomplete();
    // Fungsi untuk load tab berdasarkan NIS

    // Saat modal muncul (pastikan NIS sudah terisi di input hidden)
    $('#modalformSiswaBaru').on('shown.bs.modal', function() {

        const nis = $('#modalformSiswaBaru .siswa-nis').val();
        if (!nis) {
            console.warn("NIS tidak tersedia");
            return;
        }

        // Override loadTabContent untuk tab profile agar load select2 & data setelah konten load
        function loadProfileTab() {
            loadTabContent('<?= base_url('kesiswaan/siswa/detail/profile') ?>', '#tab-profile', nis,
                function() {
                    // Setelah konten profile dimuat ke DOM, init select2 dan load data agama & angkatan
                    initReferensiSelects('#tab-profile');
                });
        }

        // Panggil tab profile pertama kali
        loadProfileTab();

        // Event klik tab Profile
        $('#tabProfileBtn').off('click').on('click', function() {
            loadProfileTab();
        });


        // Event klik tab informasi detail orang tua
        $('#tabOrangtuaBtn').off('click').on('click', function() {
            loadTabContent('<?= base_url('kesiswaan/siswa/detail/orangtua') ?>',
                '.orangtua-content', nis);
        });

        // Event klik tab berkas siswa
        $('#tabBerkasBtn').off('click').on('click', function() {
            loadTabContent('<?= base_url('kesiswaan/siswa/detail/berkas') ?>',
                '.berkas-content', nis);
        });


        // Event klik tab Pembayaran
        $('#tabPembayaranBtn').off('click').on('click', function() {
            loadTabContent('<?= base_url('kesiswaan/siswa/detail/pembayaran') ?>',
                '.pembayaran-content', nis);
        });


        // Event klik tab pendaftaran pendidikan
        function loadPendaftaranTab() {
            loadTabContent('<?= base_url('kesiswaan/siswa/detail/pendaftaran') ?>',
                '#tab-pendaftaranpendidikan', nis,
                function() {
                    initReferensiSelects('#tab-pendaftaranpendidikan');
                });
        }

        // Panggil tab profile pertama kali
        loadPendaftaranTab();
        $('#tabPendaftaranPendidikan').off('click').on('click', function() {
            loadPendaftaranTab();
        });

        // Event klik tab Prestasi
        $('#tabPrestasiBtn').off('click').on('click', function() {
            loadTabContent('<?= base_url('kesiswaan/siswa/detail/prestasi') ?>',
                '.prestasi-content', nis);
        });

        // Trigger klik tab default
        $('#tabProfileBtn').trigger('click');
    });


});
</script>