<?php if (!empty($siswa)) : ?>
<div class="row g-1">
    <div class="col-3">
        <div class="card shadow-none bg-transparent border border-primary mb-3">
            <?php 
            $idRiwayatSekolahAktifByNis = $sekolah['ID'] ?? null; 
            $namaSekolahAktifByNis = $sekolah['NAMA_SEKOLAH'] ?? null; 
            ?>
            <div class="card-body">
                <h5 class="card-title"><i class="fa fa-pencil-square" aria-hidden="true"></i> Form Kenaikan Kelas -
                    <span class="text-primary text-uppercase text-end"><?= $namaSekolahAktifByNis; ?></span>
                </h5>
                <form id="formKenaikanKelas">
                    <input type="hidden" name="idRiwayatSekolah" id="idRiwayatSekolah"
                        value="<?= $idRiwayatSekolahAktifByNis ?>">
                    <div class="form-group mb-1">
                        <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-white" value="<?= $siswa['NIS'] ?>" id="nis"
                            name="NIS" placeholder="Nomor Induk Siswa" readonly required>
                    </div>
                    <div class="form-group mb-1">
                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-white" value="<?= $siswa['NAMA'] ?>" id="nama"
                            name="NAMA" placeholder="Nama Lengkap Siswa" readonly required>
                    </div>
                    <div class="form-group mb-1">
                        <label for="id_ruangan" class="form-label">Ruangan <span class="text-danger">*</span></label>
                        <select name="id_ruangan" id="id_ruangan" class="form-control" required>
                            <option value="">[ Pilih ]</option>
                        </select>
                    </div>
                    <div class="form-group mb-1">
                        <label for="no_kelas" class="form-label">Tingkat<span class="text-danger">*</span></label>
                        <select name="no_kelas" id="no_kelas" class="form-control" title="Tingkat Kelas (nomor)"
                            required>
                            <option value="">[ Pilih ]</option>
                            <option value="0">Tingkat 0</option>
                            <option value="1">Tingkat 1</option>
                            <option value="2">Tingkat 2</option>
                            <option value="3">Tingkat 3</option>
                            <option value="4">Tingkat 4</option>
                            <option value="5">Tingkat 5</option>
                            <option value="6">Tingkat 6</option>
                            <option value="7">Tingkat 7</option>
                            <option value="8">Tingkat 8</option>
                            <option value="9">Tingkat 9</option>
                            <option value="10">Tingkat 10</option>
                            <option value="11">Tingkat 11</option>
                            <option value="12">Tingkat 12</option>
                            <option value="13">Tingkat 13</option>
                            <option value="14">Tingkat 14</option>
                            <option value="15">Tingkat 15</option>
                        </select>
                    </div>
                    <div class="form-group mb-1">
                        <label for="tanggal" class="form-label">TMT</label>
                        <input type="date" name="tanggal" id="tanggalPrestasi" class="form-control"
                            value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group mb-1">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="statusSiswa" class="form-control" required>
                            <option value="">[ Pilih ]</option>
                            <option value="1">Aktif</option>
                            <option value="2">Selesai</option>
                            <option value="3">Lulus</option>
                            <option value="0">Berhenti</option>
                        </select>
                    </div>
                    <div class="form-group mb-1">
                        <button type="button" class="btn btn-outline-primary" id="btnSimpanPRestasi">[ <i
                                class="fa fa-save" aria-hidden="true"></i> Simpan ]</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-9">
        <div class="card shadow-none bg-transparent border border-primary mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fa fa-history" aria-hidden="true"></i> Riwayat</h5>
                <table class="table table-striped table-responsive small table-hover" id="riwayatSekolahTable">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama Sekloah</th>
                            <th class="text-center">Ruang Kelas</th>
                            <th class="text-center">Tingkat</th>
                            <th class="text-center">TMT</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data riwayat sekolah akan dimuat di sini via AJAX -->
                        <tr>
                            <td colspan="7" class="text-center">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<?php endif; ?>

