<!-- modal_form.php -->
<div class="modal fade" id="groupModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="groupModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-sm">
        <!-- ukuran besar -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelModal">Tambah Profesi</h5>
            </div>
            <form action="<?= base_url('referensi/profesi/simpan') ?>" id="formInput" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profesi" class="form-label">Profesi</label>
                        <input type="text" class="form-control" name="profesi" id="profesi" placeholder="Nama agama"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control form-select" name="status" id="status" required>
                            <option value="" selected>[ Pilih ]</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa fa-save"
                            aria-hidden="true"></i>
                        Simpan</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal"><i
                            class="fa fa-window-close" aria-hidden="true"></i> Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    disableAutocomplete();

    $('#formInput').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var id = $('#id').val();
        var url = id ? "<?= base_url('referensi/profesi/update') ?>" :
            "<?= base_url('referensi/profesi/simpan') ?>";
        var data = form.serialize();

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'saved' || response.status === 'updated') {
                    table.ajax.reload();
                    $('#groupModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || (response.status === 'saved' ?
                            'Data berhasil disimpan!' :
                            'Data berhasil diupdate!'),
                        timer: 2000,
                        showConfirmButton: false
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message ||
                            'Terjadi kesalahan saat menyimpan data',
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan pada server',
                });
            }
        });
    });
});
</script>