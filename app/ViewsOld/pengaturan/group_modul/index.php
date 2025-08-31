<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-sm float-end btn-outline-success pull-right mb-3"
                        id="addBtn">Tambah</button>
                    <table id="modulTable" class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">GROUP MODUL</th>
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
    table = $('#modulTable').DataTable({
        paging: false,
        scrollCollapse: true,
        scrollY: '50vh',
        serverSide: true,
        processing: true,
        scrollCollapse: true,
        ajax: '<?= base_url('pengaturan/group_modul/ajaxList') ?>',
        columns: [{
                data: null,
                class: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    // meta.row = index baris pada halaman sekarang (0-based)
                    // meta.settings._iDisplayStart = index start dari halaman saat ini
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'GROUP_MODUL'
            },
            {
                data: 'STATUS',
                class: 'text-center',
                render: data => data == 1 ?
                    '<span class="badge bg-label-success me-1">Aktif</span>' :
                    '<span class="badge bg-label-danger me-1">Tidak Aktif</span>'
            },
            {
                data: null,
                class: 'text-center',
                render: data => `
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-outline-warning editBtn" data-id="${data.ID}">Edit</button>
                    <button type="button" class="btn btn-xs btn-outline-danger deleteBtn" data-id="${data.ID}">Hapus</button>
                </div>
                `
            }
        ]
    });

    // Tombol Tambah
    $('#addBtn').on('click', function() {
        $.ajax({
            type: "GET",
            url: "<?= base_url('pengaturan/group_modul/form') ?>",
            dataType: "html",
            success: function(response) {
                $('.viewmodal').html(response);
                const modalEl = document.getElementById('groupModal');
                const modal = new bootstrap.Modal(modalEl);

                // Reset form dan judul modal
                $('#groupModalLabel').text('Tambah Group Modul');
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

    // Tombol Edit pada tabel
    $('#modulTable').on('click', '.editBtn', function() {
        const id = $(this).data('id');
        $.get('<?= base_url('pengaturan/group_modul/get') ?>/' + id, function(data) {
            if (!$('.viewmodal').html()) {
                // Load modal form dulu kalau belum ada (opsional)
                $.ajax({
                    url: "<?= base_url('pengaturan/group_modul/form') ?>",
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
            $('#groupModalLabel').text('Edit Group Modul');
            $('#id').val(data.ID);
            $('#modul').val(data.GROUP_MODUL);
            $('#status').val(data.STATUS);
            const modalEl = document.getElementById('groupModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });

    $('#modulTable').on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        if (confirm('Yakin ingin menghapus?')) {
            $.get('<?= base_url('pengaturan/group_modul/delete') ?>/' + id, () => {
                table.ajax.reload();
            });
        }
    });

    $('#modulForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const url = $('#ID').val() ? '<?= base_url('pengaturan/group_modul/update') ?>' :
            '<?= base_url('pengaturan/group_modul/store') ?>';


        $.post(url, formData, function() {
            $('#groupModal').modal('hide');
            table.ajax.reload();
        });
    });
});
</script>


<?= $this->endSection() ?>