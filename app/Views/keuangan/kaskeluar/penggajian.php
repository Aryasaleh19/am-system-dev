<div class="card-body">
    <p class="demo-inline-spacing">
        <button class="btn btn-outline-primary me-1 collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            Buka Kas
        </button>
    </p>

    <div class="collapse" id="collapseExample">

        <div class="row g-3 mb-3">

            <div class="col-md-4">
                <div class="form-group">
                    <select name="pegawai" id="pegawai" class="form-control" required>
                        <option value="">[ Pilih Pegawai ]</option>
                    </select>
                    <small for="pegawai">Pilih Pegawai</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <select name="id_kas" id="id_kas" class="form-select" required>
                        <option value="">[ Pilih Kas Pembayar ]</option>
                    </select>
                    <small for="id_kas">Kas Pembayar</small>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <small class="text-info text-uppercase">Saldo Kas saat ini:</small>
                <h3 class="fw-bold text-end"><span id="saldoKasSaatIni">Rp 0</span></h3>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-6 mt-3">
                <span class="label label-default">Jenis Penerimaan</span>
                <table class="table table-bordered table-hover table-sm small" id="tablePenerimaan">
                    <thead>
                        <tr class="bg-orange">
                            <th class="text-left p-2" colspan="3">
                                <span class="text-muted text-uppercase" id="namaJabatanPegawai"></span>
                            </th>
                        </tr>
                        <tr class="text-center bg-primary">
                            <th class="text-center text-white p-1">PENERIMAAN</th>
                            <th class="text-center text-white p-1">JUMLAH MASTER<br>(Rp)</th>
                            <th class="text-center text-white p-1">JUMLAH<br>(Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td colspan="3" class="text-center text-muted">Data penerimaan tidak ditemukan!</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-end">Total Diterima</th>
                            <th class="text-end">Rp 0,-</th>
                            <th class="text-end" id="totalDiterima">Rp. 0</th>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <div class="col-md-6 mt-3">
                <div class="card shadow border">
                    <div class="card-body row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input id="noTransaksi" class="form-control" type="text" readonly required>
                                <small for="noTransaksi">No. Transaksi</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input id="tglTransaksi" class="form-control" type="date" required>
                                <small for="tglTransaksi">Tgl. Transaksi</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input id="bulanTransaksi" class="form-control" type="month">
                                <small for="bulanTransaksi">Bulan</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <input id="totalDibayarkan" class="form-control text-end" type="text" required>
                                <small for="totalDiterima">Total Dibayarkan (Rp)</small>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="form-group">
                                <button type="button" id="btnProses" class="btn btn-outline-info float-end"><i
                                        class="fa fa-users"></i>
                                    Proses Penggajian</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-danger text-white p-2 title-laporan">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Daftar Potongan Aktif
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-bordered table-hover table-sm" id="tableJenisPotongan">
                            <thead>
                                <tr class="text-center bg-light">
                                    <th class="text-center">POTONGAN</th>
                                    <th class="text-center">AKAD</th>
                                    <th class="text-center">TERBAYAR</th>
                                    <th class="text-center">TENOR</th>
                                    <th class="text-center">SISA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Data potongan tidak ditemukan!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// generate random nomor transaksi 
function generateNoTransaksi() {
    let now = new Date();
    let tanggal = now.getFullYear().toString().substr(2, 2) +
        ('0' + (now.getMonth() + 1)).slice(-2) +
        ('0' + now.getDate()).slice(-2);
    let random = Math.floor(1000 + Math.random() * 9000); // 4 digit random
    return `TRX-KK.${tanggal}-${random}`; // <-- gunakan backtick (`) dan ${}
}


// Format & parse angka
// ======================
function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID').format(value);
}

function parseAngka(value) {
    return parseInt(value.replace(/\D/g, '')) || 0;
}

// ======================
// Hitung total penerimaan
// ======================
function hitungTotal() {
    let total = 0;
    $('#tablePenerimaan tbody tr td.editable').each(function() {
        total += parseInt($(this).attr('data-value')) || 0;
    });
    $('#totalDiterima').text(formatRupiah(total));
    $('#totalDibayarkan').val(formatRupiah(total));
    cekFormTerbuka();
}

// ======================
// Cek apakah form boleh dibuka
// ======================
function cekFormTerbuka() {
    let saldoKas = parseAngka($('#saldoKasSaatIni').text());
    let totalBayar = parseAngka($('#totalDibayarkan').val());
    $('#noTransaksi').val(generateNoTransaksi());
    // Form terbuka jika saldo kas >= total bayar
    if (saldoKas >= totalBayar && totalBayar > 0) {
        $('#collapseExample input, #collapseExample select, #collapseExample textarea').prop('disabled', false);
        $('#btnProses').prop('disabled', false);

    } else {
        $('#collapseExample input, #collapseExample select, #collapseExample textarea').not(
            '#pegawai, #id_kas, #tablePenerimaan td.editable').prop('disabled', true);
        $('#btnProses').prop('disabled', true);
    }

    // Beri feedback saldo
    if (saldoKas < totalBayar) {
        $('#saldoKasSaatIni').addClass('text-danger');
    } else {
        $('#saldoKasSaatIni').removeClass('text-danger');
    }
}

