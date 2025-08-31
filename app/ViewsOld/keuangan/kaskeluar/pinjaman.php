<div class="card-body">
    <p class="demo-inline-spacing">
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            Buka Kas
        </button>
    </p>

    <div class="collapse" id="collapseExample">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="id_kas" class="form-label">Kas Pembayar</label>
                <select name="id_kas" id="id_kas" class="form-select" required>
                    <option value="">[ Pilih Kas Pembayar ]</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="pegawai" class="form-label">Pilih Pegawai</label>
                <select name="pegawai" id="pegawai" class="form-select" required>
                    <option value="">[ Pilih Pegawai ]</option>
                </select>
            </div>
        </div>

        <div class="row g-4 mt-3">
            <!-- Kiri: Saldo Kas & Informasi Pinjaman -->
            <div class="col-md-6">
                <div class="mb-4">
                    <small class="text-info text-uppercase">Saldo Kas saat ini:</small>
                    <h3 class="fw-bold text-end"><span id="saldoKasSaatIni">Rp 0</span></h3>
                </div>
                <hr>
                <small class="text-danger text-uppercase d-block mb-2">Informasi Pinjaman</small>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm" id="tableJenisPotongan">
                        <thead>
                            <tr class="text-center bg-danger">
                                <th class="text-white">JENIS<br>POTONGAN</th>
                                <th class="text-white">JUMLAH AKAD<br>(Rp)</th>
                                <th class="text-white">JUMLAH TERBAYAR<br>(Rp)</th>
                                <th class="text-white">SISA AKAD<br>(Rp)</th>
                                <th class="text-white">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Data pinjaman tidak ditemukan!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kanan: Form Pinjaman -->
            <div class="col-md-6 form-pinjaman">
                <div class="card shadow border">
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="noTransaksi" class="form-label">No. Transaksi</label><sup
                                    class="text-danger">*</sup>
                                <input id="noTransaksi" class="form-control" type="text" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label for="tglTransaksi" class="form-label">Tgl. Transaksi</label><sup
                                    class="text-danger">*</sup>
                                <input id="tglTransaksi" class="form-control" type="date" required>
                            </div>
                            <div class="col-md-8">
                                <label for="jumlahPinjaman" class="form-label">Jumlah Akad</label><sup
                                    class="text-danger">*</sup>
                                <input type="text" id="jumlahPinjaman" class="form-control text-end"
                                    placeholder="Masukkan jumlah pinjaman (Akad)">
                                <div id="errorPinjaman" class="text-danger small mt-1"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="tenor" class="form-label">Tenor (x / bulan)</label><sup
                                    class="text-danger">*</sup>
                                <input id="tenor" class="form-control text-center" type="number" placeholder="Tenor"
                                    required>
                            </div>
                            <div class="col-md-12">
                                <label for="keterangan" class="form-label">Keterangan</label><sup
                                    class="text-danger">*</sup>
                                <input id="keterangan" class="form-control" type="text" placeholder="Keterangan lainnya"
                                    required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-info" id="btnProsesPinjaman">
                                <i class="fa fa-users"></i> Proses Pinjaman
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function parseRupiah(str) {
    return Number(str.replace(/[^0-9]/g, '')) || 0;
}

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka);
}

// Toggle form & tombol berdasarkan kas & pegawai
function toggleFormPinjaman() {
    let kas = $('#id_kas').val();
    let pegawai = $('#pegawai').val();
    let disable = !(kas && pegawai);
    $('.form-pinjaman input, .form-pinjaman button').prop('disabled', disable);
}

