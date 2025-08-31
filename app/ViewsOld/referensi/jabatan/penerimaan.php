<form id="formInputPengaturan" method="post" action="<?= base_url('referensi/jabatan/simpanPenerimaanJabatan') ?>">
    <input type="hidden" name="id" id="id" value="<?= $id_jabatan ?>"> <!-- id jabatan dari modal -->
    <input type="hidden" name="id_penerimaan" id="id_penerimaan"> <!-- kosong=insert, terisi=update -->

    <div class="row g-3">
        <div class="col-md-4 border-end">
            <!-- Nama jabatan -->
            <div class="mb-2">
                <input type="text" id="jabatan"
                    class="form-control text-center bg-white rounded fw-bold text-uppercase text-primary"
                    value="<?= esc($jabatan) ?>" disabled>
            </div>

            <!-- Input jenis penerimaan -->
            <div class="mb-2">
                <label>Jenis Penerimaan</label><sup class="text-danger">*</sup>
                <input type="text" class="form-control" name="penerimaan" id="penerimaan"
                    placeholder="Contoh: Gaji Pokok" required>
            </div>

            <!-- Input jumlah -->
            <div class="mb-2">
                <label>Jumlah (Rp)</label><sup class="text-danger">*</sup>
                <input type="text" class="form-control text-end" name="jumlah" id="jumlah" placeholder="0" required>
            </div>

            <!-- Button Tambah/Ubah dan Reset -->
            <div class="mt-2">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-save"></i> Tambah
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger">Reset</button>
            </div>
        </div>

        <!-- DataTable -->
        <div class="col-md-8">
            <table class="table table-hover table-sm table-bordered w-100" id="tabelPenerimaan">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:8%">No</th>
                        <th class="text-center">Penerimaan</th>
                        <th class="text-end">Jumlah (Rp)</th>
                        <th class="text-center">Admin</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    disableAutocomplete();
    
    const idJabatan = $('#id').val(); // hidden input jabatan
    $('#id').val(idJabatan); // pastikan selalu terisi

    let penerimaanTable;

    // Init/Reload DataTable
    function initPenerimaan() {
        if ($.fn.DataTable.isDataTable('#tabelPenerimaan')) {
            $('#tabelPenerimaan').DataTable().clear().destroy();
        }

        penerimaanTable = $('#tabelPenerimaan').DataTable({
            paging: false,
            scrollY: '50vh',
            scrollCollapse: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= base_url('referensi/jabatan/ajaxListPenerimaan') ?>",
                type: "GET",
                data: {
                    id: $('#id').val()
                },
                dataSrc: 'data'
            },
            columns: [{
                    data: null,
                    className: 'text-center',
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'JENIS_PENERIMAAN',
                    className: 'text-left'
                },
                {
                    data: 'JUMLAH',
                    className: 'text-end',
                    render: data => new Intl.NumberFormat('id-ID').format(data)
                },
                {
                    data: 'NAMA_PENGGUNA',
                    className: 'text-left'
                },
                {
                    data: null,
                    className: 'text-center',
                    render: data => `
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-info editBtn"
                            data-id="${data.ID}"
                            data-jenis="${data.JENIS_PENERIMAAN}"
                            data-jumlah="${data.JUMLAH}">✏️</button>
                        <button type="button" class="btn btn-sm btn-outline-danger deleteBtn"
                            data-id="${data.ID}">🗑️</button>
                    </div>`
                }
            ]
        });
    }

    initPenerimaan();

    // Format input jumlah saat mengetik
    $('#jumlah').on('input', function() {
        let val = $(this).val().replace(/\./g, '').replace(/[^0-9]/g, '');
        if (val) val = parseInt(val).toLocaleString('id-ID');
        $(this).val(val);
    });

    // Reset form kecuali id & id_penerimaan
    $('#formInputPengaturan .btn-outline-danger').on('click', function() {
        $('#formInputPengaturan')[0].reset();
        $('#id').val(idJabatan);
        $('#id_penerimaan').val('');
        $('#formInputPengaturan button[type="submit"]').html('<i class="fa fa-save"></i> Tambah');
    });

    // Klik Edit → tampilkan di form
    $(document).on('click', '.editBtn', function() {
        const idP = $(this).data('id');
        const jenis = $(this).data('jenis');
        const jumlah = $(this).data('jumlah');

        $('#penerimaan').val(jenis);
        $('#jumlah').val(new Intl.NumberFormat('id-ID').format(jumlah));
        $('#id_penerimaan').val(idP);
        $('#formInputPengaturan button[type="submit"]').html('<i class="fa fa-save"></i> Ubah');
        $('#penerimaan').focus();
    });

    // Submit form (insert/update)
    $('#formInputPengaturan').off('submit').on('submit', function(e) {
        e.preventDefault();
        const url = $('#id_penerimaan').val() ?
            "<?= base_url('referensi/jabatan/updatePenerimaan') ?>" :
            $(this).attr('action');

        let jumlah = parseInt($('#jumlah').val().replace(/\./g, '')) || 0;
        let formData = $(this).serializeArray().map(f => f.name === 'jumlah' ? {
            ...f,
            value: jumlah
        } : f);

        $.post(url, $.param(formData), function(res) {
            if (res.status === 'saved' || res.status === 'updated') {
                initPenerimaan(); // reload table
                $('#formInputPengaturan')[0].reset();
                $('#id').val(idJabatan);
                $('#id_penerimaan').val('');
                $('#formInputPengaturan button[type="submit"]').html(
                    '<i class="fa fa-save"></i> Tambah');
                showToast('Sukses', res.message, 'success', 1500);
            } else {
                showToast('Gagal', res.message, 'danger', 1500);
            }
        });
    });

    // Hapus penerimaan
    $(document).on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus data?',
            text: "Data penerimaan ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                $.get("<?= base_url('referensi/jabatan/deletePenerimaan') ?>/" + id, function(
                    res) {
                    if (res.status === 'deleted') {
                        initPenerimaan();
                        showToast('Terhapus', res.message, 'success', 1500);
                    }
                });
            }
        });
    });

});
</script>