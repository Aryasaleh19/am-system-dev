<!-- modal_form.php -->
<div class="modal fade" id="groupModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="groupModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <!-- ukuran besar -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelModal">Tambah Jenis Penerimaan</h5>
            </div>
            <form action="<?= base_url('referensi/jenispenerimaan/simpan') ?>" id="formInput" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="row g-1">
                        <div class="col-lg-12">
                            <label for="jenis" class="form-label">Jenis Penerimaan</label>
                            <input type="text" class="form-control" name="jenis" id="jenis"
                                placeholder="Nama Jenis Penerimaan" required>
                        </div>
                        <div class="col-lg-6">
                            <label for="sekolah_id" class="form-label">Lembaga</label>
                            <select class="form-control form-select" name="sekolah_id" id="sekolah_id" required>
                                <option value="" selected>[ Pilih ]</option>
                                <?php foreach($sekolah as $s) : ?>
                                <option value="<?= $s['ID'] ?>"><?= $s['NAMA_SEKOLAH'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label for="kategori" class="form-label">Kategori/Sumber</label>
                            <select class="form-control form-select" name="kategori" id="kategori" required>
                                <option value="" selected>[ Pilih ]</option>
                                <option value="Formal">Formal</option>
                                <option value="Non Formal">Non Formal</option>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" name="jumlah" id="jumlah"
                                placeholder="Jumlah (Rp)" required>
                        </div>
                        <div class="col-lg-6">
                            <label for="tenor" class="form-label">Tenor</label>
                            <input type="number" class="form-control" name="tenor" id="tenor" placeholder="Tenor (x)"
                                required>
                        </div>
                        <div class="col-lg-6">
                            <label for="satuan" class="form-label">Satuan</label>
                            <select class="form-control form-select" name="satuan" id="satuan" required>
                                <option value="" selected>[ Pilih ]</option>
                                <option value="Tahun">Tahun</option>
                                <option value="Bulan">Bulan</option>
                                <option value="Periodik">Periodik</option>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control form-select" name="status" id="status" required>
                                <option value="" selected>[ Pilih ]</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

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
        var url = id ? "<?= base_url('referensi/jenispenerimaan/update') ?>" :
            "<?= base_url('referensi/jenispenerimaan/simpan') ?>";
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