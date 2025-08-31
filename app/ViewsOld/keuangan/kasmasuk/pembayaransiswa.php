<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-6">
                    <small>Filter Data: </small>
                    <div class="input-group input-group-sm">
                        <select id="filterFrom" class="w-10 form-select form-select-sm text-center" style="width: 20%;"
                            aria-label="Tahun angkatan dari">
                            <option value="">Dari Tahun</option>
                        </select>
                        <select id="filterTo" class="w-10 form-select form-select-sm text-center" style="width: 20%;"
                            aria-label="Sampai dengan">
                            <option value="">Sampai Tahun</option>
                        </select>
                        <select id="filterSekolah" class="w-10 form-select form-select-sm text-center"
                            style="width: 40%;" aria-label="Sampai dengan">
                            <option value="">[ Semua Pendidikan ]</option>
                        </select>
                        <button class="btn btn-outline-info w-20" id="filterBtn" type="button"><i class="fa fa-search"
                                aria-hidden="true"></i> Cari</button>
                    </div>
                </div>
                <div class="col-6">
                    <button type="button" class="btn float-end btn-outline-success pull-right mb-3" id="addBtn">
                        <i class="fa fa-table" aria-hidden="true"></i> 👨Kas Per
                        Angkatan</button>
                </div>
            </div>
            <table id="angkatanTable" class="table table-sm table-hover table-bordered focusable-table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">NO.YAYASAN</th>
                        <th class="text-center">NIS</th>
                        <th class="text-center">NAMA SISWA</th>
                        <th class="text-center">ANGKATAN</th>
                        <th class="text-center">PENDIDIKAN SAAT INI</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="viewmodal"></div>
<script src="<?= base_url('js/referensi.js') ?>"></script>
<script>
$('#addBtn').on('click', function() {
    $.ajax({
        type: "GET",
        url: "<?= base_url('keuangan/pembayaransiswa/formpembayaranangkatan') ?>",
        dataType: "html",
        success: function(response) {
            $('.viewmodal').html(response);
            const modalEl = document.getElementById('modalformSiswaBaru');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            $('#labelModal').html(
                '<i class="fa fa-table" aria-hidden="true"></i> 👨Kas Siswa / Angkatan');
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            alert('Gagal memuat form');
        }
    });
});


$('#filterBtn').on('click', function() {
    const from = $('#filterFrom').val();
    const to = $('#filterTo').val();
    const sekolah = $('#filterSekolah').val();
    $('#angkatanTable').DataTable().ajax.url(
            `<?= base_url('keuangan/pembayaransiswa/ajaxList') ?>?from=${from}&to=${to}&sekolah=${sekolah}`)
        .load();
});


function detailSiswa(nis) {
    showLoader();
    $.ajax({
        type: "GET",
        url: "<?= base_url('keuangan/pembayaransiswa/modaldetailsiswa') ?>",
        dataType: "html",
        success: function(modalHtml) {
            // Masukkan modal ke DOM
            $('.viewmodal').html(modalHtml);

            // 2. Setelah modal ada di DOM, ambil data siswa
            $.ajax({
                type: "GET",
                url: "<?= base_url('keuangan/pembayaransiswa/getDetail/') ?>" + encodeURIComponent(
                    nis),
                dataType: "json",
                success: function(data) {
                    hideLoader();
                    console.log(data.NIS);
                    $('#modalformSiswaBaru .siswa-nis').val(data.NIS);

                    $('#labelModal').html(
                        '<i class="fa fa-info" aria-hidden="true"></i> 👨 Detail Kas Siswa <span class="badge bg-label-info">[' +
                        data.NIS +
                        '] ' + data
                        .NAMA + '</span>');

                    // 3. Tampilkan modal
                    const modalEl = document.getElementById('modalformSiswaBaru');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                },
                error: function(xhr) {
                    hideLoader();
                    Swal.fire('Gagal', xhr.responseText || 'Gagal mengambil data siswa',
                        'error');
                }
            });
        },
        error: function() {
            hideLoader();
            Swal.fire('Gagal', 'Gagal memuat modal detail', 'error');
        }
    });
}


