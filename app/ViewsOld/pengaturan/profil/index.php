<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <form id="profilForm" enctype="multipart/form-data">
        <div class="row">
            <!-- Kiri -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Nama Lengkap -->
                            <div class="col-lg-12 mt-3">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-buildings"></i></span>
                                    <input type="text" class="form-control"
                                        value="<?= esc($profil['NAMA_LENGKAP'] ?? '') ?>" id="nama_lengkap"
                                        name="nama_lengkap" placeholder="Nama Lengkap Lembaga">
                                </div>
                            </div>
                            <!-- Nama Singkat -->
                            <div class="col-lg-12 mt-3">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-home"></i></span>
                                    <input type="text" class="form-control" id="nama_singkat" name="nama_singkat"
                                        placeholder="Nama Singkatan" value="<?= esc($profil['NAMA_SINGKAT'] ?? '') ?>">
                                </div>
                            </div>
                            <!-- Alamat -->
                            <div class="col-lg-12 mt-3">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-map"></i></span>
                                    <input type="text" class="form-control" value="<?= esc($profil['ALAMAT'] ?? '') ?>"
                                        id="alamat" name="alamat" placeholder="Alamat Lembaga">
                                </div>
                            </div>
                            <!-- Logo -->
                            <div class="col-lg-12 mt-3">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-upload"></i></span>
                                    <input type="file" class="form-control" id="logo" name="logo">
                                </div>
                                <?php if (!empty($profil['LOGO'])): ?>
                                    <div class="mt-2" id="logoPreviewContainer">
                                        <img id="logoPreview" src="<?= base_url($profil['LOGO']) ?>" alt="Preview Logo"
                                            width="120">
                                    </div>
                                <?php else: ?>
                                    <div class="mt-2" id="logoPreviewContainer" style="display:none;">
                                        <img id="logoPreview" src="" alt="Preview Logo" width="120">
                                    </div>
                                <?php endif; ?>
                                <!-- Preview -->
                                <div class="mt-2" id="logoPreviewContainer" style="display:none;">
                                    <img id="logoPreview" src="" alt="Preview Logo" width="120">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Telp -->
                            <div class="col-lg-12 mt-3">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                    <input type="text" value="<?= esc($profil['TELP'] ?? '') ?>" class="form-control"
                                        id="telp" name="telp" placeholder="Nomor Telp">
                                </div>
                            </div>
                            <!-- Fax -->
                            <div class="col-lg-12 mt-3">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-message"></i></span>
                                    <input type="text" value="<?= esc($profil['FAX'] ?? '') ?>" class="form-control"
                                        id="fax" name="fax" placeholder="Fax">
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="col-lg-12 mt-3">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="email" value="<?= esc($profil['EMAIL'] ?? '') ?>" class="form-control"
                                        id="email" name="email" placeholder="Email">
                                </div>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="col-lg-12 mt-4">
                                <button type="button" class="btn btn-lg btn-outline-success w-100" id="addBtn">
                                    [ <i class="bx bx-save"></i> Submit ]
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<script>
    $(document).ready(function() {
        disableAutocomplete();

        $('#addBtn').on('click', function(e) {
            e.preventDefault();

            let formData = new FormData();
            formData.append('id', 1); // ID tetap 1
            formData.append('nama_lengkap', $('#nama_lengkap').val());
            formData.append('nama_singkat', $('#nama_singkat').val());
            formData.append('alamat', $('#alamat').val());
            formData.append('telp', $('#telp').val());
            formData.append('fax', $('#fax').val());
            formData.append('email', $('#email').val());

            const logo = $('#logo')[0].files[0];
            if (logo) {
                formData.append('logo', logo);
            }

            $.ajax({
                url: "<?= base_url('pengaturan/profil/update') ?>", // pastikan route ini benar
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menghubungi server'
                    });
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>