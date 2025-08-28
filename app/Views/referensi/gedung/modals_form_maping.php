<!-- modal_form.php -->
<div class="modal fade" id="mapingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="gedungModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gedungModalLabel">Tambah Ruangan</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Form -->
                    <div class="col-md-4">
                        <form id="mapingRuangan" method="post">
                            <input type="hidden" name="id" id="id">
                            <input type="hidden" name="gedung_id" id="gedung_id" required>
                            <div class="mb-3">
                                <label for="ruangan" class="form-label">Nama Ruangan</label>
                                <input type="text" class="form-control" name="ruangan" id="ruangan"
                                    placeholder="Nama ruangan" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control form-select" name="status" id="status" required>
                                    <option value="">[ Pilih ]</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-save"></i> Simpan
                                </button>
                                <button type="reset" class="btn btn-sm btn-outline-info btnReset">
                                    <i class="fas fa-sync    "></i> Reset
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">
                                    <i class="fa fa-window-close"></i> Tutup
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Table -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="fa fa-table"></i> Daftar Ruangan
                            </div>
                            <div class="card-body">
                                <table id="ruanganTable" class="table table-sm table-bordered table-striped w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-center">NO</th>
                                            <th class="text-center">RUANGAN</th>
                                            <th class="text-center">STATUS</th>
                                            <th class="text-center">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<script>
function initTableRuangan(gedungId) {
    if ($.fn.DataTable.isDataTable('#ruanganTable')) {
        tableRuangan.clear().destroy();
    }

    tableRuangan = $('#ruanganTable').DataTable({
        paging: false,
        scrollCollapse: true,
        scrollY: '50vh',
        serverSide: false,
        ajax: {
            url: '<?= base_url('referensi/ruangan/ajaxListRuangan') ?>/' + gedungId,
            dataSrc: 'data',
            error: function() {
                console.warn('Gagal memuat data ruangan');
            }
        },
        columns: [{
                data: null,
                className: 'text-center',
                orderable: false,
                render: (data, type, row, meta) => meta.row + 1
            },
            {
                data: 'RUANGAN'
            },
            {
                data: 'STATUS',
                className: 'text-center',
                render: data => data == 1 ?
                    '<span class="badge bg-label-success">Aktif</span>' :
                    '<span class="badge bg-label-danger">Tidak Aktif</span>'
            },
            {
                data: null,
                className: 'text-center',
                render: data => `
                    <button class="btn btn-xs btn-outline-warning editBtn" data-id="${data.ID}">Edit</button>
                    <button class="btn btn-xs btn-outline-danger deleteBtn" data-id="${data.ID}">Hapus</button>
                `
            }
        ]
    });
}

$(document).ready(function() {
    disableAutocomplete();

    // Reset form
    $('.btnReset').click(function(e) {
        e.preventDefault();
        const currentGedungId = $('#gedung_id').val();

        $('#mapingRuangan')[0].reset();
        $('#gedung_id').val(currentGedungId); // set ulang
    });

    // Saat modal ditampilkan
    $('#mapingModal').on('shown.bs.modal', function() {
        const gedungId = $('#gedung_id').val();
        initTableRuangan(gedungId);
    });

    // Bersihkan saat modal ditutup
    $('#mapingModal').on('hidden.bs.modal', function() {
        $('#mapingRuangan')[0].reset();
        $('#id').val('');
        if (tableRuangan) {
            tableRuangan.clear().draw();
        }
    });

    let submitting = false;

    $('#mapingRuangan').off('submit').on('submit', function(e) {
        e.preventDefault();

        if (submitting) return; // cegah submit ulang sebelum selesai

        submitting = true;
        const $form = $(this);
        const $btnSubmit = $form.find('button[type="submit"]');
        $btnSubmit.prop('disabled', true);

        const id = $('#id').val();
        const url = id ? "<?= base_url('referensi/ruangan/update') ?>" :
            "<?= base_url('referensi/ruangan/simpan') ?>";

        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                submitting = false;
                $btnSubmit.prop('disabled', false);

                if (response.status === 'saved' || response.status === 'updated') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Data berhasil disimpan!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    const currentGedungId = $('#gedung_id').val();
                    $form[0].reset();
                    $('#gedung_id').val(currentGedungId);
                    if (tableRuangan) {
                        tableRuangan.ajax.reload(null, false);
                        $('#gedungTable').DataTable().ajax.reload();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Terjadi kesalahan saat menyimpan'
                    });
                }
            },
            error: function(xhr) {
                submitting = false;
                $btnSubmit.prop('disabled', false);
                console.error('AJAX error:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan pada server'
                });
            }
        });
    });


    // Edit button
    $('#ruanganTable').on('click', '.editBtn', function() {
        const id = $(this).data('id');

        $.get('<?= base_url('referensi/ruangan/get') ?>/' + id, function(data) {
            if (data) {
                $('#id').val(data.ID);
                $('#ruangan').val(data.RUANGAN);
                $('#status').val(data.STATUS);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Data tidak ditemukan'
                });
            }
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal mengambil data'
            });
        });
    });

    // Delete button
    $('#ruanganTable').on('click', '.deleteBtn', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Hapus data?',
            text: 'Data yang dihapus tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('referensi/ruangan/delete') ?>/' + id,
                    type: 'GET',
                    success: function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil dihapus.'
                        });

                        const currentGedungId = $('#gedung_id').val();
                        $('#mapingRuangan')[0].reset();
                        $('#gedung_id').val(currentGedungId);
                        if (tableRuangan) {
                            tableRuangan.ajax.reload(null, false);
                            $('#gedungTable').DataTable().ajax.reload();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menghapus.'
                        });
                    }
                });
            }
        });
    });
});
</script>