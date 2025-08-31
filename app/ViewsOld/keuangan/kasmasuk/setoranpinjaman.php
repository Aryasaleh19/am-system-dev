<div class="card-body">


    <button class="btn btn-outline-primary me-1 collapsed" type="button" data-bs-toggle="collapse"
        data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
        Buka Kas
    </button>

    <span class="float-end text-end">
        <span class="text-warning">
            <i class="fa fa-user-circle-o" aria-hidden="true"></i>
            <?= session()->get('nama') ?>
        </span>
        <br>
        <span class="text-mute small" id="tanggalRealTime"></span>
    </span>
    <hr>
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
                        <option value="">[ Pilih Kas Penerima ]</option>
                    </select>
                    <small for="id_kas">Kas Penerima</small>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <small class="text-info text-uppercase">Saldo Kas Penerima saat ini:</small>
                <h3 class="fw-bold text-end"><span id="saldoKasSaatIni">Rp 0</span></h3>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-header bg-danger text-white p-2 title-laporan">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Daftar Potongan Aktif
                    </div>
                    <div class="card-body p-0">
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
                                    <td colspan="5" class="text-center text-muted">Data ptongan tidak ditemukan!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-3" id="formSetoran">
                <div class="card shadow border">
                    <div class="card-body p-0">
                        <table class="table table-light">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center">NO. TRANSAKSI</th>
                                    <th class="text-center">TANGGAL</th>
                                    <th class="text-center">BULAN</th>
                                    <th class="text-center">TENOR KE</th>
                                    <th class="text-center">JUMLAH</th>
                                    <th class="text-center">KAS PENERIMA</th>
                                    <th class="text-center">PETUGAS</th>
                                    <th class="text-center">
                                        <button type="button" class="btn btn-primary btn-sm text-white"
                                            title="Klik untuk menambah row form penerimaan setoran pinjaman"
                                            id="btnTambahRow">
                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- jika terdapat data setoranmaka tampilkan, dan saat klik tombol add row form setoran, akan ditambahkan dibawah baris paling akhir -->
                                <tr>
                                    <td colspan="8">
                                        <p class="text-center text-muted">Klik button <strong>Tambah (+)</strong> untuk
                                            menambah setoran</p>
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-12">

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
// generate random nomor transaksi 
function generateNoTransaksi() {
    let now = new Date();
    let tanggal = now.getFullYear().toString().substr(2, 2) +
        ('0' + (now.getMonth() + 1)).slice(-2) +
        ('0' + now.getDate()).slice(-2);
    let random = Math.floor(1000 + Math.random() * 9000); // 4 digit random
    return `TRX-KM.${tanggal}-${random}`; // <-- gunakan backtick (`) dan ${}
}


// Format & parse angka
// ======================
function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID').format(value);
}

function parseAngka(value) {
    return parseInt(value.replace(/\D/g, '')) || 0;
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

    // Reset totalSetoran
    $('#totalSetoran').val('0');

    // Generate nomor transaksi baru
    $('#noTransaksi').val(generateNoTransaksi());

    // Reset tanggal & bulan
    let today = new Date();
    $('#tglTransaksi').val(today.toISOString().slice(0, 10));
    $('#bulanTransaksi').val(today.toISOString().slice(0, 7));

    // Reset saldo kas penerima setoran
    $('#saldoKasSaatIni').text('0').removeClass('text-danger');
});


