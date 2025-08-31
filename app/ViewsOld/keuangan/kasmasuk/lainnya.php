<div class="col-12">
    <form id="formKasMasuk" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="card">
            <div class="card-body p-3">
                <span class="float-end text-warning">
                    <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                    <?= session()->get('nama') ?>
                </span>
                <br>
                <span class="text-mute small float-end" id="tanggalRealTime"></span>

                <button id="toggleButton" class="btn btn-outline-info me-1 collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false"
                    aria-controls="collapseExample">
                    <i class="fa fa-chevron-down me-1" id="toggleIcon"></i> Buka Kas Masuk
                </button>
                <small>Untuk menginput Kas Masuk, silahkan klik Buka Kas Masuk.</small>

                <input type="hidden" id="petugas" name="petugas" value="<?= session()->get('user_id') ?>">

                <p class="demo-inline-spacing"></p>
                <div class="collapse" id="collapseExample">
                    <div class="d-grid d-sm-flex p-3 border row">

                        <div class="form-group mb-2 col-2">
                            <label for="nomor">Nomor Transaksi</label><sup class="text-danger">*</sup>
                            <input id="nomor" class="form-control bg-white text-center"
                                value="TRX.<?= random_int(10000000, 99999999) ?>" placeholder="Nomor Transaksi"
                                title="Nomor Transaksi" type="text" name="nomor_transaksi" readonly required>
                        </div>

                        <div class="form-group mb-2 col-2">
                            <label for="tanggal">Tanggal Transaksi</label><sup class="text-danger">*</sup>
                            <input id="tanggal" value="<?= date('Y-m-d') ?>" class="form-control text-center"
                                type="date" title="Tanggal" name="tanggal" required>
                        </div>

                        <div class="form-group mb-2 col-4">
                            <label for="id_sekolah">Departemen Penerimaan</label><sup class="text-danger">*</sup>
                            <select name="id_sekolah" id="id_sekolah" class="form-control" title="Sekolah" required>
                                <option value="">[ Pilih ]</option>
                                <?php foreach ($sekolah as $s): ?>
                                <option value="<?= esc($s['ID']) ?>"><?= esc($s['NAMA_SEKOLAH']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                        <div class="form-group mb-2 col-4">
                            <label for="id_jenis">Jenis Penerimaan</label><sup class="text-danger">*</sup>
                            <select name="id_jenis" id="id_jenis" class="form-control" title="Jenis Penerimaan"
                                required>
                                <option value="">[ Pilih ]</option>
                            </select>
                        </div>

                        <div class="form-group mb-2 col-4">
                            <label for="id_rekening">Kas Penerima</label><sup class="text-danger">*</sup>
                            <select name="id_rekening" id="id_rekening" class="form-control" title="Kas Penerima"
                                required>
                                <option value="">[ Pilih ]</option>
                                <?php foreach ($rekening_bank as $rekening): ?>
                                <option value="<?= esc($rekening['ID']) ?>"
                                    <?= old('id_rekening') == $rekening['ID'] ? 'selected' : '' ?>>
                                    <?= esc($rekening['NAMA_BANK']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-2 col-4">
                            <label for="dari">Diterima Dari</label><sup class="text-danger">*</sup>
                            <input id="dari" class="form-control" title="Dari" placeholder="Dari" type="text"
                                name="dari" required>
                        </div>

                        <div class="form-group mb-2 col-4">
                            <label for="jumlah">Jumlah (Rp)</label><sup class="text-danger">*</sup>
                            <input id="jumlah" class="form-control text-end" title="Jumlah diterima"
                                placeholder="Jumlah Diterima (Rp)" type="text" name="jumlah" required>
                        </div>

                        <div class="form-group mb-2 col-4">
                            <label for="upload">Upload Bukti (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text">Upload</span>
                                <input type="file" class="form-control" id="upload" name="upload">
                            </div>
                        </div>

                        <div class="form-group mb-2 col-12">
                            <button type="submit" class="btn btn-outline-primary float-end">[ Submit ]</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="col-12">

    <div class="card p-1">
        <div class="card-header">
            <a class="btn btn-sm btn-outline-info me-1 collapsed float-end" data-bs-toggle="collapse"
                href="#riwayatKasMasukCollapspanel" role="button" aria-expanded="false"
                aria-controls="riwayatKasMasukCollapspanel" id="btnLihatRiwayat">
                <i class="fa fa-table" aria-hidden="true"></i> Lihat Riwayat Kas Masuk
            </a>
        </div>

        <div class="collapse" id="riwayatKasMasukCollapspanel" style="">
            <div class="d-grid d-sm-flex p-3 border">

                <table id="tabelKasMasuk" class="table table-sm table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">NO.</th>
                            <th class="text-center">NO. TRANSAKSI</th>
                            <th class="text-center">TANGGAL</th>
                            <th class="text-center">JENIS PENERIMAAN</th>
                            <th class="text-center">DITERIMA DARI</th>
                            <th class="text-center">JUMLAH</th>
                            <th class="text-center">KAS PENERIMA</th>
                            <th class="text-center">BUKTI</th>
                            <th class="text-center">PETUGAS</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>





<script>
$('#btnLihatRiwayat').click(function(e) {
    e.preventDefault();
    table.ajax.reload();
});

const btnRiwayat = document.getElementById('btnLihatRiwayat');
const collapseRiwayat = document.getElementById('riwayatKasMasukCollapspanel');

// Saat collapse dibuka → mata terbuka + "Tutup Riwayat"
collapseRiwayat.addEventListener('show.bs.collapse', () => {
    btnRiwayat.innerHTML = '<i class="fa fa-table" aria-hidden="true"></i> Tutup Riwayat Kas Masuk';
});

// Saat collapse ditutup → mata tertutup + "Lihat Riwayat"
collapseRiwayat.addEventListener('hide.bs.collapse', () => {
    btnRiwayat.innerHTML = '<i class="fa fa-table" aria-hidden="true"></i> Lihat Riwayat Kas Masuk';
});

let table; // pastikan global

$(document).ready(function() {
    table = $('#tabelKasMasuk').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('keuangan/kasmasuk/getriwayat') ?>",
            type: "POST"
        },
        columns: [{
                data: 0,
                className: "text-center"
            },
            {
                data: 1,
                className: "text-center"
            },
            {
                data: 2,
                className: "text-center"
            },
            {
                data: 3,
                className: "text-center"
            },
            {
                data: 4
            },
            {
                data: 5,
                className: "text-right"
            },
            {
                data: 6
            },
            {
                data: 7,
                className: "text-center"
            },
            {
                data: 8,
                className: "text-center"
            },
            {
                data: 9,
                className: "text-center"
            }
        ],
        order: [
            [2, "desc"]
        ]
    });

    // Event delegation untuk tombol hapus
    $('#tabelKasMasuk tbody').on('click', '.btn-delete', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Yakin?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/keuangan/kasmasuk/delete',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil!', response.message, 'success');
                            table.ajax.reload(null, false);
                            window.location.reload();
                        } else {
                            Swal.fire({
                                html: `<i class="fa fa-user-secret" style="font-size:50px;color:#f39c12" aria-hidden="true"></i> <h4>Authorization!</h4> <p>${response.message}</p>`,
                                showConfirmButton: true,
                            });

                        }
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data',
                            'error');
                    }
                });
            }
        });
    });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {

    const jumlahInput = document.getElementById('jumlah');
    const nomorInput = document.getElementById('nomor');
    const tanggalInput = document.getElementById('tanggal');
    const collapseElement = document.getElementById('collapseExample');
    const toggleButton = document.getElementById('toggleButton');

    // Format rupiah saat ketik
    jumlahInput.addEventListener('input', function() {
        let angka = this.value.replace(/[^0-9]/g, '');
        this.value = angka ? angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".") : "";
    });

    // Realtime tanggal
    function updateTanggalRealTime() {
        const now = new Date();
        const tgl = String(now.getDate()).padStart(2, '0');
        const bln = String(now.getMonth() + 1).padStart(2, '0');
        const thn = now.getFullYear();
        const jam = String(now.getHours()).padStart(2, '0');
        const mnt = String(now.getMinutes()).padStart(2, '0');
        const dtk = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('tanggalRealTime').textContent =
            `${tgl}-${bln}-${thn} ${jam}:${mnt}:${dtk} WITA`;
    }
    updateTanggalRealTime();
    setInterval(updateTanggalRealTime, 1000);

    // Select2
    $("#id_jenis, #id_rekening, #id_sekolah").select2({
        placeholder: "[ Pilih ]",
        allowClear: true,
        width: '100%',
        theme: 'bootstrap-5'
    });

    $('#id_sekolah').on('change', function() {
        const sekolahId = $(this).val();

        if (sekolahId) {
            $.ajax({
                url: "<?= base_url('api/select2/getJenisPenerimaanBySekolah') ?>",
                type: "GET",
                data: {
                    sekolah_id: sekolahId
                },
                dataType: "json",
                success: function(data) {
                    const jenisSelect = $('#id_jenis');
                    jenisSelect.empty().append('<option value="">[ Pilih ]</option>');

                    data.forEach(function(item) {
                        jenisSelect.append(
                            `<option value="${item.ID}">${item.JENIS_PENERIMAAN}</option>`
                        );
                    });

                    jenisSelect.val(null).trigger('change'); // reset Select2
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil data jenis penerimaan', 'error');
                }
            });
        } else {
            $('#id_jenis').empty().append('<option value="">[ Pilih ]</option>').val(null).trigger(
                'change');
        }
    });


    // Collapse show event → generate nomor transaksi
    collapseElement.addEventListener('show.bs.collapse', () => {
        toggleButton.innerHTML = '<i class="fa fa-chevron-up me-1"></i> Tutup Kas Masuk';
        nomorInput.value = 'TRX.' + Math.floor(Math.random() * 90000000 + 10000000);
    });

    // Collapse hide event → reset form
    collapseElement.addEventListener('hide.bs.collapse', () => {
        toggleButton.innerHTML = '<i class="fa fa-chevron-down me-1"></i> Buka Kas Masuk';
        document.getElementById('formKasMasuk').reset();
        $("#id_jenis, #id_rekening, #id_sekolah").val(null).trigger('change');
        tanggalInput.value = new Date().toISOString().split('T')[0];
    });

    // Submit form dengan SweetAlert + AJAX
    $('#formKasMasuk').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        let jumlahFormatted = $('#jumlah').val();
        let jumlahClean = jumlahFormatted.replace(/\./g, '');
        formData.set('jumlah', jumlahClean);

        Swal.fire({
            title: 'Konfirmasi Data',
            html: `
                <p><b>Nomor Transaksi:</b> ${$('#nomor').val()}</p>
                <p><b>Tanggal:</b> ${$('#tanggal').val()}</p>
                <p><b>Jenis Penerimaan:</b> ${$('#id_jenis option:selected').text()}</p>
                <p><b>Kas Penerima:</b> ${$('#id_rekening option:selected').text()}</p>
                <p><b>Diterima Dari:</b> ${$('#dari').val()}</p>
                <p><b>Jumlah:</b> Rp ${jumlahFormatted}</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('keuangan/kasmasuk/save') ?>",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Menyimpan...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Berhasil', res.message, 'success');
                            $('#formKasMasuk')[0].reset();
                            $("#id_jenis, #id_rekening").val(null).trigger(
                                'change');
                            table.ajax.reload(); // ← refresh tabel setelah simpan
                        } else if (res.status === 'error') {
                            let pesan = '';
                            for (let field in res.errors) {
                                pesan += res.errors[field] + '<br>';
                            }
                            Swal.fire('Validasi Gagal', pesan, 'warning');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                    }
                });
            }
        });
    });

});
</script>