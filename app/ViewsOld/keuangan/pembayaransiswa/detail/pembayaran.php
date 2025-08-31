<div class="row g-1">
    <div class="col-5">
        <div class="card card shadow-none bg-transparent border border-primary mb-3">
            <div class="card-header p-2">
                <h5 class="card-title pt-1">
                    <i class="fa fa-list" aria-hidden="true"></i> Jenis Pembayaran (Tagihan)
                </h5>
            </div>
            <div class="card-body" style="height: 300px; overflow-y: auto;" id="vertical-example">
                <div class="list-group list-group-flush" id="jenisPembayaranList">
                    <?php if (!empty($jenis_penerimaan)): ?>
                    <?php $sisa_dibayar = $jenis_penerimaan[0]['SISA_DIBAYAR']; ?>

                    <?php foreach ($jenis_penerimaan as $jenis): ?>
                    <?php 
                    if ($jenis['LUNAS'] == 1){
                        $icons = ' <small class="text-muted"></small> ';
                        $labelLunas = '<small class="badge rounded-pill bg-info">Lunas</small><sup>📌</sup>';
                    }else{
                        $icons = ' <small class="text-muted"></small> ';
                        $labelLunas = ' ';
                    }  
                    ?>
                    <a href="javascript:void(0);" class="list-group-item list-group-item-action p-2"
                        data-id="<?= $jenis['ID_JENIS_PENERIMAAN'] ?>" data-idMapPenerimaan="<?= $jenis['ID'] ?>"
                        data-jenis="<?= esc($jenis['JENIS_PENERIMAAN']) ?>" data-lunas="<?= $jenis['LUNAS'] ?>"
                        data-sisa="<?= $jenis['SISA_DIBAYAR'] ?>"
                        onclick="detailPembayaran(event, '<?= $jenis['ID_JENIS_PENERIMAAN'] ?>')">

                        <span class="badge badge-center rounded-pill bg-info ml-2"><?= $jenis['TENOR'] ?>x</span>
                        <?= esc($jenis['JENIS_PENERIMAAN']) ?> <?= $labelLunas ?>



                        <span class="text-danger float-end p-1 small"
                            id="jumlahTelahDibayarPembayaran_<?= $jenis['ID_JENIS_PENERIMAAN'] ?>">
                            <?= number_format($jenis['SISA_DIBAYAR'], 0, ',', '.') ?> </span>

                        <span class="text-info float-end p-1 small mr-2 ml-2"
                            id="jumlahTelahDibayarPembayaran_<?= $jenis['ID_JENIS_PENERIMAAN'] ?>">
                            <?= number_format($jenis['TELAH_DIBAYAR'], 0, ',', '.') ?> <?= $icons; ?> </span>

                        <span class="text-primary float-end p-1 small"
                            id="jumlahPembayaran_<?= $jenis['ID_JENIS_PENERIMAAN'] ?>">Rp
                            <?= number_format($jenis['JUMLAH'], 0, ',', '.') ?> <?= $icons; ?> </span>


                    </a>
                    <?php endforeach; ?>


                    <?php else: ?>
                    <div class="p-2 text-muted text-center">
                        <i class="bi bi-exclamation-diamond-fill text-warning"></i>
                        Tidak ada jenis pembayaran ditemukan. Silahkan menambahkan jenis pembayaran pada siswa ini.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <div class="col-7">
        <div class="card shadow-none bg-transparent border border-primary mb-3">
            <div class="card-header p-2">
                <h5 class="card-title pt-1">
                    <i class="bi bi-person-workspace"></i> Formulir Pembayaran
                    <span id="namaJenisPembayaran" class="text-primary"><small>Klik Jenis Pembayaran untuk membuka
                            Formulir</small></span>
                </h5>
            </div>

            <div class="card-body" style="height: 300px;">
                <div class="form-wrapper" style="pointer-events:none; opacity:0.6;">
                    <form id="formPembayaran" method="post" autocomplete="off"
                        action="<?= base_url('keuangan/pembayaransiswa/simpanPembayaran') ?>">

                        <input type="hidden" name="nis" value="<?= esc($nis) ?>">
                        <input type="hidden" name="jumlah_asli" id="jumlahAsli">
                        <input type="hidden" name="id_jenis_penerimaan" id="idJenisPenerimaan" value="">
                        <input type="hidden" name="idMapPenerimaan" id="idMapPenerimaan" value="">

                        <div class="row g-1 align-items-center">
                            <div class="col-md-4">
                                <label for="tanggalPembayaran" class="form-label mb-0">Tanggal</label><sup
                                    class="text-danger">*</sup>
                                <input type="date" class="form-control" id="tanggalPembayaran" name="tanggal" required
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="bulanPembayaran" class="form-label mb-0">Bulan Pembayaran</label><sup
                                    class="text-danger">*</sup>
                                <input type="month" class="form-control" id="bulanPembayaran" name="bulanPembayaran"
                                    required>
                            </div>

                            <div class="col-md-4">
                                <label for="jumlahBayar" class="form-label mb-0">Jumlah</label><sup
                                    class="text-danger">*</sup>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="jumlahBayar" name="jumlah"
                                        placeholder="0" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="metodeBayar" class="form-label mb-0">Kas Bank</label><sup
                                    class="text-danger">*</sup>
                                <select class="form-select" id="metodeBayar" name="metode" required>
                                    <option value="" disabled selected>Pilih Bank</option>
                                    <?php foreach ($rekening_bank as $rekening): ?>
                                    <option value="<?= esc($rekening['ID']) ?>"><?= esc($rekening['NAMA_BANK']) ?> -
                                        <?= esc($rekening['NO_REKENING']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label for="upload" class="form-label mb-0">Bukti Pembayaran</label>
                                <div class="input-group">
                                    <span class="input-group-text">Upload Bukti</span>
                                    <input type="file" class="form-control" id="upload" name="upload" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="catatan" class="form-label mb-0">Catatan (Opsional)</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="1"
                                    placeholder="Contoh: Pembayaran bulan Agustus atau keterangan lainnya"></textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-outline-primary float-end w-50">
                                    <i class="bi bi-send-check"></i> Simpan Pembayaran
                                </button>
                            </div>
                        </div>

                    </form>
                    <!-- Tempat untuk alert -->
                    <div id="alertArea" class="mt-3"></div>
                    <div id="listPembayaranTerakhir"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <button type="button" class="btn btn-outline-primary btn-sm float-end" id="btnPrintRiwayat" disabled><i
                class="bi bi-printer"></i> Print
            Riwayat</button>
        <h6><i class="bi bi-clock-history"></i> Riwayat Pembayaran <span id="namaRiwayatJenisPembayaran"
                class="text-primary"></span></h6>

        <div class="table-responsive card shadow-none bg-transparent border border-info mb-3"
            style="max-height: 300px; overflow-y: auto;">
            <div id="tabelPembayaran">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="table-light">
                        <tr style="height: 40px;">
                            <th class="text-center">#</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Bulan</th>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Kas Bank</th>
                            <th class="text-center">Catatan</th>
                            <th class="text-center">Bukti</th>
                            <th class="text-center">Admin</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabelRiwayatPembayaran">
                        <?php if (!empty($riwayat_pembayaran)): ?>
                        <?php foreach ($riwayat_pembayaran as $i => $item): ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td class="text-center"><?= date('d/m/Y', strtotime($item['TANGGAL'])) ?></td>
                            <td class="text-center"><?= date('d/m/Y', strtotime($item['TANGGAL'])) ?></td>
                            <td class="text-center"><?= date('m', strtotime($item['BULAN_TAGIHAN'])) ?></td>
                            <td class="text-center"><?= date('Y', strtotime($item['TAHUN_TAGIHAN'])) ?></td>
                            <td class="text-end">Rp <?= number_format($item['JUMLAH'], 0, ',', '.') ?></td>
                            <td><?= esc($item['NAMA_BANK'] ?? 'Tunai') ?></td>
                            <td><?= esc($item['CATATAN']) ?></td>
                            <td><?= esc($item['NAMA_PENGGUNA']) ?></td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-outline-danger"
                                    onclick="hapusPembayaran(event, '<?= $item['ID_PEMBAYARAN'] ?>')">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                        <tr>
                            <td colspan="10" class="text-center text-muted">Silahkan pilih Jenis Pembayaran untuk
                                menampilkan data</td>
                        </tr>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formPembayaran');
    const formWrapper = document.querySelector('.form-wrapper');

    // Disable semua input & tombol submit di awal
    form.querySelectorAll('input, select, textarea, button[type="submit"]').forEach(el => el.disabled = true);
    formWrapper.style.pointerEvents = 'none';
    formWrapper.style.opacity = '0.6';
});

