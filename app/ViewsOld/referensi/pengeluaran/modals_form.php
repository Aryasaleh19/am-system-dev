<!-- modal_form.php -->
<div class="modal fade" id="groupModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="groupModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!-- ukuran besar -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelModal">Tambah Jenis Pengeluaran</h5>
            </div>
            <form action="<?= base_url('referensi/jenispengeluaran/simpan') ?>" id="formInput" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode</label>
                        <input type="text" class="form-control" name="kode" id="kode"
                            placeholder="Kode Akun Pengeluaran" required>
                    </div>
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis Penerimaan</label>
                        <input type="text" class="form-control" name="jenis" id="jenis"
                            placeholder="Nama Jenis Pengeluaran" required>
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
        var url = id ? "<?= base_url('referensi/jenispengeluaran/update') ?>" :
            "<?= base_url('referensi/jenispengeluaran/simpan') ?>";
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