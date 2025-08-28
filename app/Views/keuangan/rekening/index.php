<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-sm float-end btn-outline-success pull-right mb-3"
                        id="addBtn">Tambah</button>
                    <table id="rekeningTable" class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">NO.REKENING</th>
                                <th class="text-center">BANK</th>
                                <th class="text-center">SALDO AWAL</th>
                                <th class="text-center">SALDO AKHIR</th>
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
    table = $('#rekeningTable').DataTable({
        paging: false,
        scrollCollapse: true,
        scrollY: '50vh',
        serverSide: true,
        processing: true,
        scrollCollapse: true,
        ajax: '<?= base_url('keuangan/rekeningbank/ajaxList') ?>',
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
                data: 'NO_REKENING',
                class: 'text-center'
            },
            {
                data: 'NAMA_BANK',
                class: 'text-left'
            },
            {
                data: 'SALDO_AWAL',
                class: 'text-end',
                render: function(data, type, row) {
                    return formatRupiah(data);
                }
            },
            {
                data: 'SALDO_AKHIR',
                class: 'text-end',
                render: function(data, type, row) {
                    return formatRupiah(data);
                }
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
            url: "<?= base_url('keuangan/rekeningbank/form') ?>",
            dataType: "html",
            success: function(response) {
                $('.viewmodal').html(response);
                const modalEl = document.getElementById('groupModal');
                const modal = new bootstrap.Modal(modalEl);

                // Reset form dan judul modal
                $('#labelModal').text('🏦 Tambah Rekening Bank');
                $('#id').val('');
                $('#no_rekening').val('');
                $('#nama_bank').val('');
                $('#saldo_awal').val('');
                $('#status').val('1');

                modal.show();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert('Gagal memuat form');
            }
        });
    });

    // Tombol Edit pada tabel
    $('#rekeningTable').on('click', '.editBtn', function() {
        const id = $(this).data('id');
        $.get('<?= base_url('keuangan/rekeningbank/get') ?>/' + id, function(data) {
            if (!$('.viewmodal').html()) {
                $.ajax({
                    url: "<?= base_url('keuangan/rekeningbank/form') ?>",
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
            $('#labelModal').text('🏦 Edit Rekening Bank');
            $('#id').val(data.ID);
            $('#no_rekening').val(data.NO_REKENING);
            $('#bank').val(data.NAMA_BANK);
            $('#saldo_awal').val(data.SALDO_AWAL);
            $('#status').val(data.STATUS);
            const modalEl = document.getElementById('groupModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });

    $('#rekeningTable').on('click', '.deleteBtn', function() {
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
                $.get('<?= base_url('keuangan/rekeningbank/delete') ?>/' + id, () => {
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
        const url = $('#ID').val() ? '<?= base_url('keuangan/rekeningbank/update') ?>' :
            '<?= base_url('keuangan/rekeningbank/store') ?>';


        $.post(url, formData, function() {
            $('#groupModal').modal('hide');
            table.ajax.reload();
        });
    });
});
</script>

<?= $this->endSection() ?>