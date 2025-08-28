<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<style>
.list-modul-item {
    padding: 0.2rem 0.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}


.list-modul-item button {
    padding: 0.15rem 0.5rem;
    font-size: 0.8rem;
    white-space: nowrap;
}

.list-modul-item span {
    flex-grow: 1;
    text-align: left;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.list-group-item.dragging {
    cursor: grabbing !important;
    opacity: 0.7;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    background-color: #e9ecef;
    user-select: none;
}

.list-group-item:hover,
.list-modul-item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    background-color: #f8f9fa;
}

#groupModulDropzone.border-primary.bg-light {
    background-color: #e7f1ff !important;
    border-color: #0d6efd !important;
}

#modulList li.list-group-item {
    cursor: grab;
}

#modulList li.list-group-item.dragging {
    cursor: grabbing !important;
    opacity: 0.7;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    background-color: #e9ecef;
    user-select: none;
}
</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <form id="profilForm" enctype="multipart/form-data">

        <div class="alert alert-warning">
            <strong>Perhatian!</strong> Untuk melakukan managemen modul, terlebih dahulu pilih group modul yang akan di
            Group modul lalu klik dan tahan list modul pada kotak kiri dan tarik lalu lepas pada kotak Kanan.
        </div>

        <div class="row">

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h6>Daftar Modul</h6>
                        <input type="search" name="searchModul" class="form-control mb-2" placeholder="Cari Modul"
                            id="searchModul">
                        <ul class="list-group" id="modulList">
                            <?php foreach ($moduls as $modul): ?>
                            <li class="list-group-item" draggable="true" data-id="<?= $modul['ID'] ?>">
                                <i class="fa fa-arrow-circle-right text-primary" aria-hidden="true"></i>
                                <?= esc($modul['MODUL']) ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>

                    </div>
                </div>
            </div>

            <!-- Kanan -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h6>Group Modul</h6>
                        <div class="form-group">
                            <select id="groupSelect" class="form-control select2 w-100 mb-3">
                                <option value="">[ Pilih Group Modul ]</option>
                                <?php foreach ($groups as $group): ?>
                                <option value="<?= $group['ID'] ?>"><?= esc($group['GROUP_MODUL']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small for="my-select">Pilih Group Modul</small>
                        </div>
                        <hr>
                        <div class="container text-center">
                            <div class="row">
                                <div id="groupModulDropzone" class="col border border-primary p-3"
                                    style="min-height: 100px;">

                                    <p class="text-muted"> <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
                                        Tarik dan lepas modul di sini</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let draggedModulId = null;

$(document).ready(function() {
    disableAutocomplete();

    // Inisialisasi Select2
    $('#groupSelect').select2({
        theme: 'bootstrap-5',
        placeholder: '[ Pilih Group Modul ]',
        allowClear: true,
        width: '100%'
    });

    // Saat group modul berubah
    $('#groupSelect').on('change', function() {
        let groupId = $(this).val();

        if (!groupId) {
            $('#modulList').empty();
            $('#groupModulDropzone').html(
                '<p class="text-muted"><i class="fa fa-arrow-circle-o-right"></i> Tarik dan lepas modul di sini</p>'
            );
            return;
        }

        // Disable dropdown saat fetch
        $('#groupSelect').prop('disabled', true);
        $('#modulList').empty();
        $('#groupModulDropzone').html('<p class="text-muted">Memuat modul...</p>');

        fetch(`<?= base_url('pengaturan/managemen-modul/mappedModul') ?>/${groupId}`)
            .then(res => res.json())
            .then(mappedModules => {
                const allModules = <?= json_encode($moduls) ?>;
                const mappedIds = mappedModules.map(m => m.ID);

                // Kosongkan list kiri dan kanan
                $('#modulList').empty();
                $('#groupModulDropzone').empty();

                // Tampilkan modul yang sudah dimapping (kanan) dengan tombol status
                if (mappedModules.length === 0) {
                    $('#groupModulDropzone').html(
                        '<p class="text-muted"><i class="fa fa-arrow-circle-o-right"></i> Belum ada modul dimapping</p>'
                    );
                } else {
                    mappedModules.forEach(mod => {
                        let btnClass = mod.STATUS == 1 ? 'btn-outline-success' :
                            'btn-outline-danger';
                        let btnText = mod.STATUS == 1 ? 'Non Aktif' : 'Aktif';

                        $('#groupModulDropzone').append(`
                            <div class="alert alert-info list-modul-item" data-mapping-id="${mod.mapping_id}">
                                <span class="text-left">${mod.MODUL}</span>
                                <button class="btn btn-sm ${btnClass} toggle-status-btn" data-mapping-id="${mod.mapping_id}" data-status="${mod.STATUS}" style="padding: 0.15rem 0.5rem; font-size: 0.8rem; white-space: nowrap;">
                                    ${btnText}
                                </button>
                            </div>
                        `);

                    });
                }

                // Tampilkan modul kiri yang belum dimapping
                allModules.forEach(mod => {
                    if (!mappedIds.includes(mod.ID)) {
                        $('#modulList').append(
                            `<li class="list-group-item" draggable="true" data-id="${mod.ID}"><i class="fa fa-arrow-circle-right text-primary" aria-hidden="true"></i> ${mod.MODUL}</li>`
                        );
                    }
                });

                addDragEvents();
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data modul mapped',
                });
            })
            .finally(() => {
                $('#groupSelect').prop('disabled', false);
            });
    });

    // Tambahkan event dragstart, dragend pada modul list kiri
    function addDragEvents() {
        document.querySelectorAll('#modulList .list-group-item').forEach(item => {
            item.setAttribute('draggable', 'true');
            item.addEventListener('dragstart', e => {
                draggedModulId = e.target.getAttribute('data-id');
                e.target.classList.add('dragging');

                // drag image kosong supaya kursor drag bisa custom
                var img = new Image();
                img.src =
                    'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAgMBAOXoJMcAAAAASUVORK5CYII=';
                e.dataTransfer.setDragImage(img, 0, 0);
            });
            item.addEventListener('dragend', e => {
                e.target.classList.remove('dragging');
                draggedModulId = null;
            });
        });
    }

    // Event delegation toggle status
    $('#groupModulDropzone').off('click', '.toggle-status-btn').on('click', '.toggle-status-btn', function(e) {
        e.preventDefault();

        let btn = $(this);
        let mappingId = btn.data('mapping-id');
        let currentStatus = parseInt(btn.data('status'));
        let newStatus = currentStatus === 1 ? 0 : 1;

        // Disable tombol saat request
        btn.prop('disabled', true);

        fetch('<?= base_url("pengaturan/managemen-modul/updateStatusMapping") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    mapping_id: mappingId,
                    status: newStatus
                })
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Status modul berhasil diubah',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    btn.data('status', newStatus);
                    if (newStatus === 1) {
                        btn.removeClass('btn-outline-success').addClass('btn-outline-danger').text(
                            'Non Aktif');
                    } else {
                        btn.removeClass('btn-outline-danger').addClass('btn-outline-success').text(
                            'Aktif');
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: resp.message || 'Gagal mengubah status'
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan server.'
                });
            })
            .finally(() => {
                btn.prop('disabled', false);
            });
    });

    // Filter search modul kiri
    $('#searchModul').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();

        $('#modulList li.list-group-item').each(function() {
            const modulName = $(this).text().toLowerCase();
            $(this).toggle(modulName.indexOf(searchTerm) !== -1);
        });
    });

    // Drag & drop handling untuk dropzone kanan
    const dropzone = document.getElementById('groupModulDropzone');
    dropzone.addEventListener('dragover', e => e.preventDefault());

    dropzone.addEventListener('dragenter', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-primary', 'bg-light');
    });

    dropzone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-primary', 'bg-light');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-primary', 'bg-light');

        if (draggedModulId) {
            let groupId = $('#groupSelect').val();
            if (!groupId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Group Modul',
                    text: 'Silakan pilih group modul terlebih dahulu sebelum memapping modul.'
                });
                return;
            }

            fetch('<?= base_url("pengaturan/managemen-modul/mapModul") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        modul_id: draggedModulId,
                        group_id: groupId
                    })
                })
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Modul berhasil dimapping ke group.'
                        });
                        $('#groupSelect').trigger('change');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Modul gagal dimapping.'
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan server.'
                    });
                });
        }
    });

    // Pasang event dragstart awal (untuk modul yang sudah ada di awal load)
    addDragEvents();

});
</script>


<?= $this->endSection() ?>