<div class="row g-1">
    <div class="col-6">
        <div class="card card shadow-none bg-transparent border border-primary mb-3">
            <div class="card-header  p-2">
                <h5 class="card-title pt-1">
                    <i class="fa fa-rotate-right" aria-hidden="true"></i> Form Maping Jenis Pembayaran (Tagihan)
                </h5>
            </div>

            <div class="card-body row">
                <div class="col-6 form-group">
                    <select id="jenisPembayaranSelect" class="form-select">
                        <option value="">[ Pilih ]</option>
                        <?php foreach ($jenis_penerimaan as $jenis): ?>
                        <option value="<?= esc($jenis['ID']) ?>">[<?= esc($NAMA_SEKOLAH) ?>] -
                            <?= esc($jenis['JENIS_PENERIMAAN']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Silakan pilih jenis pembayaran.
                    </div>
                </div>
                <div class="col-6 form-group">
                    <button class="btn btn-outline-primary" onclick="mapJenisPembayaran()">[ <i class="fa fa-forward"
                            aria-hidden="true"></i> Proses ]</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card card shadow-none bg-transparent border border-primary mb-3">
            <div class="card-header p-2">
                <h5 class="card-title pt-1">
                    <i class="fa fa-list" aria-hidden="true"></i> Jenis Pembayaran (Tagihan)
                </h5>
            </div>
            <?php if (!empty($mapped)): ?>
            <small><b>Penting!</b> untuk mengubah jumlah tenor klik 2x pada kolom Tenor, lalu tekan Enter atau Klik
                tombol Hitung Ulang.</small>
            <table class="table table-bordered table-sm table-striped small table-hover">
                <thead>
                    <tr>
                        <th class="text-center bg-secondary text-white">Jenis Pembayaran</th>
                        <th class="text-center bg-secondary text-white">Pendidikan</th>
                        <th class="text-center bg-secondary text-white">Tenor (x)</th>
                        <th class="text-center bg-secondary text-white">Jumlah<br>Master (Rp)</th>
                        <th class="text-center bg-secondary text-white">Jumlah<br>Kewajiban (Rp)</th>
                        <th class="text-center bg-secondary text-white">Dibayar<br>(Rp)</th>
                        <th class="text-center bg-secondary text-white">Tunggakan<br>(Rp)</th>
                        <th class="text-center bg-secondary text-white">Lunas</th>
                        <th class="text-center bg-secondary text-white">Status</th>
                        <th class="text-center bg-secondary text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
        $total_jumlah = 0;
        $total_telah_dibayar = 0;
        $total_sisa_dibayar = 0;
        ?>

                    <?php foreach ($mapped as $jenis): ?>
                    <?php
            $id = $jenis['ID'];
            $total_jumlah += $jenis['JUMLAH'];
            $total_telah_dibayar += $jenis['TELAH_DIBAYAR'];
            $total_sisa_dibayar += $jenis['SISA_DIBAYAR'];
            ?>
                    <tr>
                        <td><?= esc($jenis['JENIS_PENERIMAAN']) ?></td>
                        <td><?= esc($jenis['NAMA_SEKOLAH']) ?></td>

                        <!-- Kolom Tenor dengan data-* -->
                        <td class="text-center editable-tenor" data-id="<?= $id ?>"
                            data-original="<?= esc($jenis['TENOR']) ?>"
                            data-jumlah-master="<?= $jenis['JUMLAH_MASTER'] ?>"
                            data-telah-dibayar="<?= $jenis['TELAH_DIBAYAR'] ?>" style="cursor: pointer;">
                            <?= esc($jenis['TENOR']) ?>
                        </td>

                        <!-- Kolom Jumlah Master -->
                        <td class="text-end jumlah-master"><?= number_format($jenis['JUMLAH_MASTER'], 0, ',', '.') ?>
                        </td>

                        <!-- Kolom Jumlah Total -->
                        <td class="text-end jumlah-total"><?= number_format($jenis['JUMLAH'], 0, ',', '.') ?></td>

                        <!-- Kolom Telah Dibayar -->
                        <td class="text-end text-success telah-dibayar">
                            <?= number_format($jenis['TELAH_DIBAYAR'], 0, ',', '.') ?></td>

                        <!-- Kolom Sisa Dibayar -->
                        <td class="text-end text-danger sisa-dibayar">
                            <?= number_format($jenis['SISA_DIBAYAR'], 0, ',', '.') ?></td>

                        <!-- Kolom Lunas -->
                        <td class="text-center">
                            <?php if ($jenis['LUNAS'] == 1): ?>
                            <span class="badge bg-label-primary">Lunas</span>
                            <?php else: ?>
                            <span class="badge bg-label-warning">Belum</span>
                            <?php endif; ?>
                        </td>

                        <!-- Kolom Status -->
                        <td class="text-center">
                            <?php if ($jenis['STATUS'] == 1): ?>
                            <span class="badge bg-label-success">Aktif</span>
                            <?php else: ?>
                            <span class="badge bg-label-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>

                        <!-- Kolom Aksi -->
                        <td class="text-center">
                            <div class="btn-group" role="group" aria-label="Button group">
                                <?php if ($jenis['STATUS'] == 1): ?>
                                <?php if ($jenis['TELAH_DIBAYAR'] > 0): ?>
                                <span class="badge bg-label-secondary">Kunci</span>
                                <?php else: ?>
                                <button type="button" class="btn btn-xs btn-outline-danger"
                                    onclick="batalMapJenisPembayaran(event, '<?= $id ?>')">
                                    Batalkan
                                </button>
                                <?php endif; ?>
                                <?php else: ?>
                                <button type="button" class="btn btn-xs btn-outline-success"
                                    onclick="aktifkanMapJenisPembayaran(event, '<?= $id ?>')">
                                    Aktifkan
                                </button>
                                <?php endif; ?>

                                <button type="button" class="btn btn-xs btn-outline-success"
                                    onclick="hitungulangMapJenisPembayaran(event, '<?= $id ?>')">
                                    Hitung Ulang
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <!-- Baris Total -->
                    <tr>
                        <th colspan="4" class="text-center">Total</th>
                        <th class="text-end"><?= number_format($total_jumlah, 0, ',', '.') ?></th>
                        <th class="text-end text-success"><?= number_format($total_telah_dibayar, 0, ',', '.') ?></th>
                        <th class="text-end text-danger"><?= number_format($total_sisa_dibayar, 0, ',', '.') ?></th>
                        <th colspan="3"></th>
                    </tr>
                </tbody>
            </table>

            <?php else: ?>
            <div class="p-2 text-muted text-center">
                <i class="bi bi-exclamation-diamond-fill text-warning"></i>
                Tidak ada jenis pembayaran ditemukan.
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>




<script>
$(document).ready(function() {
    $('#jenisPembayaranSelect').select2({
        theme: 'bootstrap-5',
        placeholder: '[ Pilih Jenis Pembayaran ]',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalformSiswaBaru'),
    });
    $('#modalformSiswaBaru').on('shown.bs.modal', function() {
        $('#jenisPembayaranSelect').select2({
            placeholder: "[ Pilih ]",
            theme: "bootstrap-4", // tema Bootstrap 4 cocok juga untuk Bootstrap 5
            allowClear: true,
            dropdownParent: $('#modalformSiswaBaru'),
            width: '100%'
        });
    });


});

function mapJenisPembayaran() {
    const select = document.getElementById('jenisPembayaranSelect');
    const selectedValue = select.value;
    const nis = <?= json_encode($nis) ?>;

    if (!selectedValue) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Silakan pilih jenis pembayaran terlebih dahulu.'
        });
        select.classList.add('is-invalid');
        return;
    }

    fetch('/kesiswaan/siswa/mapJenisPembayaran', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                nis: nis,
                jenis_penerimaan_id: selectedValue
            })
        })
        .then(async res => {
            const contentType = res.headers.get("content-type");
            let data;
            if (contentType && contentType.indexOf("application/json") !== -1) {
                data = await res.json();
            } else {
                throw new Error('Response bukan JSON');
            }
            if (!res.ok) {
                throw new Error(data.message || 'Terjadi kesalahan server');
            }
            return data;
        })
        .then(response => {
            if (response.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false,
                }).then(() => {
                    const nis = <?= json_encode($nis) ?>;
                    loadTabContent('<?= base_url('kesiswaan/siswa/detail/pembayaran') ?>',
                        '.pembayaran-content', nis);
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    html: `<strong>Kode:</strong> ${response.code || '-'}<br><strong>Pesan:</strong> ${response.message}`
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.message || 'Terjadi kesalahan tidak terduga'
            });
        });
}