function formatRupiah(value) {
    value = value.replace(/[^,\d]/g, '');
    const split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    const ribuan = split[0].substr(sisa).match(/\d{3}/g);

    if (ribuan) {
        const separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
}

// Format saat input jumlah
$('#jumlahBayar').on('input', function() {
    const formatted = formatRupiah(this.value);
    this.value = formatted;

    // Simpan nilai tanpa format ke hidden input
    const rawValue = this.value.replace(/[^\d]/g, '');
    $('#jumlahAsli').val(rawValue);

    // Ambil ID jenis penerimaan yang sedang aktif
    const idJenis = $('#idJenisPenerimaan').val();
    const activeItem = document.querySelector(`[data-id="${idJenis}"]`);

    if (activeItem) {
        const sisa = parseInt(activeItem.getAttribute('data-sisa') || '0', 10);
        const jumlah = parseInt(rawValue || '0', 10);

        // Validasi jumlah melebihi sisa
        if (jumlah > sisa) {
            Swal.fire({
                icon: 'warning',
                title: 'Jumlah Melebihi Sisa',
                html: `Sisa pembayaran hanya <strong class="text-success">Rp ${sisa.toLocaleString('id-ID')}</strong><br>
                       Nilai yang Anda masukkan <strong class="text-danger">Rp ${jumlah.toLocaleString('id-ID')}</strong>`
            });

            // Reset input jika melebihi
            $(this).val('');
            $('#jumlahAsli').val('');
        }
    }
});



// Fungsi yang akan dipasang ke event listener
function onJumlahInput(e) {
    this.value = formatRupiah(this.value);

    // Optional: update input hidden jika ingin kirim nilai asli (tanpa titik/koma)
    const hiddenInput = document.getElementById('jumlahAsli');
    if (hiddenInput) {
        hiddenInput.value = this.value.replace(/[^\d]/g, '');
    }
}

// Fungsi untuk pasang event listener ke input jumlah
function aktifkanFormatRupiah() {
    const inputJumlah = document.getElementById('jumlahBayar');
    if (inputJumlah) {
        inputJumlah.removeEventListener('input', onJumlahInput); // Hindari double listener
        inputJumlah.addEventListener('input', onJumlahInput);
    }
}

// Fungsi utama saat klik jenis pembayaran
function detailPembayaran(event, id) {


    btnPrintRiwayat = document.getElementById('btnPrintRiwayat');
    btnPrintRiwayat.disabled = false; // Aktifkan tombol print riwayat
    // Reset active dari semua item list
    document.querySelectorAll('#jenisPembayaranList .list-group-item').forEach(function(el) {
        el.classList.remove('active');
        const dataId = el.getAttribute('data-id');
        const span = document.getElementById('jumlahPembayaran_' + dataId);
        if (span) {
            span.classList.remove('text-white');
            span.classList.add('text-primary');
        }
    });

    // Set active untuk item yang dipilih
    const target = event.currentTarget;
    target.classList.add('active');

    const selectedSpan = document.getElementById('jumlahPembayaran_' + id);
    if (selectedSpan) {
        selectedSpan.classList.remove('text-primary');
        selectedSpan.classList.add('text-white');
    }

    // Update judul
    const namaJenis = target.getAttribute('data-jenis') || '';
    const idMapPenerimaan = target.getAttribute('data-idMapPenerimaan') || '';
    document.getElementById('namaJenisPembayaran').textContent = namaJenis ? ' - ' + namaJenis : '';
    document.getElementById('catatan').textContent = namaJenis ? 'Pembayaran Tagihan  - ' + namaJenis : '';
    document.getElementById('namaRiwayatJenisPembayaran').textContent = namaJenis ? ' - ' + namaJenis : '';

    // Set hidden input id jenis
    document.getElementById('idJenisPenerimaan').value = (id !== 'all') ? id : '';
    document.getElementById('idMapPenerimaan').value = (id !== 'all') ? idMapPenerimaan : '';

    const form = document.getElementById('formPembayaran');
    const formWrapper = document.querySelector('.form-wrapper');

    // Cek status lunas
    const lunas = target.getAttribute('data-lunas');

    if (id === 'all' || id === '') {
        // Disable form dan sembunyikan pointer events saat pilih "all" atau kosong
        form.querySelectorAll('input, select, textarea, button[type="submit"]').forEach(el => el.disabled = true);
        formWrapper.style.pointerEvents = 'none';
        formWrapper.style.opacity = '0.6';
    } else {
        // Jika lunas = 1, disable form tapi tetap tampilkan riwayat
        if (lunas === '1') {
            form.querySelectorAll('input, select, textarea, button[type="submit"]').forEach(el => el.disabled = true);
            formWrapper.style.pointerEvents = 'none';
            formWrapper.style.opacity = '0.6';
        } else {
            // Enable form jika belum lunas
            form.querySelectorAll('input, select, textarea, button[type="submit"]').forEach(el => el.disabled = false);
            formWrapper.style.pointerEvents = 'auto';
            formWrapper.style.opacity = '1';
        }

        // **Panggil riwayat pembayaran apapun status lunasnya**
        aktifkanFormatRupiah();
        getRiwayatByJenis(event, id);
    }
}

function getRiwayatByJenis(event, id) {
    var nis = document.querySelector('input[name="nis"]').value;
    var idMapPenerimaan = document.querySelector('input[name="idMapPenerimaan"]').value;
    var idjenis = id;
    $.ajax({
        type: "GET",
        url: "<?= base_url('keuangan/pembayaransiswa/getRiwayatPembayaranByNis') ?>",
        data: {
            nis: nis,
            id_jenis: idjenis,
            idMapPenerimaan: idMapPenerimaan
        },
        dataType: "json",
        success: function(response) {

            $('#tabelRiwayatPembayaran').html(response.data);
        },
        error: function(xhr) {
            alert('Gagal memuat riwayat pembayaran');
        }
    });
}

// Event delegation untuk tombol hapus
$(document).on('click', '.btn-hapus', function(event) {
    event.preventDefault();
    let idJenis = $(this).data('id');
    hapusPembayaran(event, idJenis);
});

function hapusPembayaran(event, idJenis) {
    event.preventDefault();

    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus pembayaran ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('keuangan/pembayaransiswa/hapusPembayaran') ?>',
                type: 'POST',
                data: {
                    id: idJenis
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Berhasil', response.message, 'success');

                        // Ambil id jenis penerimaan saat ini, default ke 'all' jika kosong
                        let idJenisPenerimaan = document.getElementById('idJenisPenerimaan')
                            .value || 'all';

                        // Cari elemen list aktif pada jenis pembayaran
                        let activeListItem = document.querySelector(
                            '#jenisPembayaranList .list-group-item.active');

                        // Buat objek event dummy agar detailPembayaran bisa akses currentTarget untuk styling
                        let dummyEvent = {
                            currentTarget: activeListItem || null
                        };

                        // Ambil ulang NIS
                        const nis = $('.siswa-nis').val();
                        // Panggil ulang seluruh tab pembayaran
                        $('.pembayaran-content').html('Memuat ulang...');
                        $.get('<?= base_url('keuangan/pembayaransiswa/detail/pembayaran') ?>', {
                            nis: nis
                        }, function(res) {
                            $('.pembayaran-content').html(res);
                        });
                    } else {
                        Swal.fire('Authorization', response.message, 'warning');
                    }
                },
                error: function() {
                    Swal.fire('Kesalahan', 'Terjadi kesalahan saat menghapus.', 'error');
                }
            });
        }
    });
}

