<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table id="pegawaiTable" class="table table-sm table-hover table-bordered w-100 table-responsive"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center">NO.</th>
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
        scrollY: '50vh',
        serverSide: true,
        processing: true,
        scrollCollapse: true,
        ajax: '<?= site_url('kepegawaian/managemenakses/ajaxList') ?>',
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
                data: 'NIK',
                className: 'text-center'
            },
            {
                data: 'NAMA'
            },
            {
                data: 'JENIS_KELAMIN',
                className: 'text-center',
                render: data => data === 'L' ? 'Laki-laki' : 'Perempuan'
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
                            <button type="button" title="Managemen Akses" class="btn btn-xs btn-outline-primary btn-akses" data-id="${data.ID}">🔒 Akses</button>
                        </div>`;
                }
            }
        ]
    });

    $('#pegawaiTable').on('click', '.btn-akses', function() {
        const id = $(this).data('id');

        // Load view modal
        $.get('<?= site_url('kepegawaian/managemenakses/formakun') ?>', function(view) {
            $('.viewmodal').html(view).show();

            // Tampilkan modal setelah view dimuat
            $('#formAkun').modal('show').on('shown.bs.modal', function() {

                // Load data akun
                $.getJSON('<?= site_url('kepegawaian/managemenakses/get_akun/') ?>' +
                        id)
                    .done(function(data) {
                        // reset semua checkbox dulu
                        $('#formAkun input[type=checkbox]').prop('checked', false);

                        // isi field hidden manual
                        $('[name="PEGAWAI_ID"]').val(data.PEGAWAI_ID ?? data.ID);
                        $('[name="USERNAME"]').val(data.USERNAME ?? '');
                        $('[name="NIK"]').val(data.NIK ?? '');

                        // isi field biasa (jika ada)
                        for (let key in data) {
                            $(`[name="${key}"]`).val(data[key]).trigger('change');
                        }

                        const usernameInput = $('[name="USERNAME"]');
                        if (usernameInput.val() !== '') {
                            usernameInput.prop('readonly', true);
                        }

                        // ✅ centang checkbox sesuai akses
                        if (data.jabatan) {
                            data.jabatan.forEach(id => $('#jabatan' + id).prop(
                                'checked', true));
                        }
                        if (data.modul) {
                            data.modul.forEach(id => $('#modul' + id).prop(
                                'checked', true));
                        }
                        if (data.ruangan) {
                            data.ruangan.forEach(id => $('#ruangan' + id).prop(
                                'checked', true));
                        }

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

});
</script>


<?= $this->endSection() ?>