$('#collapseExample').on('show.bs.collapse', function() {
    let $this = $(this);

    // Reset input, select, textarea
    $this.find('input[type="text"], input[type="date"], input[type="month"]').val('');
    $this.find('select').val(null).trigger('change');
    $this.find('textarea').val('');

    // Reset tabel penerimaan & potongan ke template awal
    let penerimaanTemplate = $('#tablePenerimaan tbody').data('template');
    if (penerimaanTemplate) {
        $('#tablePenerimaan tbody').html(penerimaanTemplate);
    }

    let potonganTemplate = $('#tableJenisPotongan tbody').data('template');
    if (potonganTemplate) {
        $('#tableJenisPotongan tbody').html(potonganTemplate);
    }

    // Reset totalDibayarkan & totalDiterima
    $('#totalDiterima').text('0');
    $('#totalDibayarkan').val('0');

    // Generate nomor transaksi baru
    $('#noTransaksi').val(generateNoTransaksi());

    // Reset tanggal & bulan
    let today = new Date();
    $('#tglTransaksi').val(today.toISOString().slice(0, 10));
    $('#bulanTransaksi').val(today.toISOString().slice(0, 7));

    // Reset saldo kas
    $('#saldoKasSaatIni').text('0').removeClass('text-danger');

    // Disable tombol proses
    $('#btnProses').prop('disabled', true);
});


$(document).ready(function() {
    // Simpan template awal tbody
    let penerimaanTemplate = $('#tablePenerimaan tbody').html();
    let potonganTemplate = $('#tableJenisPotongan tbody').html();
    // --- Select2 Kas Pembayar ---
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
    }).on('select2:select', function(e) {
        let saldo = e.params.data.saldo || 0;
        $('#saldoKasSaatIni').text(formatRupiah(saldo));
        cekFormTerbuka();
    }).on('select2:clear', function() {
        $('#saldoKasSaatIni').text('0');
        cekFormTerbuka();
    });

    // --- Select2 Pegawai ---
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
                    results: data.map(item => ({
                        id: item.ID,
                        text: item.NAMA
                    }))
                };
            },
            cache: true
        }
    }).on('select2:select', function(e) {
        let pegawaiId = e.params.data.id;

        // Ambil jabatan
        $.getJSON('<?= base_url("keuangan/penggajian/getJabatanByIdPegawai") ?>', {
            id: pegawaiId
        }, function(res) {
            $('#namaJabatanPegawai').text(res?.NAMA_JABATAN || '-');
        });

        // Ambil penerimaan
        $.getJSON('<?= base_url("keuangan/penggajian/getPenerimaanByIdPegawai") ?>', {
            id: pegawaiId
        }, function(res) {
            let tbody = $('#tablePenerimaan tbody').empty();
            let data = Array.isArray(res) ? res : (res ? [res] : []);
            let total = 0;
            data.forEach(item => {
                let jumlah = parseFloat(item.JUMLAH) || 0;
                total += jumlah;
                tbody.append(`<tr>
                    <td>${item.JENIS_PENERIMAAN||'-'}</td>
                    <td class="text-end">${formatRupiah(item.JUMLAH_MASTER||0)}</td>
                    <td class="text-end editable" contenteditable="true" data-value="${jumlah}">${formatRupiah(jumlah)}</td>
                </tr>`);
            });
            $('#totalDiterima').text(formatRupiah(total));
            $('#totalDibayarkan').val(formatRupiah(total));
            hitungTotal();
        });

        // Ambil pinjaman
        $.getJSON('<?= base_url("keuangan/pinjaman/getCekPinjamanPegawai") ?>', {
            id: pegawaiId
        }, function(res) {
            let tbody = $('#tableJenisPotongan tbody').empty();
            if (res && res.length > 0) {
                res.forEach(item => {
                    tbody.append(`<tr>
                        <td class="small text-center">Pinjaman</td>
                        <td class="small text-end">${formatRupiah(item.JUMLAH_AKAD)}</td>
                        <td class="small text-end">${formatRupiah(item.JUMLAH_AKAD - item.SISA)}</td>
                        <td class="small text-center">${item.TENOR}</td>
                        <td class="small text-end">${formatRupiah(item.SISA)}</td>
                    </tr>`);
                });
            } else {
                tbody.append(
                    `<tr><td colspan="5" class="text-center text-muted">Tidak ada potongan ditemukan!</td></tr>`
                );
            }
        });

    }).on('select2:clear', function() {
        // Reset nama jabatan
        $('#namaJabatanPegawai').text('..........');

        // Reset tabel penerimaan dan potongan ke template awal
        $('#tablePenerimaan tbody').html(penerimaanTemplate);
        $('#tableJenisPotongan tbody').html(potonganTemplate);

        // Reset total diterima dan total dibayarkan
        $('#totalDiterima').text('Rp. 0');
        $('#totalDibayarkan').val('0');

        // Disable tombol proses
        $('#btnProses').prop('disabled', true);

        // Reset saldo kas (opsional)
        $('#saldoKasSaatIni').text('0');
    });

    // --- Editable table ---
    $('#tablePenerimaan').on('input', 'td.editable', function() {
        let angka = parseAngka($(this).text());
        $(this).attr('data-value', angka).text(formatRupiah(angka));
        hitungTotal();
    }).on('blur', 'td.editable', function() {
        let angka = parseAngka($(this).text());
        $(this).attr('data-value', angka).text(formatRupiah(angka));
        hitungTotal();
    });

    // --- Input totalDibayarkan manual ---
    $('#totalDibayarkan').on('input', hitungTotal);

});
</script>