<script src="<?= base_url('js/referensi.js') ?>"></script>
<script>
$(document).ready(function() {

    const nisInputSelector = '#modalformSiswaBaru .siswa-nis';
    const formSelector = '#formKenaikanKelas';
    const btnSimpanSelector = '#btnSimpanPRestasi';
    const tbodySelector = '#riwayatSekolahTable tbody';

    let editMode = false;
    let currentEditId = null;

    function loadRiwayatPrestasi() {
        const nis = $(nisInputSelector).val();
        if (!nis) return console.warn("NIS tidak tersedia");

        $.ajax({
            url: '<?= base_url('kesiswaan/siswa/riwayatPrestasi') ?>',
            type: 'GET',
            data: {
                nis
            },
            dataType: 'json',
            success: function(data) {
                const tbody = $(tbodySelector);
                tbody.empty();

                if (!data || data.length === 0) {
                    tbody.append(
                        '<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>');
                    return;
                }

                const statusMap = {
                    1: {
                        text: "Aktif",
                        class: "bg-success"
                    },
                    2: {
                        text: "Selesai",
                        class: "bg-info"
                    },
                    3: {
                        text: "Lulus",
                        class: "bg-primary"
                    },
                    0: {
                        text: "Berhenti",
                        class: "bg-danger"
                    }
                };

                data.forEach((item, idx) => {
                    const status = statusMap[item.STATUS] || {
                        text: "-",
                        class: "bg-secondary"
                    };
                    tbody.append(`
                        <tr>
                            <td class="text-center">${idx + 1}</td>
                            <td class="text-center">${item.NAMA_SEKOLAH ?? '-'}</td>
                            <td class="text-center">${item.RUANGAN ?? '-'}</td>
                            <td class="text-center">${item.KELAS ?? '-'}</td>
                            <td class="text-center">${item.TMT ?? '-'}</td>
                            <td class="text-center"><span class="badge ${status.class}">${status.text}</span></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-warning btnEditPrestasi" title="Ubah Data" data-id="${item.ID}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
                                    <button class="btn btn-sm btn-outline-danger btnDeletePrestasi" title="Hapus Data" data-id="${item.ID}"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
            }
        });
    }

    function loadRiwayatById(id) {
        $.ajax({
            url: '<?= base_url('kesiswaan/siswa/getRiwayatById') ?>',
            type: 'GET',
            data: {
                id
            },
            dataType: 'json',
            success: function(res) {
                if (!res) return Swal.fire({
                    icon: 'warning',
                    title: 'Data tidak ditemukan'
                });

                setTimeout(() => {
                    $("#id_ruangan").val(res.RUANGAN_ID).trigger("change");
                    $("#no_kelas").val(res.KELAS).trigger("change");
                    $("#tanggalPrestasi").val(res.TMT ? res.TMT.slice(0, 10) : "");
                    $("#statusSiswa").val(String(res.STATUS)).trigger("change");
                }, 50);

                editMode = true;
                currentEditId = res.ID;
                $(btnSimpanSelector).text('Update');

                Swal.fire({
                    icon: 'info',
                    title: 'Data Sebelumnya!',
                    html: `
                        Sekolah: <b>${res.NAMA_SEKOLAH}</b><br>
                        Ruangan: <b>${res.RUANGAN}</b><br>
                        Kelas: <b>${res.KELAS}</b><br>
                        TMT: <b>${res.TMT}</b><br>
                        Status: <b>${res.STATUS}</b>
                    `,
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal memuat data',
                    text: xhr.responseText
                });
            }
        });
    }

    // Klik edit
    $(document).off('click', '.btnEditPrestasi').on('click', '.btnEditPrestasi', function() {
        const id = $(this).data('id');
        loadRiwayatById(id);
    });

    // Klik delete
    $(document).off('click', '.btnDeletePrestasi').on('click', '.btnDeletePrestasi', function() {
        const idPrestasi = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data prestasi akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loader').show();
                $.ajax({
                    url: '<?= base_url("kesiswaan/siswa/deletePrestasi") ?>',
                    type: 'POST',
                    data: {
                        id: idPrestasi
                    },
                    dataType: 'json',
                    success: function(res) {
                        $('#loader').hide();
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            // Refresh tabel
                            loadRiwayatPrestasi();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `Gagal (Code: ${res.code ?? '-'})`,
                                html: `Message: <span class="text-danger small">${res.message ?? '-'}</span>`
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#loader').hide();
                        let message = xhr.responseText;
                        try {
                            message = JSON.parse(xhr.responseText).message ??
                                message;
                        } catch (e) {}
                        Swal.fire({
                            icon: 'error',
                            title: `Response Code: ${xhr.status}`,
                            html: `Message: <span class="text-danger small">${message}</span>`
                        });
                    }
                });
            }
        });
    });


    // Simpan / update
    $(document).off('click', btnSimpanSelector).on('click', btnSimpanSelector, function() {
        const form = $(formSelector)[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const data = {
            id_prestasi: currentEditId, // wajib dikirim ke controller
            NIS: $(nisInputSelector).val(),
            idRiwayatSekolah: $('#idRiwayatSekolah').val(),
            id_ruangan: $('#id_ruangan').val(),
            no_kelas: $('#no_kelas').val(),
            tanggal: $('#tanggalPrestasi').val(),
            status: $('#statusSiswa').val()
        };

        const url = editMode ?
            "<?= base_url('kesiswaan/siswa/updatePrestasi') ?>" :
            "<?= base_url('kesiswaan/siswa/savePrestasi') ?>";

        $('#loader').show();
        $.ajax({
            url,
            type: 'POST',
            data,
            dataType: 'json',
            success: function(res) {
                $('#loader').hide();
                if (res.status === 'success' || res.status === true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: editMode ? 'Data berhasil diupdate' :
                            'Data berhasil disimpan',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadRiwayatPrestasi();

                    form.reset();
                    $('#id_ruangan, #no_kelas, #statusSiswa').val(null).trigger("change");
                    $(btnSimpanSelector).text('Simpan');
                    editMode = false;
                    currentEditId = null;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        html: `Message: ${res.message ?? '-'}`
                    });
                }
            },
            error: function(xhr) {
                $('#loader').hide();
                let message = xhr.responseText;
                try {
                    message = JSON.parse(xhr.responseText).message ?? message
                } catch (e) {}
                Swal.fire({
                    icon: 'warning',
                    title: `Response Code: ${xhr.status}`,
                    html: `Message: <span class="text-danger small">${message}</span>`
                });
            }
        });
    });

    // Load awal
    loadRiwayatPrestasi();

});
</script>