function batalMapJenisPembayaran(event, id) {
    event.preventDefault();

    Swal.fire({
        title: 'Yakin ingin membatalkan map jenis pembayaran ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, batalkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('kesiswaan/siswa/batalMapJenisPembayaran') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        id: id
                    })
                })
                .then(async res => {
                    if (!res.ok) {
                        // coba ambil pesan error dari response JSON jika ada
                        let errorData;
                        try {
                            errorData = await res.json();
                        } catch {
                            errorData = null;
                        }
                        throw new Error(errorData?.message || 'Terjadi kesalahan server');
                    }
                    return res.json();
                })
                .then(response => {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => {
                            const nis = <?= json_encode($nis) ?>;
                            loadTabContent('<?= base_url('kesiswaan/siswa/detail/pembayaran') ?>',
                                '.pembayaran-content', nis);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal membatalkan map jenis pembayaran'
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message || 'Terjadi kesalahan tidak terduga'
                    });
                });
        }
    });
}

function aktifkanMapJenisPembayaran(event, id) {
    event.preventDefault();

    Swal.fire({
        title: 'Yakin ingin mengaktifkan map jenis pembayaran ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, aktifkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('kesiswaan/siswa/aktifkanMapJenisPembayaran') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        id: id
                    })
                })
                .then(async res => {
                    if (!res.ok) {
                        let errorData;
                        try {
                            errorData = await res.json();
                        } catch {
                            errorData = null;
                        }
                        throw new Error(errorData?.message || 'Terjadi kesalahan server');
                    }
                    return res.json();
                })
                .then(response => {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => {
                            const nis = <?= json_encode($nis) ?>;
                            loadTabContent('<?= base_url('kesiswaan/siswa/detail/pembayaran') ?>',
                                '.pembayaran-content', nis);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal mengaktifkan map jenis pembayaran'
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message || 'Terjadi kesalahan tidak terduga'
                    });
                });
        }
    });
}
</script>

