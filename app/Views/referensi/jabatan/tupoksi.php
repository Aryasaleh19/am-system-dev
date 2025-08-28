<form id="formTupoksi" method="post" action="<?= base_url('referensi/jabatan/simpanTupoksiJabatan') ?>">
    <input type="hidden" name="id" id="id" value="<?= $id_jabatan ?>" required> <!-- id jabatan dari modal -->
    <input type="hidden" name="id_tupoksi" id="id_tupoksi"> <!-- kosong = insert, terisi = update -->

    <div class="row g-3">
        <div class="col-md-4 border-end">
            <!-- Nama jabatan -->
            <div class="mb-2">
                <input type="text" id="jabatan"
                    class="form-control text-center bg-white rounded fw-bold text-uppercase text-primary"
                    value="<?= esc($jabatan) ?>" disabled>
            </div>

            <!-- Input Tupoksi -->
            <div class="mb-2">
                <label>Uraian Tupoksi</label><sup class="text-danger">*</sup>
                <textarea class="form-control" name="tupoksi" id="tupoksi" rows="4"
                    placeholder="Masukkan uraian tupoksi" required></textarea>
            </div>

            <!-- Input Beban Waktu (Jam / Minggu) -->
            <div class="mb-2">
                <label>Beban Waktu (Jam / Minggu)</label>
                <input type="number" class="form-control text-end" name="beban_waktu" id="beban_waktu" placeholder="0"
                    required>
            </div>

            <div class="mb-2">
                <label>Status</label>
                <select class="form-select form-control" placeholder="Status Tupoksi" name="status" id="status"
                    required>
                    <option value="" selected>[ Pilih ]</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>

            <div class="mt-2">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-save"></i> Tambah
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger">Reset</button>
            </div>
        </div>

        <!-- DataTable Tupoksi -->
        <div class="col-md-8">
            <table class="table table-hover table-sm table-bordered w-100" id="tabelTupoksi">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Uraian Tupoksi</th>
                        <th class="text-center">Beban<br>(Jam/Minggu)</th>
                        <th class="text-center">Admin</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    disableAutocomplete();
    const idJabatan = $('#id').val();
    $('#id').val(idJabatan);

    let tupoksiTable;

    // Init / reload DataTable
    function initTupoksi() {
        if ($.fn.DataTable.isDataTable('#tabelTupoksi')) {
            $('#tabelTupoksi').DataTable().clear().destroy();
        }

        tupoksiTable = $('#tabelTupoksi').DataTable({
            paging: false,
            scrollY: '50vh',
            scrollCollapse: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= base_url('referensi/jabatan/ajaxListTupoksi') ?>",
                type: "GET",
                data: {
                    id: idJabatan
                },
                dataSrc: 'data'
            },
            columns: [{
                    data: null,
                    className: 'text-center',
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'URAIAN_TUPOKSI',
                    className: 'text-left'
                },
                {
                    data: 'BEBAN',
                    className: 'text-center'

                },
                {
                    data: 'NAMA_PENGGUNA',
                    className: 'text-left'
                },
                {
                    data: 'STATUS',
                    className: 'text-center',
                    render: data => data == 1 ? '<span class="badge bg-success">Aktif</span>' :
                        '<span class="badge bg-danger">Tidak Aktif</span>'
                },
                {
                    data: null,
                    className: 'text-center',
                    render: data => `
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-info editBtn"
                            data-id="${data.ID}"
                            data-tupoksi="${data.URAIAN_TUPOKSI}"
                            data-beban="${data.BEBAN}"
                            data-status="${data.STATUS}">✏️</button>
                        <button type="button" class="btn btn-sm btn-outline-danger deleteBtn"
                            data-id="${data.ID}">🗑️</button>
                    </div>`
                }
            ]
        });
    }

    initTupoksi();

    // Reset form
    $('#formTupoksi .btn-outline-danger').on('click', function() {
        $('#formTupoksi')[0].reset();
        $('#id').val(idJabatan);
        $('#id_tupoksi').val('');
        $('#formTupoksi button[type="submit"]').html('<i class="fa fa-save"></i> Tambah');
    });

    // Edit Tupoksi
    $(document).on('click', '.editBtn', function() {
        const idT = $(this).data('id');
        const tupoksi = $(this).data('tupoksi');
        const beban = $(this).data('beban');
        const status = $(this).data('status'); // ambil status dari data-* baru

        $('#tupoksi').val(tupoksi);
        $('#beban_waktu').val(beban);
        $('#status').val(status); // set dropdown status sesuai data
        $('#id_tupoksi').val(idT);
        $('#formTupoksi button[type="submit"]').html('<i class="fa fa-save"></i> Ubah');
        $('#tupoksi').focus();
    });


    // Submit form (insert/update)
    $('#formTupoksi').off('submit').on('submit', function(e) {
        e.preventDefault();
        const url = $('#id_tupoksi').val() ?
            "<?= base_url('referensi/jabatan/updateTupoksi') ?>" :
            $(this).attr('action');

        $.post(url, $(this).serialize(), function(res) {
            if (res.status === 'saved' || res.status === 'updated') {
                initTupoksi();
                $('#formTupoksi')[0].reset();
                $('#id').val(idJabatan);
                $('#id_tupoksi').val('');
                $('#formTupoksi button[type="submit"]').html(
                    '<i class="fa fa-save"></i> Tambah');
                showToast('Sukses', res.message, 'success', 1500);
            } else {
                showToast('Gagal', res.message, 'danger', 1500);
            }
        });
    });

    // Hapus tupoksi
    $(document).on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus data?',
            text: "Data tupoksi ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                $.get("<?= base_url('referensi/jabatan/deleteTupoksi') ?>/" + id, function(
                    res) {
                    if (res.status === 'deleted') {
                        initTupoksi();
                        showToast('Terhapus', res.message, 'success', 1500);
                    }
                });
            }
        });
    });

});
</script>