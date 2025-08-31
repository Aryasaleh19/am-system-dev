<div class="card">
    <div class="card-header">
        <h4 class="card-title">Pengaturan Absensi | <span class="text-primary"><?= $jabatan ?></span></h4>
    </div>
    <div class="card-body">
        <form id="formAbsensi">
            <input type="hidden" name="id_jabatan" value="<?= $id_jabatan ?>">

            <div class="table-responsive">
                <table class="table table-bordered table-sm small text-center align-middle">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th class="text-success">Jam Datang ↪</th>
                            <th class="text-danger">Jam Pulang ↩</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hariList = ["Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu"];
                        foreach ($hariList as $hari): 
                            $dataHari = isset($absensi[$hari]) ? $absensi[$hari] : null;

                            // tentukan class baris
                            $rowClass = ($dataHari && $dataHari['STATUS'] == 0 || empty($dataHari['STATUS'])) ? '#f5c6c6ff' : '';
                        ?>
                        <tr style=" background-color:<?= $rowClass ?>">
                            <td><?= $hari ?>
                                <input type="hidden" name="hari[]" value="<?= $hari ?>">
                            </td>
                            <td>
                                <input type="text" class="form-control text-center jam" name="datang[]"
                                    value="<?= $dataHari ? $dataHari['DATANG'] : '' ?>" placeholder="HH:MM:SS">
                            </td>
                            <td>
                                <input type="text" class="form-control text-center jam" name="pulang[]"
                                    value="<?= $dataHari ? $dataHari['PULANG'] : '' ?>" placeholder="HH:MM:SS">
                            </td>
                            <td>
                                <select name="status[]" class="form-control text-center status-select">
                                    <option value="0" <?= $dataHari && $dataHari['STATUS']==0 ? 'selected' : '' ?>>Non
                                        Aktif</option>
                                    <option value="1" <?= $dataHari && $dataHari['STATUS']==1 ? 'selected' : '' ?>>Aktif
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary text-white w-40 mt-3 float-end">[ 💾 Simpan ]</button>
        </form>
    </div>
</div>


<script>
$(document).ready(function() {
    // Inputmask untuk jam
    $(".jam").inputmask("99:99:99", {
        placeholder: "HH:MM:SS",
        insertMode: false,
        showMaskOnHover: false
    });

    // AJAX submit
    $("#formAbsensi").off("submit").on("submit", function(e) {
        e.preventDefault();

        $.ajax({
            url: "<?= base_url('referensi/jabatan/saveAbsensi') ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            beforeSend: function() {
                Swal.fire({
                    title: 'Mohon tunggu...',
                    didOpen: () => {
                        Swal.showLoading()
                    },
                    allowOutsideClick: false,
                });
            },
            success: function(response) {
                Swal.close();
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    // reload tab Absensi setelah simpan
                    if (typeof reloadAbsensiTab === 'function') {
                        reloadAbsensiTab();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: xhr.responseText || error
                });
            }
        });
    });
});
</script>