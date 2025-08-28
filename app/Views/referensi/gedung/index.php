<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-sm float-end btn-outline-success pull-right mb-3" id="addBtn">➕
                        Tambah</button>
                    <table id="gedungTable" class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">GEDUNG</th>
                                <th class="text-center">JML. RUANGAN</th>
                                <th class="text-center">STATUS</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="viewmodal"></div>
<script>
var table;

$(document).ready(function() {
    // Inisialisasi DataTable Gedung
    table = $('#gedungTable').DataTable({
        paging: true, // aktifkan paging kalau pakai serverSide
        serverSide: true,
        processing: true,
        scrollCollapse: true,
        scrollY: '50vh',
        ajax: {
            url: '<?= base_url('referensi/gedung/ajaxList') ?>',
            data: function(d) {
                // Cache busting
                d._ = new Date().getTime();
            }
        },
        columns: [{
                data: null,
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'GEDUNG'
            },
            {
                data: 'JML_RUANGAN',
                className: 'text-center'
            },
            {
                data: 'STATUS',
                className: 'text-center',
                render: function(data) {
                    return data == 1 ?
                        '<span class="badge bg-label-success me-1">Aktif</span>' :
                        '<span class="badge bg-label-danger me-1">Tidak Aktif</span>';
                }
            },
            {
                data: null,
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-outline-warning editBtn" data-id="${data.ID}">✏️ Edit</button>
                        <button type="button" class="btn btn-xs btn-outline-danger deleteBtn" data-id="${data.ID}">❌ Hapus</button>
                        <button type="button" class="btn btn-xs btn-outline-success mapingBtn" data-id="${data.ID}">🚪 Maping Ruangan</button>
                    </div>
                `;
                }
            }
        ]
    });



    // Tombol Tambah Gedung
    $('#addBtn').on('click', function() {
        $.ajax({
            type: "GET",
            url: "<?= base_url('referensi/gedung/form') ?>",
            dataType: "html",
            success: function(response) {
                $('.viewmodal').html(response);
                const modalEl = document.getElementById('gedungModal');
                const modal = new bootstrap.Modal(modalEl);

                $('#gedungModalLabel').text('Tambah Group Modul');
                $('#ID').val('');
                $('#MODUL').val('');
                $('#STATUS').val('1');

                modal.show();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert('Gagal memuat form');
            }
        });
    });

    // Tombol Edit Gedung
    $('#gedungTable').on('click', '.editBtn', function() {
        const id = $(this).data('id');
        $.get('<?= base_url('referensi/gedung/get') ?>/' + id, function(data) {
            if (!$('.viewmodal').html()) {
                $.ajax({
                    url: "<?= base_url('referensi/gedung/form') ?>",
                    success: function(response) {
                        $('.viewmodal').html(response);
                        isiFormEdit(data);
                    }
                });
            } else {
                isiFormEdit(data);
            }
        });

        function isiFormEdit(data) {
            $('#gedungModalLabel').text('Edit Group Modul');
            $('#id').val(data.ID);
            $('#gedung').val(data.GEDUNG);
            $('#status').val(data.STATUS);
            const modalEl = document.getElementById('gedungModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });

    // Tombol Maping Ruangan
    $('#gedungTable').on('click', '.mapingBtn', function() {
        const id = $(this).data('id');
        $.get('<?= base_url('referensi/gedung/mapingruangan') ?>/' + id, function(data) {
            $.ajax({
                url: "<?= base_url('referensi/gedung/formMaping') ?>",
                success: function(response) {
                    $('.viewmodal').html(response);

                    const waitForModal = setInterval(() => {
                        const modalEl = document.getElementById(
                            'mapingModal');
                        if (modalEl) {
                            clearInterval(waitForModal);

                            Swal.close();
                            $('#gedungModalLabel').html(
                                '🚪 Maping Ruangan : <strong class="text-info">' +
                                data.GEDUNG + '</strong>');
                            $('#gedung_id').val(data.ID);

                            const modal = new bootstrap.Modal(modalEl);
                            modal.show();

                            // Inisialisasi ulang seluruh script maping setelah modal masuk DOM
                            initMapingScript();
                        }
                    }, 10);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal memuat form',
                        text: 'Terjadi kesalahan saat mengambil form maping.'
                    });
                }
            });
        });
    });

    // Tombol Hapus Gedung
    $('#gedungTable').on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        if (confirm('Yakin ingin menghapus?')) {
            $.get('<?= base_url('referensi/gedung/delete') ?>/' + id, () => {
                table.ajax.reload();
            });
        }
    });

    // Submit Form Gedung
    $('#modulForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const url = $('#ID').val() ?
            '<?= base_url('referensi/gedung/update') ?>' :
            '<?= base_url('referensi/gedung/store') ?>';

        $.post(url, formData, function() {
            $('#gedungModal').modal('hide');
            table.ajax.reload();
        });
    });
});

function initMapingScript() {
    const gedungId = $('#gedung_id').val();

    if ($.fn.DataTable.isDataTable('#ruanganTable')) {
        $('#ruanganTable').DataTable().clear().destroy();
    }

}
</script>


<?= $this->endSection() ?>