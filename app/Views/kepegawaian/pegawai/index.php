<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-sm float-end btn-outline-success pull-right mb-3"
                        id="addBtn">🧕➕ Pegawai Baru</button>
                    <table id="pegawaiTable" class="table table-sm table-hover table-bordered w-100 table-responsive">
                        <thead>
                            <tr>
                                <th class="text-center">NO.</th>
                                <th class="text-center">NIPY</th>
                                <th class="text-center">NIK</th>
                                <th class="text-center">NAMA</th>
                                <th class="text-center">KELAMIN</th>
                                <th class="text-center">JABATAN</th>
                                <th class="text-center">PROFESI</th>
                                <th class="text-center">JENIS</th>
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
let table; // Buat global agar bisa dipanggil ulang

$(document).ready(function() {
    table = $('#pegawaiTable').DataTable({
        paging: true,
        scrollCollapse: true,
        scrollY: '55vh',
        serverSide: true,
        processing: true,
        scrollCollapse: true,
        withInfo: true,
        ajax: '<?= site_url('kepegawaian/pegawai/ajaxList') ?>',
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
                data: 'NIP',
                className: 'text-center'
            },
            {
                data: 'NIK',
                className: 'text-center'
            },
            {
                data: 'NAMA'
            },
            {
                data: 'JENIS_KELAMIN',
                className: 'text-center',
                render: data => data === 'L' ?
                    '<span class="badge bg-label-warning">Laki-laki</span>' :
                    '<span class="badge bg-label-success">Perempuan</span>'
            },
            {
                data: 'JABATAN'
            },
            {
                data: 'PROFESI'
            },
            {
                data: 'JENIS_PEGAWAI'
            },
            {
                data: 'AKTIF',
                className: 'text-center',
                render: status => status == 1 ?
                    '<span class="badge bg-success">Aktif</span>' :
                    '<span class="badge bg-danger">Tidak Aktif</span>'
            },
            {
                data: null,
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `
                        <div class="btn-group">
                            <button type="button" title="Ubah data" class="btn btn-xs btn-outline-warning edit" data-id="${data.ID}">✏️</button>
                            <button type="button" title="Hapus Data" class="btn btn-xs btn-outline-danger delete" data-id="${data.ID}">🗑️</button>
                            <button type="button" title="Akun" class="btn btn-xs btn-outline-primary akun" data-id="${data.ID}">🔒</button>
                        </div>`;
                }
            }
        ]
    });

    // Tombol Tambah Pegawai
    $('#addBtn').click(function() {
        $.get('<?= site_url('kepegawaian/pegawai/form') ?>', function(view) {
            $('.viewmodal').html(view).show();
            $('#pegawaiModal').modal('show');
        });
    });

    $('#pegawaiTable').on('click', '.akun', function() {
        const id = $(this).data('id');

        // Load view modal
        $.get('<?= site_url('kepegawaian/pegawai/formakun') ?>', function(view) {
            $('.viewmodal').html(view).show();

            // Tampilkan modal setelah view dimuat
            $('#pegawaiAkun').modal('show').on('shown.bs.modal', function() {

                // Load data akun
                $.getJSON('<?= site_url('kepegawaian/pegawai/get_akun/') ?>' + id)
                    .done(function(data) {
                        for (let key in data) {
                            $(`[name="${key}"]`).val(data[key]).trigger('change');
                        }

                        const usernameInput = $('[name="USERNAME"]');
                        if (usernameInput.val() !== '') {
                            usernameInput.prop('readonly', true);
                        }


                        // Tutup loader jika sukses
                        Swal.close();
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        let errorMsg = 'Terjadi kesalahan saat memuat akun.';
                        let errorCode = jqXHR.status; // HTTP status code
                        let responseText = jqXHR.responseText;

                        // Coba parsing JSON dari response (kalau ada)
                        try {
                            let json = JSON.parse(responseText);
                            if (json.message) errorMsg = json.message;
                            if (json.code) errorCode = json.code;
                        } catch (e) {
                            // jika bukan JSON, pakai responseText biasa
                        }

                        Swal.fire({
                            icon: 'error',
                            title: `Gagal mengambil data (Code: ${errorCode})`,
                            html: `<pre style="text-align:left;">${errorMsg}</pre>`,
                            confirmButtonText: 'Tutup'
                        });
                    });

            });
        });
    });

    // Tombol Edit
    $('#pegawaiTable').on('click', '.edit', function() {
        const id = $(this).data('id');
        $.get('<?= site_url('kepegawaian/pegawai/form') ?>', function(view) {
            $('.viewmodal').html(view).show();
            $('#pegawaiModal').modal('show');

            // Load data ke form
            $.getJSON('<?= site_url('kepegawaian/pegawai/get/') ?>' + id, function(data) {
                for (let key in data) {
                    $(`[name="${key}"]`).val(data[key]).trigger('change');
                }
            });
        });
    });

    // Tombol Hapus
    $('#pegawaiTable').on('click', '.delete', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get('<?= site_url('kepegawaian/pegawai/delete/') ?>' + id, function(
                    response) {
                    if (response.status) {
                        Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                        table.ajax.reload();
                    } else {
                        Swal.fire('Gagal!', 'Tidak dapat menghapus data.', 'error');
                    }
                });
            }
        });
    });


});
</script>


<?= $this->endSection() ?>