<script>
document.querySelectorAll('.editable-tenor').forEach(cell => {
    cell.addEventListener('dblclick', function() {
        if (this.querySelector('input')) return;

        let original = this.dataset.original;
        let id = this.dataset.id;
        let jumlahMaster = parseFloat(this.dataset.jumlahMaster);
        let telahDibayar = parseFloat(this.dataset.telahDibayar);

        let input = document.createElement('input');
        input.type = 'number';
        input.value = original;
        input.classList.add('form-control', 'form-control-sm');

        this.innerHTML = '';
        this.appendChild(input);
        input.focus();

        let isConfirming = false;

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                let newTenor = input.value.trim();
                if (newTenor === original) {
                    Swal.fire('Info', 'Nilai tenor tidak berubah.', 'info');
                    cell.innerHTML = original;
                    return;
                }

                let newJumlah = newTenor * jumlahMaster;
                let newSisa = newJumlah - telahDibayar;

                isConfirming = true;
                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ubah',
                    cancelButtonText: 'Batal',
                    html: `
                        <p>Ubah tenor dari <b>${original}</b> menjadi <b>${newTenor}</b></p>
                        <p>Jumlah Master: <b>Rp ${jumlahMaster.toLocaleString('id-ID')}</b></p>
                        <p>Jumlah baru: <b>Rp ${newJumlah.toLocaleString('id-ID')}</b></p>
                        <p>Tunggakan baru: <b>Rp ${newSisa.toLocaleString('id-ID')}</b> 
                        (Jumlah baru - Telah Dibayar Rp ${telahDibayar.toLocaleString('id-ID')})</p>
                    `
                }).then(result => {
                    isConfirming = false;
                    if (result.isConfirmed) {
                        updateTenorKeServer(id, newTenor, newJumlah, newSisa, cell);
                    } else {
                        cell.innerHTML = original;
                    }
                });
            }
        });

        input.addEventListener('blur', function() {
            if (!isConfirming) {
                cell.innerHTML = input.value.trim();
            }
        });
    });
});

