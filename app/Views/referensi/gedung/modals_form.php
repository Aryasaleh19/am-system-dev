<!-- modal_form.php -->
<div class="modal fade" id="gedungModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="gedungModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <!-- ukuran besar -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gedungModalLabel">Tambah Gedung</h5>
            </div>
            <form action="<?= base_url('referensi/gedung/simpan') ?>" id="gedungForm" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="gedung" class="form-label">Nama Gedung</label><sup class="text-danger">*</sup>
                        <input type="text" class="form-control" name="gedung" id="gedung"
                            placeholder="Masukkan Nama Gedung" required>
                    </div>
                    <div class="mb-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" class="form-control" name="latitude" id="latitude"
                            placeholder="Titik Maps Absensi (Latitude)">
                    </div>
                    <div class="mb-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" class="form-control" name="longitude" id="longitude"
                            placeholder="Titik Maps Absensi (Longitude)">
                    </div>
                    <div class="mb-3">
                        <label for="maxjarak" class="form-label">Max. Jarak (Meter)</label>
                        <input type="text" class="form-control" name="maxjarak" id="maxjarak"
                            placeholder="Maksimal jarak absensi">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label><sup class="text-danger">*</sup>
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

    $('#gedungForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var id = $('#id').val();
        var url = id ? "<?= base_url('referensi/gedung/update') ?>" :
            "<?= base_url('referensi/gedung/simpan') ?>";
        var data = form.serialize();

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'saved' || response.status === 'updated') {
                    table.ajax.reload();
                    $('#gedungModal').modal('hide');
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