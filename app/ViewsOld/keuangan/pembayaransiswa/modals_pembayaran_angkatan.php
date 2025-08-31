<div class="modal fade" id="modalformSiswaBaru" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="labelModal" aria-hidden="true" data-nis="">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelModal">Kas Siswa / Angkatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-1">
                    <div class="col-md-6">
                        <div class="alert alert-primary p-2" role="alert">
                            <h4 class="alert-heading">Perhatian!</h4>
                            <p class="small">Untuk melakukan pembayaran siswa, silahkan pilih angkatan
                                terlebih
                                dahulu.
                                <br>Jika angkatan belum ada, silahkan buat angkatan baru pada menu <strong>Referensi >
                                    Angkatan</strong>.
                                <br>Jika angkatan sudah ada, silahkan pilih angkatan yang ingin dibayar.
                            </p>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body row g-1 p-2">
                                <div class="col-md-6">
                                    <label for="angkatan_id" class="form-label"><i class="fa fa-check-circle"
                                            aria-hidden="true"></i>
                                        Pilih
                                        Angkatan</label>
                                    <select id="angkatan_id" class="form-select">
                                        <option value="">Pilih Angkatan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="tanggal" class="form-label"><i class="fa fa-check-circle"
                                            aria-hidden="true"></i> Tanggal </label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal"
                                        value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <small>Untuk melakukan pembayaran siswa, silahkan pilih angkatan terlebih dahulu</small>
                                <div class="col-md-12">
                                    <button id="btnSubmitPembayaran" class="btn btn-outline-primary float-end"
                                        type="submit">[ <i class="fa fa-save" aria-hidden="true"></i> Submit ]</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="angkatanSiswaTable"
                        class="table table-sm table-hover table-bordered w-100 table-responsive">
                        <thead>
                            <tr>
                                <th class="text-center">NO</th>
                                <th class="text-center">NIS</th>
                                <th class="text-center">NAMA SISWA</th>
                                <th class="text-center">ANGKATAN</th>
                                <th class="text-center">JENIS PEMBAYARAN</th>
                            </tr>
                        </thead>
                        <tbody id="angkatanSiswaTableBody">
                            <!-- Data akan diisi melalui AJAX -->
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url('js/referensi.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Delegasi event ke seluruh kolom input contenteditable
    document.querySelectorAll('.tableeditcontet[contenteditable="true"]').forEach(cell => {
        cell.addEventListener('input', function(e) {
            const maxSisa = parseInt(this.getAttribute('data-sisa'));
            let inputVal = this.textContent.replace(/[^0-9]/g, ''); // ambil hanya angka
            inputVal = inputVal === '' ? 0 : parseInt(inputVal);

            if (inputVal > maxSisa) {
                alert('Nilai pembayaran tidak boleh melebihi sisa pembayaran: ' + maxSisa);
                this.textContent = maxSisa;
                // Pindahkan kursor ke akhir teks
                placeCaretAtEnd(this);
            }
        });
    });

    function placeCaretAtEnd(el) {
        el.focus();
        if (typeof window.getSelection != "undefined" &&
            typeof document.createRange != "undefined") {
            var range = document.createRange();
            range.selectNodeContents(el);
            range.collapse(false);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
    }
});

function formatRupiah(angka) {
    let numberString = angka.replace(/[^,\d]/g, ''),
        split = numberString.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;
}

// Event: format otomatis saat ketik di kolom tableeditcontet
$(document).on('input', '.tableeditcontet[contenteditable="true"]', function() {
    const maxSisa = parseInt($(this).attr('data-sisa')) || 0;

    // Ambil hanya angka dari isi contenteditable
    let inputValStr = $(this).text().replace(/[^0-9]/g, '');
    let inputVal = inputValStr === '' ? 0 : parseInt(inputValStr);

    if (inputVal > maxSisa) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Nilai pembayaran tidak boleh melebihi sisa pembayaran: ' + maxSisa,
            confirmButtonText: 'Oke'
        }).then(() => {
            // Setelah klik OK, set nilai ke maxSisa dengan format Rupiah
            $(this).text(formatRupiah(maxSisa.toString()));
            placeCaretAtEnd(this);
        });

    } else {
        // Format nilai yang valid dan set ulang teks
        const formatted = formatRupiah(inputVal.toString());
        $(this).text(formatted);
        placeCaretAtEnd(this);
    }
});

function placeCaretAtEnd(el) {
    el.focus();
    if (typeof window.getSelection != "undefined" &&
        typeof document.createRange != "undefined") {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    }
}


$('#angkatan_id').on('change', function() {
    const angkatanId = $(this).val();
    const nis = $(this).data('nis');
    getAngkatanSiswa(angkatanId, nis);
});

function getAngkatanSiswa(angkatanId) {
    $.ajax({
        url: '<?= base_url('keuangan/pembayaransiswa/getAngkatanTableEditContent') ?>',
        type: 'GET',
        data: {
            angkatanId: angkatanId
        },
        dataType: 'JSON',
        success: function(response) {
            // Ganti header dan body tabel
            $('#angkatanSiswaTable thead').html(response.header1 + response.header2);
            $('#angkatanSiswaTable tbody').html(response.body);
            $('#angkatanSiswaTable tfoot').html(response.footer);
        }
    });

}

function kumpulkanDataPembayaran() {
    let pembayaran = [];

    $('.tableeditcontet[contenteditable="true"]').each(function() {
        const nis = $(this).data('nis');
        const jenisId = $(this).data('jenis-id');
        const maxSisa = parseInt($(this).data('sisa')) || 0;
        // const jenisNama = $(this).data('jenis-penerimaan');


        // Ambil angka saja dari isi cell
        let jumlahStr = $(this).text().replace(/[^0-9]/g, '');
        let jumlah = jumlahStr === '' ? 0 : parseInt(jumlahStr);

        if (nis && jumlah > 0 && jumlah <= maxSisa) {
            pembayaran.push({
                nis: nis,
                id_jenis_penerimaan: jenisId,
                jumlah: jumlah,
                tanggal: $('#tanggal').val() // tanggal dari input modal,
                // jenis_nama: jenisNama
            });
        }
    });

    return pembayaran;
}


$('#modalformSiswaBaru button[type="submit"]').on('click', function(e) {
    e.preventDefault();

    const pembayaran = kumpulkanDataPembayaran();

    if (pembayaran.length === 0) {
        Swal.fire('Info', 'Tidak ada data pembayaran yang diinput.', 'info');
        return;
    }

    $.ajax({
        url: '<?= base_url('keuangan/pembayaransiswa/simpanPembayaranAngkatan') ?>',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({
            pembayaran: pembayaran
        }),
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire('Berhasil', response.message, 'success');
                $('#modalformSiswaBaru').modal('hide');
                // reload data tabel atau reset form jika perlu
            } else {
                Swal.fire('Gagal', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Gagal', 'Terjadi kesalahan pada server.', 'error');
        }
    });
});