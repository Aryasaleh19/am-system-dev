<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-sm float-end btn-outline-success pull-right mb-3"
                        id="addBtn">Tambah</button>
                    <table id="jabatanTable" class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">AGAMA</th>
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
    // === DataTable Jabatan ===
    table = $('#jabatanTable').DataTable({
        paging: false,
        scrollCollapse: true,
        scrollY: '50vh',
        serverSide: true,
        processing: true,
        ajax: '<?= base_url('referensi/jabatan/ajaxList') ?>',
        columns: [{
                data: null,
                class: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'JABATAN'
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
                  <button type="button" class="btn btn-sm btn-outline-primary pengaturanBtn" data-id="${data.ID}">🎖️ Pengaturan</button>
                  <button type="button" class="btn btn-sm btn-outline-warning editBtn" data-id="${data.ID}">✏️ Edit</button>
                  <button type="button" class="btn btn-sm btn-outline-danger deleteBtn" data-id="${data.ID}">❌ Hapus</button>
                </div>`
            }
        ]
    });

    // === Tombol Tambah ===
    $('#addBtn').on('click', function() {
        $.get('<?= base_url('referensi/jabatan/form') ?>', function(res) {
            $('.viewmodal').html(res);
            const modalEl = document.getElementById('groupModal');
            const modal = new bootstrap.Modal(modalEl);
            $('#labelModal').text('🎖️ Tambah Jabatan');
            modal.show();
        });
    });

    // === Tombol Edit ===
    $('#jabatanTable').on('click', '.editBtn', function() {
        const id = $(this).data('id');
        $.get('<?= base_url('referensi/jabatan/get') ?>/' + id, function(data) {
            $.get('<?= base_url('referensi/jabatan/form') ?>', function(res) {
                $('.viewmodal').html(res);
                $('#labelModal').text('🎖️ Ubah Jabatan');
                $('#groupModal #id').val(data.ID);
                $('#groupModal #jabatan').val(data.JABATAN);
                $('#groupModal #status').val(data.STATUS);
                const modal = new bootstrap.Modal(document.getElementById(
                    'groupModal'));
                modal.show();
            });
        });
    });

    // === Tombol Pengaturan (Fullscreen Modal) ===
    $('#jabatanTable').on('click', '.pengaturanBtn', function() {
        const id = $(this).data('id');

        $.get('<?= base_url('referensi/jabatan/formPengaturan') ?>', function(res) {
            $('.viewmodal').append(res);
            $('#modalPengaturan #id').val(id); // kirim id jabatan ke modal
            const modal = new bootstrap.Modal(document.getElementById('modalPengaturan'));
            modal.show();

            // trigger default tab
            $('#modalPengaturan .nav-tabs button[data-bs-target="penerimaan"]').trigger(
            'click');
        });
    });


    // === Tombol Delete ===
    $('#jabatanTable').on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: "Yakin Hapus?",
            text: "Data akan dihapus!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.get('<?= base_url('referensi/jabatan/delete') ?>/' + id, function() {
                    table.ajax.reload();
                    Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                });
            }
        });
    });

});
</script>



<?= $this->endSection() ?>