$(document).ready(function() {

    // disable form awal
    toggleFormPinjaman();

    // Select2 Kas Pembayar
    $('#id_kas').select2({
        placeholder: '[ Pilih Kas Pembayar ]',
        theme: 'bootstrap-5',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '<?= base_url("api/select2/bank") ?>',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.map(d => ({
                        id: d.ID,
                        text: d.NAMA_BANK + ' - ' + d.NO_REKENING,
                        saldo: d.SALDO_AKHIR
                    }))
                };
            }
        }
    }).on('select2:select select2:clear', function(e) {
        let saldo = e.params && e.params.data ? e.params.data.saldo : 0;
        $('#saldoKasSaatIni').text(formatRupiah(saldo || 0));
        toggleFormPinjaman();
    });

    // Select2 Pegawai
    $('#pegawai').select2({
        placeholder: '[ Pilih Pegawai ]',
        theme: 'bootstrap-5',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '<?= base_url("api/select2/pegawai") ?>',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.map(d => ({
                        id: d.ID,
                        text: d.NAMA
                    }))
                };
            }
        }
    }).on('select2:select select2:clear', function() {
        toggleFormPinjaman();

        let pegawaiId = $(this).val();
        if (pegawaiId) {
            $.getJSON('<?= base_url("keuangan/pinjaman/getCekPinjamanPegawai") ?>', {
                id: pegawaiId
            }, function(res) {
                let tbody = $('#tableJenisPotongan tbody').empty();
                if (res && res.length > 0) {
                    res.forEach(item => {
                        tbody.append(`
                                <tr>
                                    <td class="text-center">${item.KETERANGAN || 'Pinjaman'}</td>
                                    <td class="text-end">${formatRupiah(item.JUMLAH_AKAD)}</td>
                                    <td class="text-end">${formatRupiah(item.JUMLAH_AKAD - item.SISA)}</td>
                                    <td class="text-end">${formatRupiah(item.SISA)}</td>
                                    <td class="text-center">
                                        ${
                                            item.STATUS == 1 
                                                ? `<button type="button" class="btn btn-xs btn-outline-danger deletePinjaman" data-id="${item.ID}">Batalkan</button>`
                                                : item.STATUS == 2 
                                                    ? `<span class="badge bg-success">Lunas</span>`
                                                    : `<span class="badge bg-secondary">Batal</span>`
                                        }
                                    </td>
                                </tr>
                            `);
                    });
                } else {
                    tbody.html(
                        `<tr><td colspan="5" class="text-center text-muted">Pegawai tersebut tidak memiliki pinjaman!</td></tr>`
                    );
                }
            });
        } else {
            $('#tableJenisPotongan tbody').html(
                `<tr><td colspan="5" class="text-center text-muted">Pegawai tersebut tidak memiliki pinjaman!</td></tr>`
            );
        }
    });

    // Format Rupiah saat input
    $('#jumlahPinjaman').on('input', function() {
        let val = parseRupiah($(this).val());
        $(this).val(val ? formatRupiah(val) : '');
        let saldoKas = parseRupiah($('#saldoKasSaatIni').text());
        if (val > saldoKas) {
            $('#errorPinjaman').text('Jumlah pinjaman tidak boleh melebihi Saldo Akhir Kas!').addClass(
                'is-invalid');
        } else {
            $('#errorPinjaman').text('').removeClass('is-invalid');
        }
    });

    // Generate nomor transaksi
    function generateNoTransaksi() {
        let now = new Date();
        let tgl = now.getFullYear().toString().substr(2, 2) + ('0' + (now.getMonth() + 1)).slice(-2) + ('0' +
            now.getDate()).slice(-2);
        let rand = Math.floor(1000 + Math.random() * 9000);
        return `TRX-KK.${tgl}-${rand}`;
    }

    // Collapse show event
    let tableJenisPotongan = $('#tableJenisPotongan tbody').html();
    $('#collapseExample').on('show.bs.collapse', function() {
        $(this).find('input[type="text"], input[type="date"], input[type="number"]').val('');
        $(this).find('select').val(null).trigger('change');
        $('#noTransaksi').val(generateNoTransaksi());
        $('#tglTransaksi').val(new Date().toISOString().slice(0, 10));

        $('#tableJenisPotongan tbody').html(tableJenisPotongan);
    });

    // Tombol Proses Pinjaman
    $('#btnProsesPinjaman').on('click', function() {
        let idKas = $('#id_kas').val();
        let pegawaiId = $('#pegawai').val();
        let jumlah = parseRupiah($('#jumlahPinjaman').val());
        let saldoKas = parseRupiah($('#saldoKasSaatIni').text());
        let tenor = $('#tenor').val();
        let noTrans = $('#noTransaksi').val();
        let tglTrans = $('#tglTransaksi').val();
        let keterangan = $('#keterangan').val();


        if (!idKas || !pegawaiId) {
            return Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih Kas dan Pegawai!'
            });
        }
        if (!jumlah || jumlah === 0) {
            return Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Masukkan jumlah pinjaman!'
            });
        }
        if (jumlah > saldoKas) {
            return Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Jumlah pinjaman melebihi Saldo Kas!'
            });
        }

        $.ajax({
            url: '<?= base_url("keuangan/pinjaman/savePinjaman") ?>',
            type: 'POST',
            data: {
                id_kas: idKas,
                pegawai: pegawaiId,
                noTransaksi: noTrans,
                tglTransaksi: tglTrans,
                jumlahPinjaman: jumlah,
                tenor: tenor,
                keterangan: keterangan
            },
            dataType: 'json',
            beforeSend: function() {
                $('#btnProsesPinjaman').prop('disabled', true).text('Memproses...');
            },
            success: function(res) {
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message
                    }).then(() => {
                        $('#collapseExample').collapse('hide');
                        $('#id_kas,#pegawai').val(null).trigger('change');
                        $('#jumlahPinjaman,#tenor').val('');
                        $('#tableJenisPotongan tbody').html(
                            `<tr><td colspan="3" class="text-center text-muted">Pegawai tersebut tidak memiliki pinjaman!</td></tr>`
                        );
                        $('#saldoKasSaatIni').text('0');
                    });
                } else {
                    // Tampilkan pesan error asli + kode jika ada
                    let msg = res.message || 'Terjadi kesalahan saat menyimpan pinjaman.';
                    if (res.code) msg += ` (Code: ${res.code})`;
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: msg
                    });
                }
            },
            error: function(xhr) {
                // Jika server mengirim JSON dengan message & code
                let errMsg = 'Terjadi kesalahan.';
                try {
                    let response = JSON.parse(xhr.responseText);
                    errMsg = response.message || errMsg;
                    if (response.code) errMsg += ` (Code: ${response.code})`;
                } catch (e) {
                    errMsg += ` Status: ${xhr.status}`;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errMsg
                });
            },
            complete: function() {
                $('#btnProsesPinjaman').prop('disabled', false).html(
                    '<i class="fa fa-users"></i> Proses Pinjaman');
            }
        });
    });

    // Batalkan pinjaman
    $(document).on('click', '.deletePinjaman', function() {
        let pinjamanId = $(this).data('id');

        Swal.fire({
            title: 'Konfirmasi Pembatalan',
            text: "Apakah Anda yakin ingin membatalkan pinjaman ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjut Batalkan',
            cancelButtonText: 'Tunda',
            color: '#d33',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'

        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("keuangan/pinjaman/deletePinjaman") ?>',
                    type: 'POST',
                    data: {
                        id: pinjamanId
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message
                            });
                            // refresh tabel pinjaman
                            $('#collapseExample').collapse('hide');
                            $('#id_kas,#pegawai').val(null).trigger('change');
                            $('#jumlahPinjaman,#tenor').val('');
                            $('#tableJenisPotongan tbody').html(
                                `<tr><td colspan="3" class="text-center text-muted">Pegawai tersebut tidak memiliki pinjaman!</td></tr>`
                            );
                            $('#saldoKasSaatIni').text('0');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan: ' + xhr.responseText
                        });
                    }
                });
            }
        });
    });



});
</script>