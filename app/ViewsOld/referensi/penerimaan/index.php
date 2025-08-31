<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-sm float-end btn-outline-success pull-right mb-3"
                        id="addBtn">Tambah</button>
                    <table id="jenisTable" class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">NO</th>
                                <th class="text-center">PENDIDIKAN</th>
                                <th class="text-center">JENIS PENERIMAAN</th>
                                <th class="text-center">JUMLAH (Rp)</th>
                                <th class="text-center">SUMBER/KATEGORI</th>
                                <th class="text-center">TENOR (x)</th>
                                <th class="text-center">SATUAN</th>
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
var groupColumn = 1;
$(document).ready(function() {
    table = $('#jenisTable').DataTable({
        paging: false,
        serverSide: true,
        processing: false,
        columnDefs: [{
            visible: false,
            targets: groupColumn
        }],
        order: [
            [groupColumn, 'asc'],
            [0, 'asc']
        ],
        ajax: '<?= base_url('referensi/jenispenerimaan/ajaxList') ?>',
        columns: [{
                data: null,
                class: 'text-center',
                orderable: false,
                searchable: false,
                render: function() {
                    return ''; // Kosong dulu
                }
            },
            {
                data: 'NAMA_SEKOLAH',
                class: 'text-center',
                orderable: false,
            },
            {
                data: 'JENIS_PENERIMAAN',
                orderable: false,
            },
            {
                data: 'JUMLAH',
                class: 'text-end',
                orderable: false,
                render: function(data, type, row) {
                    return formatRupiah(data);
                }
            },
            {
                data: 'KATEGORI',
                class: 'text-center',
                orderable: false,
                render: data => data == 'Formal' ?
                    '<span class="badge bg-label-primary me-1">Formal</span>' :
                    '<span class="badge bg-label-info me-1">Non Formal</span>'
            },
            {
                data: 'TENOR',
                class: 'text-center',
                orderable: false,
                render: function(data) {
                    return data ? data + 'x' : '-';
                }
            },
            {
                data: 'SATUAN',
                class: 'text-center',
                orderable: false,
                render: data => data == 'Tahun' ?
                    '<span class="badge bg-label-primary me-1">Tahun</span>' : data == 'Bulan' ?
                    '<span class="badge bg-label-info me-1">Bulan</span>' :
                    '<span class="badge bg-label-warning me-1">Periodik</span>'
            },
            {
                data: 'STATUS',
                class: 'text-center',
                orderable: false,
                render: data => data == 1 ?
                    '<span class="badge bg-label-success me-1">Aktif</span>' :
                    '<span class="badge bg-label-danger me-1">Tidak Aktif</span>'
            },
            {
                data: null,
                class: 'text-center',
                orderable: false,
                render: data => `
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-warning editBtn" data-id="${data.ID}">✏️ Edit</button>
                    <button type="button" class="btn btn-sm btn-outline-danger deleteBtn" data-id="${data.ID}">❌ Hapus</button>
                </div>
                `
            }
        ],
        drawCallback: function(settings) {
            var api = this.api();
            var rows = api.rows({
                page: 'current'
            }).nodes();
            var last = null;

            // Hapus grup lama
            $('#jenisTable tbody tr.group').remove();

            // Buat grup dengan icon plus
            api.column(groupColumn, {
                    page: 'current'
                })
                .data()
                .each(function(group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                            `<tr class="group" style="cursor:pointer; background-color: #FFFBDE">
                        <td colspan="8">
                            <i class="fa fa-plus-circle text-info btn-toggle" style="font-size:16px; margin-right:8px;"></i> Lembaga ${group}
                        </td>
                    </tr>`
                        );
                        last = group;
                    }
                });

            // Sembunyikan semua baris data
            $(rows).each(function() {
                if (!$(this).hasClass('group')) {
                    $(this).hide();
                }
            });

            // Set nomor urut per grup (reset ke 1 setiap grup)
            var groupStartIndex = 0;
            var groupCount = 0;
            var currentGroup = null;

            api.column(groupColumn, {
                page: 'current'
            }).data().each(function(group, i) {
                if (group !== currentGroup) {
                    currentGroup = group;
                    groupStartIndex = 1; // reset nomor urut untuk grup baru
                }
                // Set nomor di kolom 0 (nomor urut) untuk baris ke-i
                var rowNode = api.row(i).node();
                // Isi kolom pertama (index 0) dengan nomor urut groupStartIndex
                if (rowNode) {
                    // Pastikan hanya isi baris yang visible (kalau mau)
                    $(rowNode).find('td').eq(0).html(groupStartIndex);
                }
                groupStartIndex++;
            });
        }


    });

    // Order by the grouping
    $('#jenisTable tbody').on('click', 'tr.group', function() {
        var $groupRow = $(this);
        var $icon = $groupRow.find('i.btn-toggle');
        var $nextRows = $groupRow.nextUntil('tr.group');

        if ($icon.hasClass('fa-plus-circle')) {
            // Buka grup ini
            $icon
                .removeClass('fa-plus-circle text-info')
                .addClass('fa-minus-circle text-danger');
            $nextRows.show();

            // Tutup grup lain yang terbuka
            $('#jenisTable tbody i.btn-toggle.fa-minus-circle').not($icon).each(function() {
                var $otherIcon = $(this);
                var $otherGroupRow = $otherIcon.closest('tr.group');
                var $otherNextRows = $otherGroupRow.nextUntil('tr.group');

                $otherIcon
                    .removeClass('fa-minus-circle text-danger')
                    .addClass('fa-plus-circle text-info');
                $otherNextRows.hide();
            });
        } else {
            // Tutup grup ini
            $icon
                .removeClass('fa-minus-circle text-danger')
                .addClass('fa-plus-circle text-info');
            $nextRows.hide();
        }
    });



    // Tombol Tambah
    $('#addBtn').on('click', function() {
        $.ajax({
            type: "GET",
            url: "<?= base_url('referensi/jenispenerimaan/form') ?>",
            dataType: "html",
            success: function(response) {
                $('.viewmodal').html(response);
                const modalEl = document.getElementById('groupModal');
                const modal = new bootstrap.Modal(modalEl);

                // Reset form dan judul modal
                $('#labelModal').text('💵 Tambah Jenis Penerimaan');
                $('#id').val('');
                $('#jenis').val('');
                $('#jumlah').val('');
                $('#kategori').val('');
                $('#tenor').val('');
                $('#satuan').val('');
                $('#sekolah_id').val('');
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
    $('#jenisTable').on('click', '.editBtn', function() {
        const id = $(this).data('id');
        $.get('<?= base_url('referensi/jenispenerimaan/get') ?>/' + id, function(data) {
            if (!$('.viewmodal').html()) {
                // Load modal form dulu kalau belum ada (opsional)
                $.ajax({
                    url: "<?= base_url('referensi/jenispenerimaan/form') ?>",
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
            $('#groupModalLabel').text('💵 Edit Jenis Penerimaan');
            $('#id').val(data.ID);
            $('#jenis').val(data.JENIS_PENERIMAAN);
            $('#jumlah').val(data.JUMLAH);
            $('#kategori').val(data.KATEGORI);
            $('#tenor').val(data.TENOR);
            $('#satuan').val(data.SATUAN);
            $('#sekolah_id').val(data.SEKOLAH_ID);
            $('#status').val(data.STATUS);
            const modalEl = document.getElementById('groupModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });

    $('#jenisTable').on('click', '.deleteBtn', function() {
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
                $.get('<?= base_url('referensi/jenispenerimaan/delete') ?>/' + id, () => {
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
        const url = $('#ID').val() ? '<?= base_url('referensi/jenispenerimaan/update') ?>' :
            '<?= base_url('referensi/jenispenerimaan/store') ?>';


        $.post(url, formData, function() {
            $('#groupModal').modal('hide');
            table.ajax.reload();
        });
    });
});
</script>


<?= $this->endSection() ?>