$(document).ready(function() {
    let potonganTemplate = $('#tableJenisPotongan tbody').html();
    // --- Select2 Kas Penerima ---
    $('#id_kas').select2({
        placeholder: '[ Pilih Kas Penerima ]',
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

    }).on('select2:clear', function() {
        $('#saldoKasSaatIni').text('0');

    });

    // --- Select2 Pegawai ---
    let idPinjamanAktif = null;

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

        // Ambil pinjaman pegawai
        $.getJSON('<?= base_url("keuangan/pinjaman/getCekPinjamanPegawai") ?>', {
            id: pegawaiId
        }, function(res) {
            let tbodyPotongan = $('#tableJenisPotongan tbody').empty();
            if (res && res.length > 0) {
                res.forEach((item, index) => {
                    tbodyPotongan.append(`<tr>
                    <td class="small text-center">Pinjaman</td>
                    <td class="small text-end">${formatRupiah(item.JUMLAH_AKAD)}</td>
                    <td class="small text-end">${formatRupiah(item.JUMLAH_DIBAYAR)}</td>
                    <td class="small text-center">${item.TENOR}</td>
                    <td class="small text-end">${formatRupiah(item.SISA)}</td>
                </tr>`);
                    if (index === 0) idPinjamanAktif = item
                        .ID; // simpan ID pinjaman pertama
                });
            } else {
                tbodyPotongan.append(
                    '<tr><td colspan="5" class="text-center text-muted">Tidak ada potongan ditemukan!</td></tr>'
                );
                idPinjamanAktif = null;
            }

            // --- Tampilkan riwayat setoran ---
            if (idPinjamanAktif) {
                $.ajax({
                    url: '<?= base_url("keuangan/pinjaman/getDataSetoranByIdPinjaman") ?>',
                    method: 'GET',
                    data: {
                        idpinjaman: idPinjamanAktif
                    },
                    success: function(data) {
                        let tbodySetoran = $('#formSetoran tbody').empty();
                        let setoranData = typeof data === 'string' ? JSON.parse(
                            data) : data;

                        if (setoranData.length > 0) {
                            let totalSetor = 0;

                            setoranData.forEach(item => {
                                // Pastikan BULAN dalam 2 digit
                                let bulan = String(item.BULAN).padStart(2,
                                    '0');
                                let valueMonth = `${item.TAHUN}-${bulan}`;

                                // Tambah ke total
                                totalSetor += parseFloat(item.JUMLAH_SETOR);

                                tbodySetoran.append(`<tr data-id="${item.ID}">
                                    <td class="small text-center">${item.NO_TRANSAKSI}</td>
                                    <td class="small text-center">${item.TANGGAL}</td>
                                    <td class="small text-center">${valueMonth}</td>
                                    <td class="small text-center">${item.TENOR_KE}</td>
                                    <td class="small text-end">${formatRupiah(item.JUMLAH_SETOR)}</td>
                                    <td class="small text-left">${item.NAMA_BANK}</td>
                                    <td class="small text-end">${item.NAMA_PENGGUNA}</td>
                                    <td class="small text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm btnHapusSetoran" title="Hapus Setoran">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>`);
                            });

                            // Tambahkan row total di bawah
                            tbodySetoran.append(`
                               <tr class="fw-bold">
                                    <th colspan="4" class="small bg-info text-white text-end">Total Dibayar:</th>
                                    <th class="small bg-info text-white text-end">${formatRupiah(totalSetor)}</th>
                                    <th colspan="3" class="bg-info"></th>
                                </tr>
                            `);
                        } else {
                            tbodySetoran.append(
                                '<tr><td colspan="8" class="text-center text-muted">Klik tombol Tambah (+) untuk menambah setoran</td></tr>'
                            );
                        }

                    },
                    error: function(err) {
                        console.error('Gagal load riwayat setoran:', err);
                    }
                });
            } else {
                $('#formSetoran tbody').html(
                    '<tr><td colspan="6" class="text-center text-muted">Klik tombol Tambah (+) untuk menambah setoran</td></tr>'
                );
            }
        });
    });


    // event listener tombol hapus setoran
    $(document).on('click', '.btnHapusSetoran', function() {
        let row = $(this).closest('tr');
        let idSetoran = row.data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Setoran ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('keuangan/pinjaman/deleteSetoranByIdSetoran') ?>',
                    type: 'POST',
                    data: {
                        idSetoran: idSetoran
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            row.remove();
                            showToast('Terhapus', 'Data setoran berhasil dihapus!',
                                'success');
                            $('#collapseExample').collapse('hide');
                        } else {
                            Swal.fire({
                                html: "<i class='fa fa-user-secret text-danger fa-3x' aria-hidden='true'></i><br><h3 class='text-danger'>Auhtorization Failed</h3><br/> Anda tidak memiliki akses untuk menghapus data setoran ini. Hubungi petugas yang melakukan transaksi ini.",
                            });
                        }
                    },
                    error: function() {
                        showToast('Gagal', 'Terjadi kesalahan server',
                            'danger');
                    }
                });
            }
        });
    });



    // --- Form Setoran Multi-row ---
    function hitungTotalSetoran() {
        let total = 0;
        let tenor = 0;
        $('#formSetoran tbody tr').each(function() {
            let val = parseAngka($(this).find('.jumlahSetoran').val());
            if (!isNaN(val)) {
                total += val;
                tenor++;
            }
        });
        $('#jumlahDibayar').text('Rp ' + formatRupiah(total));
        $('#jumlahTenor').text(tenor + 'x');
    }

    // --- Cek apakah formSetoran bisa diisi ---
    function cekFormSetoran() {
        let pegawai = $('#pegawai').val();
        let kas = $('#id_kas').val();
        let disabled = !(pegawai && kas);
        $('#formSetoran tbody, #btnTambahRow').prop('disabled', disabled);
    }

    $('#pegawai, #id_kas').on('change select2:clear', cekFormSetoran);

    // --- Tombol tambah row ---
    $('#btnTambahRow').on('click', function() {
        if ($('#pegawai').val() === "" || $('#id_kas').val() === "" || !idPinjamanAktif) return;

        let tbody = $('#formSetoran tbody');
        if (tbody.find('tr td').length === 1) tbody.empty(); // hapus pesan kosong

        let noTransaksi = generateNoTransaksi();
        let today = new Date().toISOString().slice(0, 10);

        let row = `<tr>
                        <td class="p-1 text-center">
                            <input type="text" class="form-control form-control-sm small text-center noTransaksi" value="${noTransaksi}" readonly>
                            <small>No.Tranksaksi</small><sup class="text-danger">*</sup>
                        </td>
                        <td class="p-1 text-center">
                            <input type="date" class="form-control form-control-sm small text-center tglTransaksi" value="${today}">
                            <small>Tgl.Tranksaksi</small><sup class="text-danger">*</sup>
                        </td>
                        <td class="p-1 text-center">
                            <input type="month" class="form-control form-control-sm small text-center blnBayar">
                            <small>Bulan Setoran</small><sup class="text-danger">*</sup>
                        </td>
                        <td class="p-1 text-center">
                            <input type="number" class="form-control form-control-sm small text-center tenor" value="1" min="1">
                            <small>Tenor Ke</small><sup class="text-danger">*</sup>
                        </td>
                        <td colspan="2" class="p-1 text-center">
                            <input type="text" class="form-control form-control-sm small jumlahSetoran text-end" value="0">
                            <small>Jumlah Setoran</small><sup class="text-danger">*</sup>
                        </td>
                        <td class="text-center p-1" colspan="2">
                            <div class="btn-group  small p-1" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-outline-danger btn-sm small btnHapusRow" title="Batal Setor">
                                    <i class="fa fa-trash"></i> Batal
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm small btnSimpanSetoran" title="Simpan setoran">
                                    <i class="fa fa-save"></i> Simpan
                                </button>
                            </div>
                        
                            </td>
                    </tr>`;

        tbody.append(row);
        hitungTotalSetoran();
    });


    // --- Simpan setoran per row ---
    $('#formSetoran').on('click', '.btnSimpanSetoran', function() {
        let row = $(this).closest('tr');
        let noTransaksi = row.find('.noTransaksi').val();
        let tglTransaksi = row.find('.tglTransaksi').val();
        let blnBayar = row.find('.blnBayar').val();
        let tenor = row.find('.tenor').val();
        let jumlah = parseAngka(row.find('.jumlahSetoran').val());
        let idKas = $('#id_kas').val();

        if (!noTransaksi || !tglTransaksi || !jumlah || !idPinjamanAktif || !idKas || !blnBayar) {
            showToast('Perhatian', 'Masukkan semua data setoran dengan benar!', 'danger');
            return;
        }

        $.ajax({
            url: '<?= base_url("keuangan/pinjaman/simpanSetoranPinjaman") ?>',
            method: 'POST',
            data: {
                noTransaksi: noTransaksi,
                idpinjaman: idPinjamanAktif, // gunakan ID pinjaman aktif
                tglTransaksi: tglTransaksi,
                blnBayar: blnBayar,
                tenor: tenor,
                jumlah: jumlah,
                idKas: idKas
            },
            success: function(res) {
                let data = typeof res === 'string' ? JSON.parse(res) : res;
                if (data.success) {

                    showToast('Berhasil', data.message, 'success');
                    $('#collapseExample').collapse('hide');
                    row.remove(); // hapus row setelah sukses
                    hitungTotalSetoran();

                } else {
                    alert('Gagal: ' + data.message);
                }
            },
            error: function(err) {
                alert('Gagal menyimpan setoran, silahkan coba lagi!');
            }
        });
    });


    // --- Hapus row ---
    $('#formSetoran').on('click', '.btnHapusRow', function() {
        $(this).closest('tr').remove();
        if ($('#formSetoran tbody tr').length === 0) {
            $('#formSetoran tbody').html(
                '<tr><td colspan="8" class="text-center text-muted">Klik button <strong>Tambah (+)</strong> untuk menambah setoran</td></tr>'
            );
        }
        hitungTotalSetoran();
    });

    // --- Update total saat jumlah setoran diubah ---
    $('#formSetoran').on('input', '.jumlahSetoran', function() {
        let val = parseAngka($(this).val());
        $(this).val(formatRupiah(val));
        hitungTotalSetoran();
    });

});
</script>