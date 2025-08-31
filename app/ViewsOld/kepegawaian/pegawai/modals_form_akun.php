<!-- Modal -->
<div class="modal fade" id="pegawaiAkun" data-bs-backdrop="static" tabindex="-1" aria-labelledby="pegawaiAkunLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formAkun">
            <div class="modal-content">
                <div class="modal-header bg-danger ">
                    <h5 class="modal-title text-white">🔒 Form Akun Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body row">

                    <input type="hidden" name="PEGAWAI_ID" required>

                    <div class="col-md-6 mb-3">
                        <label for="NIK" class="form-label">NIK</label>
                        <input type="text" class="form-control bg-white form-control-sm" name="NIK" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="NAMA" class="form-label">Nama</label>
                        <input type="text" class="form-control bg-white form-control-sm" name="NAMA" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="USERNAME" class="form-label">Username</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Masukkan username"
                            name="USERNAME" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="PASSWORD" class="form-label">Password</label>
                        <input type="password" class="form-control form-control-sm" placeholder="Masukkan password"
                            name="PASSWORD">
                        <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <select class="form-control select2" name="ACTIVE" id="ACTIVE" required>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-success">[ <i class="fa fa-save"
                            aria-hidden="true"></i> Simpan ]</button>
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">[ <i
                            class="fa fa-window-close" aria-hidden="true"></i> Batal ]</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    disableAutocomplete();
    const usernameInput = $('[name="USERNAME"]');
    if (usernameInput.val() !== '') {
        usernameInput.prop('readonly', true);
    }


    $(document).off('submit', '#formAkun');
    $(document).on('submit', '#formAkun', function(e) {
        e.preventDefault();

        // Bersihkan error lama
        $('#formAkun .is-invalid').removeClass('is-invalid');
        $('#formAkun .invalid-feedback').remove();

        const formData = $(this).serialize();
        const isUpdate = $("[name='PEGAWAI_ID']").val() !== "";

        const url = "<?= site_url('kepegawaian/pegawai/simpan_akun') ?>";


        $.post(url, formData, function(response) {
            if (response.status === 'saved') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                $('#pegawaiAkun').modal('hide');
                tabel.ajax.reload(null, false); // reload datatable tanpa reset page
            } else if (response.status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: `<pre>${JSON.stringify(response.errors, null, 2)}</pre>`,
                    confirmButtonText: 'Tutup'
                });

                // Tampilkan error di form
                $.each(response.errors, function(key, val) {
                    const field = $(`[name="${key}"]`);
                    field.addClass('is-invalid');
                    field.after(`<div class="invalid-feedback">${val}</div>`);
                });
            }
        });
    });

});
</script>