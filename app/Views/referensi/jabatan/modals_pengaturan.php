<div class="modal fade" id="modalPengaturan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="pengaturanModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelModal">Pengaturan Jabatan</h5>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">
                    <i class="fa fa-window-close"></i> Tutup
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="id"> <!-- id jabatan dari modal -->
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-tabs nav-fill" role="tablist">
                        <li class="nav-item shadow">
                            <button type="button" class="nav-link active" role="tab" data-bs-target="penerimaan">
                                📥 Penerimaan
                            </button>
                        </li>
                        <li class="nav-item shadow">
                            <button type="button" class="nav-link" role="tab" data-bs-target="tupoksi">
                                ✔ Tupoksi
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-target="absensi">
                                🗓️ Absensi
                            </button>
                        </li>
                    </ul>

                    <!-- Satu container content saja -->
                    <div id="tabContent" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function reloadAbsensiTab() {
    const $container = $('#tabContent');
    const idJabatan = $('#modalPengaturan #id').val();

    // jika tab saat ini bukan absensi → switch dulu
    if ($container.data('current-tab') !== 'absensi') {
        $('.nav-tabs button').removeClass('active');
        $('.nav-tabs button[data-bs-target="absensi"]').addClass('active');
    }

    // load konten tab Absensi
    $.get("<?= base_url('referensi/jabatan/absensi') ?>", {
        id: idJabatan
    }, function(data) {
        $container.html(data);

        // re-inisialisasi inputmask / JS lainnya jika perlu
        if (typeof initAbsensi === 'function') initAbsensi();
    });

    // set current-tab
    $container.data('current-tab', 'absensi');
}

$(document).ready(function() {
    const $container = $('#tabContent');

    function loadTab(tab) {
        const $container = $('#tabContent');
        const idJabatan = $('#modalPengaturan #id').val(); // ambil ID dari modal
        $container.data('current-tab', tab);
        $container.html('<p>Loading...</p>');

        let url = '';
        if (tab === 'penerimaan') url = "<?= base_url('referensi/jabatan/penerimaan') ?>";
        if (tab === 'tupoksi') url = "<?= base_url('referensi/jabatan/tupoksi') ?>";
        if (tab === 'absensi') url = "<?= base_url('referensi/jabatan/absensi') ?>";

        if (url) {
            $.get(url, {
                id: idJabatan
            }, function(data) {
                $container.html(data);
                // Jika tab penerimaan, jalankan DataTable/JS khusus penerimaan
                if (tab === 'penerimaan' && typeof initPenerimaan === 'function') {
                    initPenerimaan();
                }
            });
        }
    }

    // default load pertama kali → ambil ID dari modal
    const jabatanId = $('#modalPengaturan #id').val();
    loadTab('penerimaan', jabatanId);

    // klik tab
    $('.nav-tabs button').on('click', function() {
        const tab = $(this).data('bs-target');
        $('.nav-tabs button').removeClass('active');
        $(this).addClass('active');

        const currentTab = $('#tabContent').data('current-tab');
        if (currentTab !== tab) {
            loadTab(tab);
        }
    });


    // reset modal supaya DataTable tidak dobel
    $('#modalPengaturan').on('hidden.bs.modal', function() {
        $container.removeData('current-tab').empty();
    });
});
</script>