document.getElementById('btnPrintRiwayat').addEventListener('click', function() {
    const nis = document.querySelector('input[name="nis"]').value;
    const idJenis = document.getElementById('idJenisPenerimaan').value;

    if (!nis) {
        alert('NIS tidak ditemukan!');
        return;
    }

    // URL cetak PDF
    const url =
        `/laporan/keuangan/kartukontrol?nis=${encodeURIComponent(nis)}&id_jenis_pewmbayaran=${encodeURIComponent(idJenis)}`;

    window.open(url, '_blank');
});



$(document).ready(function() {
    $('#formPembayaran').submit(function(e) {
        e.preventDefault();

        // Ambil semua data form termasuk file
        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: '<?= base_url('keuangan/pembayaransiswa/simpanPembayaran') ?>',
            data: formData,
            dataType: 'json',
            processData: false, // penting untuk FormData
            contentType: false, // penting untuk FormData
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });
                    $('#formPembayaran')[0].reset();

                    let idJenis = document.getElementById('idJenisPenerimaan').value || '';
                    let activeItem = document.querySelector(
                        '#jenisPembayaranList .list-group-item.active');

                    detailPembayaran({
                        currentTarget: activeItem
                    }, idJenis);

                    const nis = $('.siswa-nis').val();
                    $('.pembayaran-content').html('Memuat ulang...');
                    $.get('<?= base_url('keuangan/pembayaransiswa/detail/pembayaran') ?>', {
                        nis: nis
                    }, function(res) {
                        $('.pembayaran-content').html(res);
                    });
                } else {
                    let pesan = response.errors ? Object.values(response.errors).join(
                        '\n') : response.message;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: pesan
                    });
                }
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan saat menyimpan.';
                try {
                    let response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        msg = response.message;
                    }
                } catch (e) {
                    if (xhr.responseText) {
                        msg = xhr.responseText;
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: msg
                });
            }
        });
    });


});
</script>