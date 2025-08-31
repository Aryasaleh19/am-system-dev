<!-- Modal -->
<div class="modal fade" id="modalKegiatan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">🗓️ <?= (!empty($id_kegiatan)) ? 'Ubah':'Tambah' ?>
                    Kegiatan </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="alert alert-warning text-black">
                <strong>Perhatian!</strong> Anda saat ini berada pada Program : <strong><?= $program ?></strong>
                untuk menambah Sub
                Kegiatan silahkan isi form dibawah ini.
            </div>

            <form class="ajaxForm" method="post"
                data-url="<?= (!empty($id_kegiatan)) ? base_url('perencanaan/kegiatan/update') : base_url('perencanaan/kegiatan/save') ?>">

                <input type="hidden" name="ID_KEGIATAN" id="ID_KEGIATAN" value="<?= $id_kegiatan ?>">
                <input type="hidden" name="ID_PROGRAM" id="ID_PROGRAM" value="<?= $idProgram ?>">

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Kegiatan</label>
                        <input type="text" name="NAMA_KEGIATAN" id="NAMA_KEGIATAN" placeholder="Nama Kegiatan"
                            class="form-control" value="<?= is_array($kegiatan) ? '' : $kegiatan ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Tahun Anggaran</label>
                        <input type="number" name="TAHUN" value="<?= $tahun ?>" placeholder="2025" class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>Anggaran</label>
                        <input type="number" name="ANGGARAN" id="ANGGARAN" value="<?= $anggaran ?>"
                            placeholder="Masukkan Anggaran (Harus Angka)" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="STATUS" id="STATUS" class="form-select" required>
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