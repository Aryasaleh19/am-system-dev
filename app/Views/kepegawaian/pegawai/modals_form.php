<div class="modal fade" id="pegawaiModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="pegawaiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <!-- modal-xl untuk lebar ekstra -->
        <div class="modal-content">
            <form action="<?= base_url('kepegawaian/pegawai/simpan') ?>" id="formPegawai" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="pegawaiModalLabel">🧕 Tambah Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ID" id="ID">

                    <div class="container-fluid">
                        <div class="row g-3">
                            <!-- Kolom 1 -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="NIP" class="form-label">NIP</label>
                                    <input type="text" class="form-control" name="NIP" id="NIP"
                                        placeholder="Masukkan NIP" required>
                                </div>

                                <div class="mb-3">
                                    <label for="NIK" class="form-label">NIK</label>
                                    <input type="text" class="form-control" name="NIK" id="NIK"
                                        placeholder="Masukkan NIK" required>
                                </div>

                                <div class="mb-3">
                                    <label for="NAMA" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="NAMA" id="NAMA"
                                        placeholder="Masukkan Nama Lengkap" required>
                                </div>

                                <div class="mb-3">
                                    <label for="TEMPAT_LAHIR" class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" name="TEMPAT_LAHIR" id="TEMPAT_LAHIR"
                                        placeholder="Masukkan Tempat Lahir">
                                </div>
                            </div>

                            <!-- Kolom 2 -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="TANGGAL_LAHIR" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="TANGGAL_LAHIR" id="TANGGAL_LAHIR">
                                </div>

                                <div class="mb-3">
                                    <label for="JENIS_KELAMIN" class="form-label">Jenis Kelamin</label>
                                    <select class="form-control select2" name="JENIS_KELAMIN" id="JENIS_KELAMIN"
                                        required>
                                        <option value="">[ Pilih ]</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="AGAMA_ID" class="form-label">Agama</label>
                                    <select class="form-control select2" name="AGAMA_ID" id="AGAMA_ID" required>
                                        <option value="">[ Pilih ]</option>
                                        <?php foreach ($agamas as $agama): ?>
                                        <option value="<?= esc($agama['ID']) ?>"><?= esc($agama['AGAMA']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="PENDIDIKAN_ID" class="form-label">Pendidikan</label>
                                    <select class="form-control select2" name="PENDIDIKAN_ID" id="PENDIDIKAN_ID">
                                        <option value="">[ Pilih ]</option>
                                        <?php foreach ($pendidikans as $pendidikan): ?>
                                        <option value="<?= esc($pendidikan['ID']) ?>">
                                            <?= esc($pendidikan['PENDIDIKAN']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Kolom 3 -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="JABATAN_ID" class="form-label">Jabatan</label>
                                    <select class="form-control select2" name="JABATAN_ID" id="JABATAN_ID" required>
                                        <option value="">[ Pilih ]</option>
                                        <?php foreach ($jabatans as $jabatan): ?>
                                        <option value="<?= esc($jabatan['ID']) ?>"><?= esc($jabatan['JABATAN']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="PROFESI_ID" class="form-label">Profesi</label>
                                    <select class="form-control select2" name="PROFESI_ID" id="PROFESI_ID">
                                        <option value="">[ Pilih ]</option>
                                        <?php foreach ($profesis as $profesi): ?>
                                        <option value="<?= esc($profesi['ID']) ?>"><?= esc($profesi['PROFESI']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="TMT_SK" class="form-label">No. SK</label>
                                    <input type="text" class="form-control" name="TMT_SK" id="TMT_SK"
                                        placeholder="Masukkan Nomor SK">
                                </div>

                                <div class="mb-3">
                                    <label for="TM_SK" class="form-label">Tanggal SK</label>
                                    <input type="date" class="form-control" name="TM_SK" id="TM_SK">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label for="JENIS_PEGAWAI_ID" class="form-label">Jenis Pegawai</label>
                                        <select class="form-control select2" name="JENIS_PEGAWAI_ID"
                                            id="JENIS_PEGAWAI_ID" required>
                                            <option value="">[ Pilih ]</option>
                                            <?php foreach ($jenispegawais as $jenispegawai): ?>
                                            <option value="<?= esc($jenispegawai['ID']) ?>">
                                                <?= esc($jenispegawai['JENIS_PEGAWAI']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="AKTIF" class="form-label">Status Aktif</label>
                                        <select class="form-control select2" name="AKTIF" id="AKTIF" required>
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-success btn-sm"><i class="fa fa-save"></i>
                        Simpan</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    disableAutocomplete();
    // Inisialisasi Select2
    $('.select2').select2({
        dropdownParent: $('#pegawaiModal'),
        theme: 'bootstrap-5',
        placeholder: '[ Pilih ]',
        allowClear: true,
        width: '100%'
    });

    // Reset form saat modal ditutup
    $('#pegawaiModal').on('hidden.bs.modal', function() {
        $('#formPegawai')[0].reset();
        $('.select2').val(null).trigger('change');
        $('#ID').val('');
        $('#pegawaiModalLabel').text('Tambah Pegawai');
    });

    // Submit form via AJAX
    $('#formPegawai').on('submit', function(e) {
        e.preventDefault();

        const id = $('#ID').val();
        const url = id ? '<?= base_url('kepegawaian/pegawai/update') ?>' :
            '<?= base_url('kepegawaian/pegawai/simpan') ?>';

        $.ajax({
            type: 'POST',
            url: url,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'saved' || response.status === 'updated') {
                    $('#pegawaiModal').modal('hide');
                    $('#formPegawai')[0].reset();
                    $('.select2').val(null).trigger('change');
                    $('#ID').val('');
                    $('#pegawaiModalLabel').text('Tambah Pegawai');
                    table.ajax.reload(null, false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal | ' + response.code,
                        text: response.message
                    });
                }
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
});
</script>