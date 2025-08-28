<!-- Modal -->
<div class="modal fade" id="modalFormProgram" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">🗓️ <?= (!empty($idProgram)) ? 'Ubah':'Tambah' ?>
                    Program </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form class="ajaxForm" method="post"
                data-url="<?= (!empty($idProgram)) ? base_url('perencanaan/program/update') : base_url('perencanaan/program/save') ?>">

                <div class="modal-body">
                    <input type="hidden" name="ID_PROGRAM" id="ID_PROGRAM" value="<?= $idProgram ?>">

                    <div class="form-group">
                        <label class="form-label small">Nama Program</label>
                        <input type="text" name="NAMA_PROGRAM" id="NAMA_PROGRAM" class="form-control"
                            placeholder="Masukkan nama program" value="<?= $program ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label small">Tahun</label>
                        <input type="number" name="TAHUN" value="<?= $tahun ?>" id="TAHUN" class="form-control"
                            placeholder="2025" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label small">Anggaran</label>
                        <input type="number" step="0.01" value="<?= $anggaran ?>" name="ANGGARAN" id="ANGGARAN"
                            class="form-control" placeholder="0" required>
                    </div>
                    <div class="form-group">
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
                // gunakan res.status secara truthy, bukan === true
                if (res.status) {
                    // tutup modal dulu sebelum Swal
                    const modalEl = form.closest('.modal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // reload DataTable
                        if ($.fn.DataTable.isDataTable('#programTable')) {
                            $('#programTable').DataTable().ajax.reload(null, false);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Terjadi kesalahan'
                    });
                }
            });



    }
});
</script>