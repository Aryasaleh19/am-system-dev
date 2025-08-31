<div class="col-12">
    <form id="formKasKeluar" enctype="multipart/form-data">
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
                    <i class="fa fa-chevron-down me-1" id="toggleIcon"></i> Buka Kas Keluar
                </button>
                <small>Untuk menginput Kas Masuk, silahkan klik Buka Kas Masuk.</small>

                <input type="hidden" id="petugas" name="petugas" value="<?= session()->get('user_id') ?>">

                <p class="demo-inline-spacing"></p>
                <div class="collapse" id="collapseExample">
                    <div class="d-grid d-sm-flex p-3 border row">

                        <div class="form-group mb-2 col-2">
                            <label for="nomor">Nomor Transaksi</label><sup class="text-danger">*</sup>
                            <input id="nomor" class="form-control bg-white text-center"
                                value="TRX.KK<?= random_int(10000000, 99999999) ?>" placeholder="Nomor Transaksi"
                                title="Nomor Transaksi" type="text" name="nomor_transaksi" readonly required>
                        </div>

                        <div class="form-group mb-2 col-2">
                            <label for="tanggal">Tanggal Transaksi</label><sup class="text-danger">*</sup>
                            <input id="tanggal" value="<?= date('Y-m-d') ?>" class="form-control text-center"
                                type="date" title="Tanggal" name="tanggal" required>
                        </div>
                        <div class="form-group mb-2 col-4">
                            <label for="id_jenis">Jenis Pengeluaran</label><sup class="text-danger">*</sup>
                            <select name="id_jenis" id="id_jenis" class="form-control" title="Kas Penerima" required>
                                <option value="">[ Pilih ]</option>
                                <?php foreach ($jenis_pengeluaran as $jenis): ?>
                                <option value="<?= esc($jenis['ID']) ?>"
                                    <?= old('id_jenis') == $jenis['ID'] ? 'selected' : '' ?>>
                                    [<?= esc($jenis['KODE']) ?>] - <?= esc($jenis['JENIS_PENGELUARAN']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-2 col-4">
                            <label for="penerima">Penerima</label><sup class="text-danger">*</sup>
                            <input id="penerima" class="form-control" title="Penerima" placeholder="Penerima"
                                type="text" name="penerima" required>
                        </div>

                        <div class="form-group mb-2 col-4">
                            <label for="id_rekening">Kas Pembayar</label><sup class="text-danger">*</sup>
                            <select name="id_rekening" id="id_rekening" class="form-control" title="Kas Penerima"
                                required>
                                <option value="">[ Pilih ]</option>
                                <?php foreach ($rekening_bank as $rekening): ?>
                                <option value="<?= esc($rekening['ID']) ?>"
                                    data-saldo="<?= esc($rekening['SALDO_AKHIR']) ?>"
                                    <?= old('id_rekening') == $rekening['ID'] ? 'selected' : '' ?>>
                                    <?= esc($rekening['NAMA_BANK']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-2 col-4">
                            <label for="saldoakhir">Saldo Kas Akhir</label><sup class="text-danger">*</sup>
                            <input id="saldoakhir" class="form-control text-end" title="Saldo Kas Akhir saat ini"
                                placeholder="Saldo akhir rekening saat ini" type="text" name="saldoakhir" readonly>
                        </div>


                        <div class="form-group mb-2 col-4">
                            <label for="jumlah">Jumlah (Rp)</label><sup class="text-danger">*</sup>
                            <input id="jumlah" class="form-control text-end" title="Jumlah diterima"
                                placeholder="Jumlah Dibayar (Rp)" type="text" name="jumlah" required>
                        </div>

                        <div class="form-group mb-2 col-4">
                            <label for="keterangan">Keterangan</label><sup class="text-danger">*</sup>
                            <input id="keterangan" class="form-control" title="Keterangan" placeholder="Keterangan"
                                type="text" name="keterangan" required>
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
                href="#riwayatKasKeluarCollapspanel" role="button" aria-expanded="false"
                aria-controls="riwayatKasKeluarCollapspanel" id="btnLihatRiwayat">
                <i class="fa fa-table" aria-hidden="true"></i> Lihat Riwayat Transaksi
            </a>
        </div>

        <div class="collapse" id="riwayatKasKeluarCollapspanel">
            <div class="d-grid">

                <table id="tabelKasMasuk" class="table small table-sm table-hover p-2">
                    <thead>
                        <tr>
                            <th class="text-center">NO.</th>
                            <th class="text-center">NO. TRANSAKSI</th>
                            <th class="text-center">TANGGAL</th>
                            <th class="text-center">JENIS PENGELUARAN</th>
                            <th class="text-center">PENERIMA</th>
                            <th class="text-center">JUMLAH</th>
                            <th class="text-center">KAS PEMBAYAR</th>
                            <th class="text-center">BUKTI</th>
                            <th class="text-center">KETERANGAN</th>
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
document.addEventListener('DOMContentLoaded', function() {
    const selectRekening = document.getElementById('id_rekening');
    const saldoAkhirInput = document.getElementById('saldoakhir');

    // Tampilkan saldo awal jika ada yang terpilih dari old()
    if (selectRekening.value) {
        const selectedOption = selectRekening.options[selectRekening.selectedIndex];
        saldoAkhirInput.value = selectedOption.dataset.saldo || '';
    }

    selectRekening.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        saldoAkhirInput.value = selectedOption.dataset.saldo || '';
    });
});