// Fungsi untuk tampilkan loader
function showLoader() {
    if ($('#loaderOverlay').length === 0) {
        $('body').append(`
          <div id="loaderOverlay" style="
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.8);
            z-index: 1055; /* lebih dari modal bootstrap */
            display: flex;
            justify-content: center;
            align-items: center;
          ">
            <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        `);
    } else {
        $('#loaderOverlay').show();
    }
}

// Fungsi untuk sembunyikan loader
function hideLoader() {
    $('#loaderOverlay').hide();
}
</script>

<script>
function formatRiwayat(nis) {
    return `
        <div class="p-2 border rounded bg-light">
            <table class="table table-hover table-sm table-bordered m-0 p-0 w-100" id="riwayat-${nis}">
                <thead class="table-secondary">
                    <tr>
                        <th class="text-center" style="width:40px;">#</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-center">Kas Bank</th>
                        <th class="text-center">Catatan</th>
                        <th class="text-center">Admin</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    `;
}


function loadRiwayatPembayaran(nis) {
    $.ajax({
        url: `<?= base_url('keuangan/pembayaransiswa/getRiwayatPembayaranByNisInfo') ?>`,
        type: 'GET', // atau POST kalau mau sesuai route
        data: {
            nis: nis
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $(`#riwayat-${nis} tbody`).html(response.data);
            } else {
                $(`#riwayat-${nis} tbody`).html(
                    `<tr><td colspan="7" class="text-center text-danger">Gagal memuat data</td></tr>`
                );
            }
        },
        error: function() {
            $(`#riwayat-${nis} tbody`).html(
                `<tr><td colspan="7" class="text-center text-danger">Error koneksi</td></tr>`
            );
        }
    });
}



$(document).ready(function() {
    const table = $('#angkatanTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "<?= base_url('keuangan/pembayaransiswa/ajaxList') ?>",
            dataSrc: 'data'
        },
        columns: [{
                className: 'text-center',
                orderable: false,
                data: null,
                defaultContent: '<i class="fa fa-plus-circle text-info" style="cursor:pointer;"></i>'
            },
            {
                data: 'nis',
                className: 'text-center'
            },
            {
                data: 'nis_new',
                className: 'text-center'
            },
            {
                data: 'nama'
            },
            {
                data: 'angkatan',
                className: 'text-center'
            },
            {
                data: 'nama_sekolah',
                className: 'text-center'
            },

            {
                data: 'status',
                className: 'text-center',
                render: function(data) {
                    return `<span class="badge bg-${data === 'Aktif' ? 'success' : 'danger'}">${data}</span>`;
                }
            },
            {
                data: 'nis',
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function(nis) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info" onclick="detailSiswa('${nis}')">🔑 Buka Kas</button>
                        </div>
                    `;
                }
            }
        ]
    });

    $('#angkatanTable tbody').on('click', 'tr', function(e) {
        if ($(e.target).closest('button, a, input, select, textarea').length > 0) {
            return;
        }

        const tr = $(this);
        const row = table.row(tr);
        const icon = tr.find('td:first-child i');

        if (row.child.isShown()) {
            row.child.hide();
            icon.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-info');
            tr.removeClass('shown');
        } else {
            // Tutup baris lain yang terbuka
            table.rows('.shown').every(function() {
                this.child.hide();
                $(this.node()).removeClass('shown');
                $(this.node()).find('td:first-child i')
                    .removeClass('fa-minus-circle text-danger')
                    .addClass('fa-plus-circle text-info');
            });

            row.child(formatRiwayat(row.data().nis)).show();
            loadRiwayatPembayaran(row.data().nis);
            icon.removeClass('fa-plus-circle text-info').addClass('fa-minus-circle text-danger');
            tr.addClass('shown');
        }
    });

});
</script>