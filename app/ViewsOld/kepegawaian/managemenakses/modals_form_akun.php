<div class="modal fade" id="formAkun" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="groupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <form id="formsubmitAkun">
                <div class="modal-header">
                    <h5 class="modal-title">🔒 Managemen Akses | <input type="text" style="font-size:17px"
                            class="text-center bg-yellow text-black" id="NAMA" name="NAMA" disabled></h5>
                    <div class="btn-group" role="group" aria-label="Button group">
                        <button type="submit" class="btn btn-sm btn-outline-success">
                            [ <i class="fa fa-save" aria-hidden="true"></i> Simpan ]
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">
                            [ <i class="fa fa-window-close" aria-hidden="true"></i> Batal ]
                        </button>
                    </div>
                </div>

                <div class="modal-body row overflow-auto" style="max-height: calc(100vh - 160px);">
                    <input type="hidden" name="PEGAWAI_ID" required>
                    <input type="hidden" name="USERNAME" required>
                    <input type="hidden" name="NIK" required>
                    <div class="col-md-3 mb-3">

                    </div>
                    <div class="row">
                        <!-- Jabatan -->
                        <div class="col-lg-4">
                            <div class="card" style="background-color: #FCF8DD">
                                <div class="card-header h5">Level Akses</div>
                                <div class="card-body">
                                    <?php foreach ($jabatan as $j): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="<?= $j['ID'] ?>"
                                            id="jabatan<?= $j['ID'] ?>" name="JABATAN_<?= $j['ID'] ?>"
                                            <?= in_array($j['ID'], $aksesTerpilih['JABATAN']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="jabatan<?= $j['ID'] ?>">
                                            <?= esc($j['JABATAN']) ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>


                                </div>
                            </div>
                        </div>

                        <!-- Modul by Group -->
                        <div class="col-lg-4">
                            <div class="card" style="background-color: #FCF8DD">
                                <div class="card-header h5">Modul Akses</div>
                                <div class="card-body">
                                    <?php foreach ($modulByGroup as $groupId => $groupData): ?>
                                    <div class="mb-2">
                                        <strong><?= esc($groupData['GROUP_MODUL']) ?></strong>
                                        <div style="padding-left: 28px; margin-top: 5px;">
                                            <?php if (!empty($groupData['MODUL'])): ?>
                                            <?php foreach ($groupData['MODUL'] as $modul): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="modul<?= $modul['ID'] ?>" name="MODUL_<?= $modul['ID'] ?>"
                                                    value="<?= $modul['ID'] ?>"
                                                    <?= in_array($modul['ID'], $aksesTerpilih['MODUL']) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="modul<?= $modul['ID'] ?>">
                                                    <?= esc($modul['MODUL']) ?>
                                                </label>
                                            </div>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <em class="text-muted">Tidak ada modul</em>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        </div>

                        <!-- Ruangan by Gedung -->
                        <div class="col-lg-4">
                            <div class="card" style="background-color: #FCF8DD">
                                <div class="card-header h5">Ruang Akses</div>
                                <div class="card-body">
                                    <?php foreach ($ruanganByGedung as $gedungId => $data): ?>
                                    <div class="mb-2">
                                        <strong><?= esc($data['GEDUNG']) ?></strong>
                                        <div style="padding-left: 28px; margin-top: 5px;">
                                            <?php if (!empty($data['RUANGAN'])): ?>
                                            <?php foreach ($data['RUANGAN'] as $ruangan): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="ruangan<?= $ruangan['ID'] ?>"
                                                    name="RUANGAN_<?= $ruangan['ID'] ?>" value="<?= $ruangan['ID'] ?>"
                                                    <?= in_array($ruangan['ID'], $aksesTerpilih['RUANGAN']) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="ruangan<?= $ruangan['ID'] ?>">
                                                    <?= esc($ruangan['RUANGAN']) ?>
                                                </label>
                                            </div>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <em class="text-muted">Tidak ada ruangan</em>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    $(document).on('submit', '#formsubmitAkun', function(e) {
        e.preventDefault();

        const $form = $(this);
        const pegawaiId = $form.find('[name="PEGAWAI_ID"]').val();


        if (!pegawaiId) {
            Swal.fire('Warning!',
                'Pegawai tersebut, belum memiliki Username dan Password. Silahkan buatkan akun pada menu Pegawai.',
                'warning');
            return;
        }

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            type: 'POST',
            url: '<?= site_url('kepegawaian/managemenakses/save_akses') ?>',
            data: $form.serialize(),
            dataType: 'json',
            success: function(res) {
                Swal.close();
                if (res.status) {
                    Swal.fire('Berhasil', res.message, 'success').then(() => {
                        $('#pegawaiTable').DataTable().ajax.reload(null,
                            false); // reload tanpa reset paging
                    });
                } else {
                    Swal.fire('Gagal', res.message ||
                        'Terjadi kesalahan saat menyimpan data.',
                        'error');
                }
            },
            error: function(xhr) {
                Swal.close();
                let msg = 'Terjadi kesalahan saat menghubungi server.';
                // coba parsing error JSON jika ada
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.message) msg = res.message;
                } catch (e) {}
                Swal.fire('Error', msg, 'error');
                console.error('AJAX error:', xhr.responseText);
            }
        });
    });
});
</script>