$('#btnLihatRiwayat').click(function(e) {
    e.preventDefault();
    table.ajax.reload();
});

const btnRiwayat = document.getElementById('btnLihatRiwayat');
const collapseRiwayat = document.getElementById('riwayatKasKeluarCollapspanel');

// Saat collapse dibuka → mata terbuka + "Tutup Riwayat"
collapseRiwayat.addEventListener('show.bs.collapse', () => {
    btnRiwayat.innerHTML = '<i class="fa fa-table" aria-hidden="true"></i> Tutup Riwayat Transaksi';
});

// Saat collapse ditutup → mata tertutup + "Lihat Riwayat"
collapseRiwayat.addEventListener('hide.bs.collapse', () => {
    btnRiwayat.innerHTML = '<i class="fa fa-table" aria-hidden="true"></i> Lihat Riwayat Transaksi';
});

$('#jumlah').on('keyup change', function() {
    let jumlah = $(this).val().replace(/\./g, ''); // hapus titik
    let saldo = $('#saldoakhir').val().replace(/\./g, '');

    jumlah = parseInt(jumlah) || 0;
    saldo = parseInt(saldo) || 0;

    // cek apakah lebih besar dari saldo
    if (jumlah > saldo) {
        // tambah class invalid kalau belum ada
        if (!$(this).hasClass('is-invalid')) {
            $(this).addClass('is-invalid');
            // tambahkan feedback hanya sekali
            $(this).after(
                '<div class="invalid-feedback">Jumlah pembayaran tidak boleh melebihi saldo akhir.</div>');
        }
        // kosongkan dan fokus ke input
        $(this).val('').focus();
    } else {
        // hapus class & feedback kalau sudah sesuai
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    }
});


let table; // pastikan global

$(document).ready(function() {
    const saldoAkhirInput = $('#saldoakhir');

    function formatRupiah(angka) {
        if (!angka) return '';
        let numberString = angka.toString().replace(/[^,\d]/g, '');
        let split = numberString.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    // Event change untuk Select2
    $('#id_rekening').on('change', function() {
        const saldo = $(this).find(':selected').data('saldo') || '';
        saldoAkhirInput.val(formatRupiah(saldo));
    });

    // Tampilkan saldo awal jika ada yang terpilih dari old()
    const selectedOption = $('#id_rekening').find(':selected');
    if (selectedOption.val()) {
        saldoAkhirInput.val(formatRupiah(selectedOption.data('saldo')));
    }

    table = $('#tabelKasMasuk').DataTable({
        processing: true,
        serverSide: true,
        width: '100%',
        ajax: {
            url: "<?= base_url('keuangan/kaskeluar/getriwayat') ?>",
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
                className: "text-left"
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
                className: "text-left"
            },
            {
                data: 9,
                className: "text-center"
            },
            {
                data: 10,
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
                    url: '/keuangan/kaskeluar/delete',
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
    keteranganInput = document.getElementById('keterangan');
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
    $("#id_jenis, #id_rekening").select2({
        placeholder: "[ Pilih ]",
        allowClear: true,
        width: '100%',
        theme: 'bootstrap-5'
    });


    // Collapse show event → generate nomor transaksi
    collapseElement.addEventListener('show.bs.collapse', () => {
        toggleButton.innerHTML = '<i class="fa fa-chevron-up me-1"></i> Tutup Kas Keluar';
        nomorInput.value = 'TRX.KK' + Math.floor(Math.random() * 90000000 + 10000000);
    });

    // Collapse hide event → reset form
    collapseElement.addEventListener('hide.bs.collapse', () => {
        toggleButton.innerHTML = '<i class="fa fa-chevron-down me-1"></i> Buka Kas Keluar';
        document.getElementById('formKasKeluar').reset();
        $("#id_jenis, #id_rekening").val(null).trigger('change');
        tanggalInput.value = new Date().toISOString().split('T')[0];
    });

    // Submit form dengan SweetAlert + AJAX
    $('#formKasKeluar').on('submit', function(e) {
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
                <p><b>Jenis Pengeluaran:</b> ${$('#id_jenis option:selected').text()}</p>
                <p><b>Kas Pembayar:</b> ${$('#id_rekening option:selected').text()}</p>
                <p><b>Diterima oleh:</b> ${$('#penerima').val()}</p>
                <p><b>Jumlah:</b> Rp ${jumlahFormatted}</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('keuangan/kaskeluar/save') ?>",
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
                            $('#formKasKeluar')[0].reset();
                            $("#id_jenis, #id_rekening").val(null).trigger(
                                'change');
                            table.ajax.reload(); // ← refresh tabel setelah simpan
                            window.location.reload();
                        } else if (res.status === 'error') {
                            let pesan = '';
                            for (let field in res.errors) {
                                pesan += res.errors[field] + '<br>';
                            }
                            Swal.fire('Validasi Gagal', pesan, 'warning');
                        }
                    },
                    error: function() {
                        // munculkan pesan asli dan code error
                        Swal.fire('Gagal', res.message ||
                            'Terjadi kesalahan saat menyimpan data.',
                            'error');
                    }
                });
            }
        });
    });

});
</script>