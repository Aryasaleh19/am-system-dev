<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6">
                            <small>Filter Data: </small>
                            <div class="input-group input-group-sm">
                                <select id="filterFrom" class="w-10 form-select form-select-sm text-center"
                                    style="width: 20%;" aria-label="Tahun angkatan dari">
                                    <option value="">Dari Tahun</option>
                                </select>
                                <select id="filterTo" class="w-10 form-select form-select-sm text-center"
                                    style="width: 20%;" aria-label="Sampai dengan">
                                    <option value="">Sampai Tahun</option>
                                </select>
                                <select id="filterSekolah" class="w-10 form-select form-select-sm text-center"
                                    style="width: 40%;" aria-label="Pilih Pendidikan">
                                    <option value="">[ Semua Pendidikan ]</option>
                                </select>
                                <button class="btn btn-outline-info w-20" id="filterBtn" type="button"><i
                                        class="fa fa-search" aria-hidden="true"></i> Cari</button>
                            </div>
                        </div>

                        <div class="col-6">
                            <button type="button" class="btn float-end btn-outline-success pull-right mb-3"
                                id="addBtn"><i class="fa fa-plus-circle" aria-hidden="true"></i> 👨 Formulir Siswa
                                Baru</button>
                        </div>
                    </div>
                    <table id="angkatanTable" class="table table-sm table-hover table-bordered table-striped small">
                        <thead>
                            <tr>
                                <th class="text-center">NO</th>
                                <th class="text-center">NO. INDUK<br>YAYASAN</th>
                                <th class="text-center">NO. INDUK<br>SISWA</th>
                                <th class="text-center">NAMA SISWA</th>
                                <th class="text-center">ANGKATAN</th>
                                <th class="text-center">PENDIDIKAN<br>SAAT INI</th>
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
<script src="<?= base_url('js/referensi.js') ?>"></script>

<script>
$('#filterBtn').on('click', function() {
    const from = $('#filterFrom').val();
    const to = $('#filterTo').val();
    const sekolah = $('#filterSekolah').val();
    $('#angkatanTable').DataTable().ajax.url(
        `<?= base_url('kesiswaan/siswa/ajaxList') ?>?from=${from}&to=${to}&sekolah=${sekolah}`).load();
});

$('#addBtn').on('click', function() {
    $.ajax({
        type: "GET",
        url: "<?= base_url('kesiswaan/siswa/formsiswabaru') ?>",
        dataType: "html",
        success: function(response) {
            $('.viewmodal').html(response);
            const modalEl = document.getElementById('modalformSiswaBaru');
            const modal = new bootstrap.Modal(modalEl);
            $('#labelModal').html(
                '<i class="fa fa-plus-circle" aria-hidden="true"></i> 👨 Formulir Siswa Baru'
            );
            modal.show();
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            alert('Gagal memuat form');
        }
    });
});

function hapus(nis) {
    Swal.fire({
        title: 'Yakin hapus?',
        text: "Data tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "<?= base_url('kesiswaan/siswa/delete/') ?>" + nis,
                success: function(response) {
                    Swal.fire('Terhapus!', response.message, 'success');
                    $('#angkatanTable').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    let message = xhr.responseText;

                    // Kalau responsenya JSON, coba parse dulu untuk ambil pesan spesifik
                    try {
                        const json = JSON.parse(xhr.responseText);
                        // Misal server kirim { message: "Error detail" }
                        if (json.message) {
                            message = json.message;
                        }
                    } catch (e) {
                        // bukan JSON, tetap gunakan responseText
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: `Response Code: ${xhr.status}`,
                        html: `Message: <span class="text-danger small"> ${message} </span>`,
                    });
                }
            });
        }
    });
}

