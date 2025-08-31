<?php if (!empty($siswa)) : ?>
<form id="formSiswaBaru">
    <div class="row g-1">
        <!-- Baris 1 -->
        <div class="col-md-12">
            <button type="submit" form="formSiswaBaru" class="btn btn-outline-primary btn-sm float-end">[ <i
                    class="fa fa-save" aria-hidden="true"></i> Update ]</button>
            <h5>Profil Siswa</h5>
        </div>
        <!-- Baris 1 -->
        <div class="col-md-4">
            <label for="nis" class="form-label">No. Induk Yayasan <span class="text-danger">*</span></label>
            <input type="text" class="form-control" value="<?= $siswa['NIS'] ?>" id="nis" name="NIS"
                placeholder="Nomor Induk Siswa" readonly required>
        </div>
        <div class="col-md-4">
            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" class="form-control" value="<?= $siswa['NAMA'] ?>" id="nama" name="NAMA"
                placeholder="Nama Lengkap Siswa" required>
        </div>

        <!-- Baris 2 -->
        <div class="col-md-4">
            <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
            <input type="text" class="form-control" value="<?= $siswa['TEMPAT_LAHIR'] ?>" id="tempat_lahir"
                placeholder="Tempat Lahir Siswa" name="TEMPAT_LAHIR" required>
        </div>
        <div class="col-md-4">
            <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
            <input type="date" class="form-control" value="<?= $siswa['TANGGAL_LAHIR'] ?>" id="tanggal_lahir"
                placeholder="Tanggal Lahir Siswa" name="TANGGAL_LAHIR" required>
        </div>

        <!-- Baris 3 -->
        <div class="col-md-4">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
            <select class="form-select" id="jenis_kelamin" placeholder="Jenis Kelamin Siswa" name="JENIS_KELAMIN"
                required>
                <option value="">-- Pilih --</option>
                <option value="L" <?= $siswa['JENIS_KELAMIN'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= $siswa['JENIS_KELAMIN'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="agama_id" class="form-label">Agama <span class="text-danger">*</span></label>
            <select class="form-select" id="agama_id" placeholder="Agama Siswa" name="AGAMA_ID">
                <option value="">[ Pilih ]</option>
            </select>
        </div>
        <!-- Baris 5 -->
        <div class="col-md-12">
            <label for="ALAMAT" class="form-label">Alamat <span class="text-danger">*</span></label>
            <input type="text" value="<?= $siswa['ALAMAT'] ?>" class="form-control" id="ALAMAT"
                placeholder="Alamat Siswa" name="ALAMAT" required>
        </div>

        <!-- DATA WILAYAH -->
        <?= view('templates/wilayah') ?>

        <div class="col-md-4">
            <label for="kontak_ortu" class="form-label">Kontak Orang Tua</label>
            <input type="text" value="<?= $siswa['KONTAK_ORANG_TUA'] ?>" class="form-control" id="kontak_ortu"
                placeholder="Kontak Orang Tua Siswa" name="KONTAK_ORANG_TUA">
        </div>

        <!-- Baris 6 -->
        <div class="col-md-4">
            <label for="nama_ayah" class="form-label">Nama Ayah <span class="text-danger">*</span></label>
            <input type="text" value="<?= $siswa['NAMA_AYAH'] ?>" class="form-control" id="nama_ayah"
                placeholder="Nama Ayah Siswa" name="NAMA_AYAH" required>
        </div>
        <div class="col-md-4">
            <label for="nama_ibu" class="form-label">Nama Ibu <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_ibu" value="<?= $siswa['NAMA_IBU'] ?>"
                placeholder="Nama Ibu Siswa" name="NAMA_IBU" required>
        </div>

        <!-- Baris 7 -->
        <div class="col-md-6">
            <label for="angkatan_id" class="form-label">Angkatan <span class="text-danger">*</span></label>
            <select class="form-select" id="angkatan_id" placeholder="Angkatan Siswa" name="ANGKATAN_ID">
                <option value="">[ Pilih ]</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" placeholder="Status Siswa" name="STATUS">
                <option value="1" <?= $siswa['STATUS'] == 1 ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= $siswa['STATUS'] == 0 ? 'selected' : '' ?>>Tidak Aktif</option>
            </select>
        </div>
    </div>
</form>

<?php else : ?>
<div class="alert alert-warning">Data siswa tidak tersedia.</div>
<?php endif; ?>

<script src="<?= base_url('js/referensi.js') ?>"></script>
<script src="<?= base_url('js/wilayah.js') ?>"></script>

<script>
$(document).ready(function() {

    $('#formSiswaBaru').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "<?= base_url('kesiswaan/siswa/update') ?>",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                $('#modalformSiswaBaru').modal('hide');
                $('#angkatanTable').DataTable().ajax.reload();
                Swal.fire('Sukses', response.message, 'success');
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

    const siswa = <?= json_encode($siswa ?? []) ?>;

    // Inisialisasi Select2 langsung di sini
    $('#agama_id, #angkatan_id').select2({
        theme: 'bootstrap-5',
        placeholder: '[ Pilih ]',
        dropdownParent: $('#modalformSiswaBaru')
    });

    // Load Agama
    $.ajax({
        url: '/api/referensi/agama',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const $agama = $('#agama_id');
            $agama.empty().append('<option value="">[ Pilih ]</option>');
            data.forEach(item => {
                $agama.append(new Option(item.AGAMA, item.ID));
            });
            if (siswa.AGAMA_ID) {
                $agama.val(siswa.AGAMA_ID).trigger('change');
            }
        }
    });

    // Load Angkatan
    $.ajax({
        url: '/api/referensi/angkatan',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const $angkatan = $('#angkatan_id');
            $angkatan.empty().append('<option value="">[ Pilih ]</option>');
            data.forEach(item => {
                $angkatan.append(new Option(item.ANGKATAN, item.ID));
            });
            if (siswa.ANGKATAN_ID) {
                $angkatan.val(siswa.ANGKATAN_ID).trigger('change');
            }
        }
    });

    // Set wilayah bertingkat
    if (!$.isEmptyObject(siswa)) {
        setTimeout(() => {
            $('#PROVINSI').val(siswa.PROV_ID).trigger('change');
            setTimeout(() => {
                $('#KABUPATEN').val(siswa.KAB_ID).trigger('change');
                setTimeout(() => {
                    $('#KECAMATAN').val(siswa.KEC_ID).trigger('change');
                    setTimeout(() => {
                        $('#KELURAHAN').val(siswa.KEL_ID).trigger('change');
                    }, 700);
                }, 700);
            }, 700);
        }, 700);
    }
});
</script>