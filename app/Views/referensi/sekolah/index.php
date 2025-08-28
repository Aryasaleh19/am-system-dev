<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-sm float-end btn-outline-success pull-right mb-3"
                        id="addBtn">Tambah</button>
                    <table id="sekolahTable" class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">KODE</th>
                                <th class="text-center">SEKOLAH / PENDIDIKAN</th>
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
    table = $('#sekolahTable').DataTable({
        paging: false,
        scrollCollapse: true,
        scrollY: '50vh',
        serverSide: true,
        processing: true,
        scrollCollapse: true,
        ajax: '<?= base_url('referensi/sekolah/ajaxList') ?>',
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
                data: 'KODE',
                class: 'text-center'
            },
            {
                data: 'NAMA_SEKOLAH'
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
                    <button type="button" class="btn btn-xs btn-outline-warning editBtn" data-id="${data.ID}">✏️ Edit</button>
                    <button type="button" class="btn btn-xs btn-outline-danger deleteBtn" data-id="${data.ID}">❌ Hapus</button>
                </div>
                `
            }
        ]
    });

    // Tombol Tambah
    $('#addBtn').on('click', function() {
        $.ajax({
            type: "GET",
            url: "<?= base_url('referensi/sekolah/form') ?>",
            dataType: "html",
            success: function(response) {
                $('.viewmodal').html(response);
                const modalEl = document.getElementById('groupModal');
                const modal = new bootstrap.Modal(modalEl);

                // Reset form dan judul modal
                $('#labelModal').text('🏫 Tambah Sekolah');
                $('#id').val('');
                $('#kode').val('');
                $('#nama_sekolah').val('');
                $('#status').val('1');

                modal.show();
            },
            error: function(xhr) {
                let errorTitle = 'Gagal';
                let errorText = 'Terjadi kesalahan pada server.';

                try {
                    // coba parse response JSON dari server
                    const res = JSON.parse(xhr.responseText);
                    if (res.code) {
                        errorTitle += ' | ' + res.code;
                    }
                    if (res.message) {
                        errorText = res.message;
                    }
                } catch (e) {
                    // kalau gagal parsing, biarkan default pesan
                }

                Swal.fire({
                    icon: 'error',
                    title: errorTitle,
                    text: errorText
                });

                console.error(xhr.responseText);
            }
        });
    });

    // Tombol Edit pada tabel
    $('#sekolahTable').on('click', '.editBtn', function() {
        const id = $(this).data('id');
        $.get('<?= base_url('referensi/sekolah/get') ?>/' + id, function(data) {
            if (!$('.viewmodal').html()) {
                // Load modal form dulu kalau belum ada (opsional)
                $.ajax({
                    url: "<?= base_url('referensi/sekolah/form') ?>",
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
            $('#labelModal').text('🏫 Edit Sekolah');
            $('#id').val(data.ID);
            $('#kode').val(data.KODE);
            $('#nama_sekolah').val(data.NAMA_SEKOLAH);
            $('#status').val(data.STATUS);
            const modalEl = document.getElementById('groupModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });

    $('#sekolahTable').on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: "Yakin Hapus?",
            text: "Anda akan menghapus data!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, lanjut hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                $.get('<?= base_url('referensi/sekolah/delete') ?>/' + id, () => {
                    Swal.fire({
                        title: "Terhapus!",
                        text: "Data berhasil dihapus.",
                        icon: "success"
                    });
                    table.ajax.reload();
                });
            }
        });
    });

    $('#modulForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const url = $('#ID').val() ? '<?= base_url('referensi/sekolah/update') ?>' :
            '<?= base_url('referensi/sekolah/store') ?>';


        $.post(url, formData, function() {
            $('#groupModal').modal('hide');
            table.ajax.reload();
        });
    });
});
</script>


<?= $this->endSection() ?>