function edit(nis) {
    // Tampilkan loader (buat dulu elemen ini di HTML atau dinamis)
    showLoader();

    $.ajax({
        type: "GET",
        url: "<?= base_url('kesiswaan/siswa/get/') ?>" + encodeURIComponent(nis),
        dataType: "json",
        success: function(data) {
            $('#addBtn').trigger('click'); // Buka modal form

            setTimeout(() => {
                let form = $('#formSiswaBaru');

                // Isi input biasa
                form.find('input[name=NIS]').val(data.NIS).prop('readonly', true);
                form.find('input[name=NAMA]').val(data.NAMA);
                form.find('input[name=TEMPAT_LAHIR]').val(data.TEMPAT_LAHIR);
                form.find('input[name=TANGGAL_LAHIR]').val(data.TANGGAL_LAHIR);
                form.find('input[name=KONTAK_ORANG_TUA]').val(data.KONTAK_ORANG_TUA);
                form.find('input[name=NAMA_AYAH]').val(data.NAMA_AYAH);
                form.find('input[name=NAMA_IBU]').val(data.NAMA_IBU);
                form.find('textarea[name=ALAMAT]').val(data.ALAMAT);

                // Select (trigger change untuk select2)
                form.find('select[name=JENIS_KELAMIN]').val(data.JENIS_KELAMIN).trigger(
                    'change');
                form.find('select[name=AGAMA_ID]').val(data.AGAMA_ID).trigger('change');
                form.find('select[name=STATUS]').val(data.STATUS).trigger('change');

                // Tunggu angkatan load dulu baru set value
                angkatanLoadPromise.done(function() {
                    $('#angkatan_id').val(data.ANGKATAN_ID).trigger('change');
                });

                // Set wilayah bertingkat
                $('#PROVINSI').val(data.PROV_ID).trigger('change');

                setTimeout(() => {
                    $('#KABUPATEN').val(data.KAB_ID).trigger('change');

                    setTimeout(() => {
                        $('#KECAMATAN').val(data.KEC_ID).trigger('change');

                        setTimeout(() => {
                            $('#KELURAHAN').val(data.KEL_ID).trigger(
                                'change');

                            // Semua selesai, sembunyikan loader
                            hideLoader();

                        }, 700);
                    }, 700);
                }, 700);

                // Setup submit update
                form.off('submit').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: "<?= base_url('kesiswaan/siswa/update') ?>",
                        data: $(this).serialize(),
                        dataType: "json",
                        success: function(response) {
                            $('#modalformSiswaBaru').modal('hide');
                            $('#angkatanTable').DataTable().ajax.reload();
                            Swal.fire('Sukses', response.message,
                                'success');
                        },
                        error: function(xhr) {
                            let message = xhr.responseText;

                            // Kalau responsenya JSON, coba parse dulu untuk ambil pesan spesifik
                            try {
                                const json = JSON.parse(xhr.responseText);
                                // Misal server kirim { message: "Error detail" }
                                if (json.message) {
                                    message = json.message;
                                }
                            } catch (e) {
                                // bukan JSON, tetap gunakan responseText
                            }

                            Swal.fire({
                                icon: 'warning',
                                title: `Response Code: ${xhr.status}`,
                                html: `Message: <span class="text-danger small"> ${message} </span>`,
                            });
                        }
                    });
                });

            }, 700);
        },
        error: function(xhr) {
            hideLoader();
            let errorMsg = "Terjadi kesalahan tidak dikenal.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                errorMsg = xhr.responseText;
            }
            console.error("Gagal mengambil data siswa:", errorMsg);
            Swal.fire('Gagal', errorMsg, 'error');
        }
    });
}

function detailSiswa(nis) {
    showLoader();
    $.ajax({
        type: "GET",
        url: "<?= base_url('kesiswaan/siswa/modaldetailsiswa') ?>",
        dataType: "html",
        success: function(modalHtml) {
            // Masukkan modal ke DOM
            $('.viewmodal').html(modalHtml);

            // 2. Setelah modal ada di DOM, ambil data siswa
            $.ajax({
                type: "GET",
                url: "<?= base_url('kesiswaan/siswa/getDetail/') ?>" + encodeURIComponent(nis),
                dataType: "json",
                success: function(data) {
                    hideLoader();
                    console.log(data.NIS);
                    $('#modalformSiswaBaru .siswa-nis').val(data.NIS);

                    $('#labelModal').html(
                        '<i class="fa fa-info" aria-hidden="true"></i> 👨 Detail Informasi Siswa <span class="badge bg-label-info">[' +
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
        error: function(xhr) {
            let message = xhr.responseText;

            // Kalau responsenya JSON, coba parse dulu untuk ambil pesan spesifik
            try {
                const json = JSON.parse(xhr.responseText);
                // Misal server kirim { message: "Error detail" }
                if (json.message) {
                    message = json.message;
                }
            } catch (e) {
                // bukan JSON, tetap gunakan responseText
            }

            Swal.fire({
                icon: 'warning',
                title: `Response Code: ${xhr.status}`,
                html: `Message: <span class="text-danger small"> ${message} </span>`,
            });
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
$(document).ready(function() {
    $('#angkatanTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "<?= base_url('kesiswaan/siswa/ajaxList') ?>",
            dataSrc: 'data'
        },
        columns: [{
                data: 'no',
                className: 'text-center'
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
                render: function(data, type, row) {
                    return `<span class="badge bg-${data === 'Aktif' ? 'success' : 'danger'}">${data}</span>`;
                }
            },
            {
                data: 'aksi',
                className: 'text-center',
                orderable: false,
                searchable: false
            }
        ]
    });
});
</script>

<?= $this->endSection() ?>