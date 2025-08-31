<?php if (!empty($siswa)) : ?>
<div class="row">
    <div class="col-4">
        <div class="card shadow-none bg-transparent border border-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Form Pendaftaran Pendidikan</h5>
                <form id="formpendaftaranSekolahSiswa">
                    <div class="row g-1">
                        <div class="form-group">
                            <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white" value="<?= $siswa['NIS'] ?>" id="nis"
                                name="NIS" placeholder="Nomor Induk Siswa" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white" value="<?= $siswa['NAMA'] ?>" id="nama"
                                name="NAMA" placeholder="Nama Lengkap Siswa" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Tgl. Pendaftaran</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="id_sekolah">Sekolah / Pendidikan</label>
                            <select id="id_sekolah" class="form-control custom-select" name="id_sekolah" required>
                                <option value="">[ Pilih ]</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="angkatan_new">Tahun Angkatan</label>
                            <input type="number" name="angkatan_new" id="angkatan_new" class="form-control" required>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-block"
                            id="btnSimpanPendaftaranPendidikan">[ <i class="fa fa-save" aria-hidden="true"></i>
                            Simpan ]</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-8">
        <div class="card shadow-none bg-transparent border border-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Riwayat Sekolah</h5>
                <table class="table table-striped table-responsive" id="riwayatSekolahTable">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th class="text-center">ANGKATAN</th>
                            <th class="text-center">NAMA SEKOLAH</th>
                            <th class="text-center">NIS BARU</th>
                            <th class="text-center">TMT</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<?php endif; ?>
<script src="<?= base_url('js/referensi.js') ?>"></script>

<script>
function reloadRiwayatSekolahTable(callback) {
    const nis = $('#nis').val();

    $.ajax({
        url: "<?= base_url('kesiswaan/siswa/riwayatSekolahTable') ?>",
        type: 'GET',
        data: {
            nis: nis
        },
        success: function(html) {
            $('#riwayatSekolahTable tbody').html(html);

            // Bind ulang event setelah isi tabel diupdate
            bindStatusSwitchEvent();

            if (typeof callback === 'function') callback();
        },
        error: function() {
            Swal.fire('Error', 'Gagal memuat ulang tabel riwayat sekolah', 'error');
            if (typeof callback === 'function') callback();
        }
    });
}

function bindStatusSwitchEvent() {
    // Unbind dulu supaya event tidak bertumpuk
    $(document).off('change', '.status-switch');

    $(document).on('change', '.status-switch', function() {
        let id = $(this).data('id');
        let status = $(this).is(':checked') ? 1 : 0;

        // Update label status langsung
        let label = $('label[for="switch_' + id + '"]');
        label.text(status === 1 ? 'Aktif' : 'Tidak Aktif');

        $('#loader').show();

        $.ajax({
            url: "<?= base_url('kesiswaan/siswa/updatestatussekolah') ?>",
            type: 'POST',
            data: {
                id: id,
                status: status
            },
            dataType: 'json',
            success: function(res) {
                swal.fire({
                    icon: res.status ? 'success' : 'error',
                    title: res.status ? 'Berhasil' : 'Gagal',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                reloadRiwayatSekolahTable(function() {
                    $('#loader').hide();
                });
                $('#angkatanTable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                $('#loader').hide();
                let message = xhr.responseText;
                try {
                    const json = JSON.parse(xhr.responseText);
                    if (json.message) message = json.message;
                } catch (e) {}
                Swal.fire({
                    icon: 'warning',
                    title: `Response Code: ${xhr.status}`,
                    html: `Message: <span class="text-danger small"> ${message} </span>`,
                });
            }
        });
    });
}

function hapusRiwayat(id) {
    Swal.fire({
        title: 'Hapus Riwayat Sekolah?',
        text: "Data yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#loader').show();

            $.ajax({
                url: "<?= base_url('kesiswaan/siswa/hapusRiwayatSekolah') ?>",
                type: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    $('#loader').hide();
                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        reloadRiwayatSekolahTable();
                        $('#angkatanTable').DataTable().ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Hapus',
                            html: `Message: ${res.message}<br>Code: ${res.code ?? '-'}`,
                        });
                    }
                },
                error: function(xhr) {
                    $('#loader').hide();
                    let message = xhr.responseText;
                    let code = xhr.status;
                    try {
                        const json = JSON.parse(xhr.responseText);
                        if (json.message) message = json.message;
                        if (json.code) code = json.code;
                    } catch (e) {}
                    Swal.fire({
                        icon: 'error',
                        title: `Error Code: ${code}`,
                        html: `<span class="text-danger">${message}</span>`
                    });
                }
            });
        }
    });
}

function resetFormButKeepOptions() {
    const form = $('#formpendaftaranSekolahSiswa')[0];
    form.reset(); // reset input biasa seperti tanggal, text, dll

    // Reset select2 tanpa menghapus opsi
    $('#id_sekolah').val('').trigger('change'); // kosongkan pilihan, tapi opsi tetap ada
    $('#angkatan_id').val('').trigger('change'); // kosongkan pilihan, tapi opsi tetap ada
}

$(document).ready(function() {
    reloadRiwayatSekolahTable();

    $('#btnSimpanPendaftaranPendidikan').on('click', function() {

        const form = $('#formpendaftaranSekolahSiswa')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const data = {
            NIS: $('#nis').val(),
            SEKOLAH_ID: $('#id_sekolah').val(),
            ANGKATAN_ID: $('#angkatan_new').val(),
            TANGGAL: $('#tanggal').val()
        };

        $('#loader').show();

        $.ajax({
            url: "<?= base_url('kesiswaan/siswa/savePendaftaranSekolah') ?>",
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(res) {
                $('#loader').hide();
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data pendaftaran berhasil disimpan',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    reloadRiwayatSekolahTable();
                    resetFormButKeepOptions();
                    $('#angkatanTable').DataTable().ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Simpan',
                        html: `Message: ${res.message}<br>Code: ${res.code ?? '-'}`,
                    });
                }
            },
            error: function(xhr) {
                $('#loader').hide();
                let message = xhr.responseText;
                try {
                    const json = JSON.parse(xhr.responseText);
                    if (json.message) message = json.message;
                } catch (e) {}
                Swal.fire({
                    icon: 'warning',
                    title: `Response Code: ${xhr.status}`,
                    html: `Message: <span class="text-danger small"> ${message} </span>`,
                });
            }
        });
    });
});
</script>