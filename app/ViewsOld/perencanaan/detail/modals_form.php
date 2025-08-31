<!-- Modal -->
<div class="modal fade" id="modalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">📂 <?= (!empty($id_sub)) ? 'Ubah':'Tambah' ?> Sub Kegiatan |
                    <?= $parent_nama ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="alert alert-warning text-black">
                <strong>Perhatian!</strong> Anda saat ini berada pada Kegiatan : <strong><?= $parent_nama ?></strong>
                untuk menambah atau mengubah
                Kegiatan silahkan isi form dibawah ini.
            </div>

            <form class="ajaxForm" method="post" data-url="<?= base_url('perencanaan/detail/save') ?>">
                <input type="hidden" name="id" value="<?= $id_sub ?>">
                <input type="hidden" name="ID_KEGIATAN" value="<?= $id_kegiatan ?>">

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Sub Kegiatan</label>
                        <input type="text" name="NAMA_SUB_KEGIATAN" placeholder="Nama Sub Kegiatan" class="form-control"
                            value="<?= is_array($subkegiatan) ? '' : $subkegiatan ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Anggaran</label>
                        <input type="number" name="ANGGARAN" value="<?= $anggaran ?>" placeholder="0"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="STATUS" class="form-select" required>
                            <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa fa-save"></i>
                        Simpan</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal"><i
                            class="fa fa-window-close"></i> Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    disableAutocomplete();
});
document.addEventListener('submit', function(e) {
    // Hanya tangani form dengan class ajaxForm
    if (e.target && e.target.classList.contains('ajaxForm')) {
        e.preventDefault();
        const form = e.target;
        const url = form.dataset.url;
        const formData = new FormData(form);

        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    showToast('Tersimpan', res.message, 'success', 1500);
                    $('#programTable').DataTable().ajax.reload(null, false);
                    const modalEl = form.closest('.modal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();


                } else {
                    showToast('Gagal', res.message, 'warning', 1500);
                }
            })
            .catch(err => {
                Swal.fire('Terjadi Kesalahan', err.message || err, 'error');
            });
    }
});
</script>