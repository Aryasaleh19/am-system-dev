<!-- modal_form.php -->
<div class="modal fade" id="groupModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="groupModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <!-- ukuran besar -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelModal">Tambah Nomor Rekening</h5>
            </div>
            <form action="<?= base_url('keuangan/rekening/simpan') ?>" id="formInput" method="post">

                <div class="modal-body">
                    <div class="mb-3">
                        <input type="hidden" class="form-control" name="id" id="id" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_rekening" class="form-label">Nomor Rekening</label>
                        <input type="text" class="form-control" name="no_rekening" id="no_rekening"
                            placeholder="Nomor Rekening" required>
                    </div>
                    <div class="mb-3">
                        <label for="bank" class="form-label">Nama Bank</label>
                        <input type="text" class="form-control" name="bank" id="bank" placeholder="Nama Bank" required>
                    </div>
                    <div class="mb-3">
                        <label for="saldo_awal" class="form-label">Saldo Awal</label>
                        <input type="number" class="form-control" name="saldo_awal" id="saldo_awal"
                            placeholder="Saldo Awal" required>
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
        var url = id ? "<?= base_url('keuangan/rekeningbank/update') ?>" :
            "<?= base_url('keuangan/rekeningbank/save') ?>";
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
            error: function(xhr) {
                // Ambil code dan message dari response JSON jika ada
                let response = xhr.responseJSON;
                let message = response?.message || 'Terjadi kesalahan pada server';
                let code = response?.code || xhr.status;

                Swal.fire({
                    icon: 'error',
                    title: 'Error ' + code,
                    text: message
                });

                // Untuk debugging di console
                console.error('AJAX Error:', response || xhr);
            }
        });
    });

});
</script>