function updateTenorKeServer(id, newTenor, newJumlah, newSisa, cell) {
    fetch("<?= base_url('kesiswaan/siswa/detail/updatetenor') ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "<?= csrf_hash() ?>"
            },
            body: JSON.stringify({
                id: id,
                tenor: newTenor,
                jumlah: newJumlah,
                sisa_dibayar: newSisa
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                cell.dataset.original = newTenor;
                cell.innerHTML = newTenor;

                // Update nilai di tabel
                let tr = cell.closest('tr');
                tr.querySelector('.jumlah-total').textContent = newJumlah.toLocaleString('id-ID');
                tr.querySelector('.sisa-dibayar').textContent = newSisa.toLocaleString('id-ID');

                Swal.fire('Berhasil', 'Tenor dan perhitungan berhasil diperbarui!', 'success');
            } else {
                Swal.fire('Gagal', data.message || 'Gagal update tenor.', 'error');
                cell.innerHTML = cell.dataset.original;
            }
        })
        .catch(err => {
            Swal.fire('Error', err.message || 'Terjadi kesalahan koneksi.', 'error');
            cell.innerHTML = cell.dataset.original;
        });
}

// Fungsi hitung ulang juga pakai data-* supaya aman
function hitungulangMapJenisPembayaran(event, id) {
    event.preventDefault();

    let cellTenor = document.querySelector(`.editable-tenor[data-id="${id}"]`);
    if (!cellTenor) {
        Swal.fire('Error', 'Data tidak ditemukan.', 'error');
        return;
    }

    let original = cellTenor.dataset.original;
    let tenor = parseFloat(cellTenor.textContent.trim());
    let jumlahMaster = parseFloat(cellTenor.dataset.jumlahMaster);
    let telahDibayar = parseFloat(cellTenor.dataset.telahDibayar);

    let newJumlah = tenor * jumlahMaster;
    let newSisa = newJumlah - telahDibayar;

    if (tenor === parseFloat(original)) {
        Swal.fire('Info', 'Nilai tenor tidak berubah.', 'info');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Perubahan',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal',
        html: `
            <p>Ubah tenor dari <b>${original}</b> menjadi <b>${tenor}</b></p>
            <p>Jumlah Master: <b>Rp ${jumlahMaster.toLocaleString('id-ID')}</b></p>
            <p>Jumlah baru: <b>Rp ${newJumlah.toLocaleString('id-ID')}</b></p>
            <p>Tunggakan baru: <b>Rp ${newSisa.toLocaleString('id-ID')}</b> 
            (Jumlah baru - Telah Dibayar Rp ${telahDibayar.toLocaleString('id-ID')})</p>
        `
    }).then(result => {
        if (result.isConfirmed) {
            updateTenorKeServer(id, tenor, newJumlah, newSisa, cellTenor);
        } else {
            cellTenor.textContent = original;
        }
    });
}
</script>