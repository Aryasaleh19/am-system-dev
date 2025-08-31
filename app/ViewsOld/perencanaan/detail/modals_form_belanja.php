<?php
$isEdit = !empty($belanja['ID_BELANJA']);
?>

<div class="modal fade" id="modalBelanja" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalBelanjaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBelanjaLabel">📌 <?= $isEdit ? 'Edit' : 'Tambah' ?> Belanja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form class="ajaxForm" method="post"
                data-url="<?= $isEdit ? base_url('perencanaan/detail/updateBelanja') : base_url('perencanaan/detail/saveBelanja') ?>">

                <input type="hidden" name="ID_BELANJA" value="<?= $belanja['ID_BELANJA'] ?? '' ?>">
                <input type="hidden" name="ID_SUB" value="<?= $id_sub ?? '' ?>">
                <input type="hidden" name="TANGGAL" class="form-control"
                    value="<?= $belanja['TANGGAL'] ?? date('Y-m-d') ?>" required>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Uraian Belanja</label>
                        <textarea name="URAIAN_BELANJA" id="URAIAN_BELANJA" rows="3" class="form-control"
                            placeholder="Uraian Belanja" required><?= $belanja['URAIAN_BELANJA'] ?? '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Anggaran</label>
                        <input type="number" name="ANGGARAN" class="form-control" placeholder="0"
                            value="<?= $belanja['ANGGARAN'] ?? '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="STATUS" id="STATUS" class="form-select" required>
                            <option value="1"
                                <?= (isset($belanja['STATUS']) && $belanja['STATUS'] == 1) ? 'selected' : '' ?>>
                                Aktif
                            </option>
                            <option value="0"
                                <?= (isset($belanja['STATUS']) && $belanja['STATUS'] == 0) ? 'selected' : '' ?>>
                                Tidak
                                Aktif</option>
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
    if (e.target && e.target.classList.contains('ajaxFormBelanja')) {
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
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        const modalEl = form.closest('.modal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        form.reset();

                        // reload DataTable
                        if ($.fn.DataTable.isDataTable('#programTable')) {
                            $('#programTable').DataTable().ajax.reload(null, false);
                        }
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error', err.message || err, 'error'));
    }
});
</script>