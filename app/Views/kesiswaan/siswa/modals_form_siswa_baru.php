<div class="modal fade" id="modalformSiswaBaru" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="formSiswaBaruLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-top modal-fullscreen">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelModal">Formulir Siswa Baru</h5>
                <div class="btn-group" role="group" aria-label="Button group">
                    <button type="submit" form="formSiswaBaru" class="btn btn-outline-primary">[ <i class="fa fa-save"
                            aria-hidden="true"></i> Simpan ]</button>
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal" aria-label="Tutup">[ <i
                            class="fa fa-window-close" aria-hidden="true"></i> Tutup ]</button>
                </div>
            </div>
            <div class="modal-body">
                <form id="formSiswaBaru">
                    <div class="row g-1">
                        <!-- Baris 1 -->
                        <div class="col-md-6">
                            <label for="nis" class="form-label">No. Induk Yayasan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nis" name="NIS" placeholder="Nomor Yayasan"
                                readonly required>
                        </div>
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="NAMA"
                                placeholder="Nama Lengkap Siswa" required>
                        </div>

                        <!-- Baris 2 -->
                        <div class="col-md-6">
                            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                            <input type="text" class="form-control" id="tempat_lahir" placeholder="Tempat Lahir Siswa"
                                name="TEMPAT_LAHIR">
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" placeholder="Tanggal Lahir Siswa"
                                name="TANGGAL_LAHIR">
                        </div>

                        <!-- Baris 3 -->
                        <div class="col-md-6">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select class="form-select" id="jenis_kelamin" placeholder="Jenis Kelamin Siswa"
                                name="JENIS_KELAMIN">
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="agama_id" class="form-label">Agama</label>
                            <select class="form-select" id="agama_id" placeholder="Agama Siswa" name="AGAMA_ID">
                                <option value="">[ Pilih ]</option>
                            </select>
                        </div>

                        <!-- DATA WILAYAH -->
                        <?= view('templates/wilayah') ?>


                        <!-- Baris 5 -->
                        <div class="col-md-12">
                            <label for="ALAMAT" class="form-label">Alamat</label>
                            <textarea name="ALAMAT" id="ALAMAT" class="form-control"
                                placeholder="Alamat Siswa"></textarea>
                        </div>

                        <!-- Baris 5 -->
                        <div class="col-md-6">
                            <label for="angkatan_id" class="form-label">Angkatan</label>
                            <select class="form-select" id="angkatan_id" placeholder="Angkatan Siswa"
                                name="ANGKATAN_ID">
                                <option value="">[ Pilih ]</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kontak_ortu" class="form-label">Kontak Orang Tua</label>
                            <input type="text" class="form-control" id="kontak_ortu"
                                placeholder="Kontak Orang Tua Siswa" name="KONTAK_ORANG_TUA">
                        </div>

                        <!-- Baris 6 -->
                        <div class="col-md-6">
                            <label for="nama_ayah" class="form-label">Nama Ayah</label>
                            <input type="text" class="form-control" id="nama_ayah" placeholder="Nama Ayah Siswa"
                                name="NAMA_AYAH">
                        </div>
                        <div class="col-md-6">
                            <label for="nama_ibu" class="form-label">Nama Ibu</label>
                            <input type="text" class="form-control" id="nama_ibu" placeholder="Nama Ibu Siswa"
                                name="NAMA_IBU">
                        </div>

                        <!-- Baris 7 -->
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" placeholder="Status Siswa" name="STATUS">
                                <option value="1" selected>Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/wilayah.js') ?>"></script>
<script src="<?= base_url('js/referensi.js') ?>"></script>

<script>
function generateNISPendaftaran() {
    const now = new Date();

    // Format tanggal & waktu: YYYYMMDDHHMMSS
    const year = String(now.getFullYear()).slice(-2); // Ambil 2 digit terakhir tahun
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hour = String(now.getHours()).padStart(2, '0');
    const minute = String(now.getMinutes()).padStart(2, '0');
    const second = String(now.getSeconds()).padStart(2, '0');

    // Tambahkan 3 digit random
    const random = String(Math.floor(Math.random() * 900) + 100);

    // Gabungkan jadi NIS unik dengan prefix 'P'
    return `P${year}${month}${day}${hour}${minute}${second}${random}`;

}

// Panggil saat awal
$(document).ready(function() {
    document.getElementById("nis").value = generateNISPendaftaran();
    disableAutocomplete();
});
$('#formSiswaBaru').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        type: "POST",
        url: "<?= base_url('kesiswaan/siswa/save') ?>",
        data: $(this).serialize(),
        dataType: "json",
        success: function(response) {
            if (response.status === 'success') {
                $('#modalformSiswaBaru').modal('hide');
                $('#angkatanTable').DataTable().ajax.reload();
                Swal.fire('Sukses', response.message, 'success');
            }
        },
        error: function(xhr) {
            let msg = xhr.responseJSON?.message || 'Gagal menyimpan data';
            Swal.fire('Error', msg, 'error');
        }
    });
});
</script>