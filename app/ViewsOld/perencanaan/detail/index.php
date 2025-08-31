<?= $this->extend('templates/default') ?>
<?= $this->section('content') ?>
<style>
#programTable tbody tr.focused {
    background-color: #FFE082 !important;
    /* kuning highlight */
}

#programTable tbody tr {
    cursor: pointer;
}
</style>
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header row">
                    <div class="col-lg-4">
                        <div class="input-group small">
                            <select name="filterTahun" id="filterTahun" class="form-control float-left"
                                required="required">
                                <option value="">[ Pilih ]</option>
                            </select>
                            <button type="button" class="btn btn-outline-info" id="btn-filter-tahun-perencanaan"><i
                                    class="fa fa-search" aria-hidden="true"></i> Lihat</button>

                        </div>

                    </div>
                    <div class="col-lg-8">
                        <button class="btn btn-primary text-white float-end" onclick="addProgram()"><i
                                class="fa fa-plus-circle"></i>
                            Tambah
                            Program</button>
                    </div>

                </div>
                <div class="card-body">
                    <table id="programTable" class="table table-sm table-bordered w-100">
                        <thead>
                            <tr style="background-color: #F97A00;">
                                <th class="text-center text-white">#</th>
                                <th class="text-center text-white">Program</th>
                                <th class="text-center text-white">Anggaran</th>
                                <th class="text-center text-white">Tahun</th>
                                <th class="text-center text-white">Status</th>
                                <th class="text-center text-white">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="viewmodal"></div>

<script>
$(document).ready(function() {
    // --- isi dropdown 5 tahun terakhir ---
    let tahunSekarang = new Date().getFullYear();
    for (let i = 0; i < 5; i++) {
        let t = tahunSekarang - i;
        $("#filterTahun").append(`<option value="${t}">${t}</option>`);
    }

    // --- inisialisasi DataTable ---
    var table = $('#programTable').DataTable({
        ajax: function(d, callback) {
            const tahun = $('#filterTahun').val();

            // jika belum pilih tahun: kembalikan tabel kosong TANPA request server
            if (!tahun) {
                callback({
                    data: []
                });
                return;
            }

            // tampilkan preloader
            $("#preloader").show();

            // request normal ke server
            $.ajax({
                url: "<?= base_url('perencanaan/detail/ajaxList') ?>",
                data: {
                    tahun: tahun
                },
                dataType: "json",
                success: function(json) {
                    callback(json);
                },
                error: function() {
                    callback({
                        data: []
                    });
                },
                complete: function() {
                    $("#preloader").fadeOut();
                }
            });
        },
        columns: [{
                className: 'text-center',
                orderable: false,
                data: null,
                render: function(data) {
                    const lvl = data.level?.toLowerCase().trim();
                    let cls = '';
                    if (lvl === 'program') cls = 'expand-program';
                    else if (lvl === 'kegiatan') cls = 'expand-kegiatan';
                    else if (lvl === 'subkegiatan') cls = 'expand-subkegiatan';
                    return cls ?
                        `<i class="fa fa-plus-circle text-info ${cls}" style="cursor:pointer" 
                                data-id="${data.id}" data-level="${lvl}"></i>` :
                        '';
                }
            },
            {
                data: 'nama',
                render: function(data, type, row) {
                    const lvl = row.level?.toLowerCase().trim();
                    let nameStyle = '',
                        namePrefix = '';
                    switch (lvl) {
                        case 'program':
                            nameStyle = 'bg-light-primary p-1 rounded';
                            namePrefix = 'PROGRAM';
                            break;
                        case 'kegiatan':
                            nameStyle = 'bg-light-warning p-1 rounded';
                            namePrefix = 'KEGIATAN';
                            break;
                        case 'subkegiatan':
                            nameStyle = 'bg-light-info p-1 rounded';
                            namePrefix = 'SUB KEGIATAN';
                            break;
                        case 'belanja':
                            nameStyle = 'bg-light-success p-1 rounded';
                            namePrefix = 'BELANJA';
                            break;
                    }
                    return `<span class="${nameStyle}">${namePrefix}: ${data}</span>`;
                }
            },
            {
                data: 'anggaran',
                className: 'text-end',
                render: d => d ? new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(d) : '-'
            },
            {
                data: 'tahun',
                className: 'text-center'
            },
            {
                data: 'status',
                className: 'text-center',
                render: s =>
                    s == 1 ?
                    '<span class="badge bg-success">Aktif</span>' :
                    '<span class="badge bg-danger">Tidak Aktif</span>'
            },
            {
                data: 'level',
                className: 'text-center',
                orderable: false,
                render: function(lvl, type, row) {
                    const level = lvl?.toLowerCase().trim();
                    let html = '';
                    if (level === 'program') {
                        html = `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-success" onclick="editProgram(${row.id})">Edit</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deteleProgram(${row.id})">Hapus</button>
                        </div>
                        <button class="btn btn-sm btn-primary text-white" onclick="addKegiatan('${row.id}','${row.nama}')">
                            <i class="fa fa-plus-circle"></i> Kegiatan
                        </button>`;
                    } else if (level === 'kegiatan') {
                        html = `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-success" onclick="editKegiatan('${row.id_kegiatan}','${row.parent}')">Edit</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deteleKegiatan(${row.id})">Hapus</button>
                        </div>
                        <button class="btn btn-sm btn-primary text-white" onclick="addSubkegiatan(${row.id},'${row.nama}')">
                            <i class="fa fa-plus-circle"></i> Sub
                        </button>`;
                    } else if (level === 'subkegiatan') {
                        html = `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" onclick="editSubkegiatan(${row.id},${row.id_kegiatan})">Edit</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteSubkegiatan(${row.id})">Hapus</button>
                        </div>
                        <button class="btn btn-sm btn-primary text-white" onclick="addBelanja(${row.id},'${row.nama}')">
                            <i class="fa fa-plus-circle"></i> Belanja
                        </button>`;
                    } else if (level === 'belanja') {
                        html = `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" onclick="editBelanja(${row.id},${row.parent})">Edit</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteBelanja(${row.id})">Hapus</button>
                        </div>`;
                    }
                    return html;
                }
            }
        ],
        createdRow: function(row, data) {
            const lvl = data.level?.toLowerCase().trim();
            if (lvl) $(row).addClass(`level-${lvl}`);
        }
    });

    // Filter perencaan
    $(".btn-outline-info").on("click", function() {
        let tahun = $("#filterTahun").val();

        if (!tahun) {
            showToast('Peringatan', 'Silakan pilih tahun terlebih dahulu!', 'danger');
            table.clear().draw(); // kosongkan tabel tanpa request server
            return;
        }

        table.ajax.reload(); // reload tabel kalau tahun valid
    });



    // Tampilkan preloader sebelum request (ID= #preloader; ada pada di direktori view/templates/default.php)
    table.on('preXhr.dt', function() {
        $("#preloader").show();
    });

    // Sembunyikan preloader setelah data selesai (ID= #preloader; ada pada di direktori view/templates/default.php)
    table.on('xhr.dt', function() {
        $("#preloader").fadeOut();
    });


    // Highlight row (semua level)
    $(document).on('click', '#programTable tbody tr', function(e) {
        e.stopPropagation(); // jangan trigger parent klik

        // Hapus highlight di semua baris (parent & child)
        $('#programTable tbody tr').removeClass('focused');

        // Tambahkan highlight ke row yang diklik
        $(this).addClass('focused');

        // Scroll ke tengah viewport
        $(this)[0].scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    });



    function reloadChildTable(trParent, parentId, level) {
        fetch(`<?= base_url('perencanaan/detail/ajaxChildren') ?>?parent_id=${parentId}&level=${level}`)
            .then(res => res.json())
            .then(res => {
                const html = generateChildTable(res.data);
                if (level === 'kegiatan') {
                    $(trParent).next('.child-subkegiatan').remove();
                    $(trParent).after(`<tr class="child-subkegiatan"><td colspan="5">${html}</td></tr>`);
                } else if (level === 'subkegiatan') {
                    $(trParent).next('.child-belanja').remove();
                    $(trParent).after(`<tr class="child-belanja"><td colspan="5">${html}</td></tr>`);
                }
                // tetap highlight row parent
                $(trParent).addClass('focused');
            });
    }


    // Fungsi generate tabel child (kegiatan/detail/belanja)
    function generateChildTable(children) {
        // mengubah background head berdasarkan level
        // ambil nilai level
        var lvl = children[0].level?.toLowerCase().trim();

        var bg = '';
        if (lvl === 'kegiatan') {
            bg = '#254100ff';
            title = 'Kegiatan';
        } else if (lvl === 'subkegiatan') {
            bg = '#0d5c03ff';
            title = 'Sub Kegiatan';
        } else if (lvl === 'belanja') {
            bg = '#5e9702ff';
            title = 'Belanja';
        }

        if (!children.length)
            return `<tr><td colspan="5" class="text-center text-muted">Tidak Ada Data</td></tr>`;
        let html = `<table class="table table-sm table-bordered w-100"><thead><tr style="background-color: ${bg};">
            <th class="text-center text-white">#</th>
            <th class="text-center text-white">${title}</th>
            <th class="text-center text-white">Anggaran</th>
            <th class="text-center text-white">Status</th>
            <th class="text-center text-white">Aksi</th>
        </tr></thead><tbody>`;
        children.forEach(c => {
            const lvl = c.level?.toLowerCase().trim();
            let cls = '';
            if (lvl === 'kegiatan') cls = 'expand-kegiatan';
            else if (lvl === 'subkegiatan') cls = 'expand-subkegiatan';
            let aksi = '';
            if (lvl === 'kegiatan') {
                aksi =
                    `<div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-success" onclick="editKegiatan('${c.id}','${c.parent}')">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deteleKegiatan(${c.id})">Hapus</button>
                </div>
                <button class="btn btn-sm btn-primary text-white ml-2" onclick="addSubkegiatan(${c.id},'${c.nama}')"><i class="fa fa-plus-circle"></i> Sub Kegiatan</button>`;
                label = 'KEGIATAN';
            } else if (lvl === 'subkegiatan') {
                aksi =
                    `<div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="editSubkegiatan(${c.id},${c.id_kegiatan})">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSubkegiatan(${c.id})">Hapus</button>
                </div>
                <button class="btn btn-sm btn-primary text-white ml-2" onclick="addBelanja(${c.id},'${c.nama}')"><i class="fa fa-plus-circle"></i> Belanja</button>`;
                label = 'SUBKEGIATAN';
            } else if (lvl === 'belanja') {
                aksi = `<div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="editBelanja(${c.id},${c.parent})">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBelanja(${c.id})">Hapus</button>
                </div>`;
                label = 'BELANJA';
            }
            html += `<tr class="level-${lvl}">
                <td class="text-center">${cls?`<i class="fa fa-plus-circle text-info ${cls}" style="cursor:pointer" data-id="${c.id}" data-level="${lvl}"></i>`:''}</td>
                <td>${label}: ${c.nama}</td>
                <td class="text-end">${c.anggaran?new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR'}).format(c.anggaran):'-'}</td>
                <td class="text-center">${c.status==1?'<span class="badge bg-success">Aktif</span>':'<span class="badge bg-danger">Tidak Aktif</span>'}</td>
                <td class="text-center">${aksi}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        return html;
    }

    // Expand handlers
    $(document).on('click', '.expand-program', function(e) {
        e.stopPropagation();
        const icon = $(this),
            tr = icon.closest('tr'),
            row = table.row(tr),
            data = row.data();
        if (row.child.isShown()) {
            row.child.hide();
            icon.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-info');
        } else {
            fetch(
                    `<?= base_url('perencanaan/detail/ajaxChildren') ?>?parent_id=${data.id}&level=program`
                )
                .then(res => res.json())
                .then(res => {
                    row.child(generateChildTable(res.data)).show();
                    icon.removeClass('fa-plus-circle text-info').addClass(
                        'fa-minus-circle text-danger');
                });
        }
    });

    $(document).on('click', '.expand-kegiatan', function(e) {
        e.stopPropagation();
        const icon = $(this),
            trParent = icon.closest('tr'),
            idKegiatan = icon.data('id');
        if ($(trParent).next().hasClass('child-subkegiatan')) {
            $(trParent).next().remove();
            icon.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-info');
        } else {
            fetch(
                    `<?= base_url('perencanaan/detail/ajaxChildren') ?>?parent_id=${idKegiatan}&level=kegiatan`
                )
                .then(res => res.json())
                .then(res => {
                    $(trParent).after(
                        `<tr class="child-subkegiatan"><td colspan="5">${generateChildTable(res.data)}</td></tr>`
                    );
                    icon.removeClass('fa-plus-circle text-info').addClass(
                        'fa-minus-circle text-danger');
                });
        }
    });

    $(document).on('click', '.expand-subkegiatan', function(e) {
        e.stopPropagation();
        const icon = $(this),
            trParent = icon.closest('tr'),
            idSub = icon.data('id');
        if ($(trParent).next().hasClass('child-belanja')) {
            $(trParent).next().remove();
            icon.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-info');
        } else {
            fetch(
                    `<?= base_url('perencanaan/detail/ajaxChildren') ?>?parent_id=${idSub}&level=subkegiatan`
                )
                .then(res => res.json())
                .then(res => {
                    $(trParent).after(
                        `<tr class="child-belanja"><td colspan="5">${generateChildTable(res.data)}</td></tr>`
                    );
                    icon.removeClass('fa-plus-circle text-info').addClass(
                        'fa-minus-circle text-danger');
                });
        }
    });

    // Klik baris level program untuk expand/collapse
    $(document).on('click', 'tr.level-program', function(e) {
        // Kalau kliknya di tombol aksi jangan ikut trigger expand
        if ($(e.target).is('button, i.fa-minus-circle, i.fa-plus-circle')) return;

        let icon = $(this).find('.expand-program');
        if (icon.length) {
            icon.trigger('click');
        }
    });

    // Klik baris level kegiatan
    $(document).on('click', 'tr.level-kegiatan', function(e) {
        if ($(e.target).is('button, i.fa-minus-circle, i.fa-plus-circle')) return;

        let icon = $(this).find('.expand-kegiatan');
        if (icon.length) {
            icon.trigger('click');
        }
    });

    // Klik baris level subkegiatan
    $(document).on('click', 'tr.level-subkegiatan', function(e) {
        if ($(e.target).is('button, i.fa-minus-circle, i.fa-plus-circle')) return;

        let icon = $(this).find('.expand-subkegiatan');
        if (icon.length) {
            icon.trigger('click');
        }
    });

});



// ===== Subkegiatan Aksi =====
function addSubkegiatan(kegiatanId, kegiatanNama) {
    fetch(
            `<?= base_url('perencanaan/detail/form') ?>?id_kegiatan=${kegiatanId}&parent_nama=${encodeURIComponent(kegiatanNama)}`
        )
        .then(res => res.text())
        .then(html => {
            document.querySelector('.viewmodal').innerHTML = html;
            const modalEl = document.getElementById('modalForm');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
}

function editSubkegiatan(id_sub, id_kegiatan) {
    fetch(`<?= base_url('perencanaan/detail/form') ?>?id_sub=${id_sub}&id_kegiatan=${id_kegiatan}`)
        .then(res => res.text())
        .then(html => {
            document.querySelector('.viewmodal').innerHTML = html;
            const modalEl = document.getElementById('modalForm');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
}

function deleteSubkegiatan(id) {
    swal.fire({
        title: 'Anda yakin?',
        text: "Anda akan menghapus data Sub Kegiatan ini. Data yang terkait akan ikut terhapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "get",
                url: "<?= base_url('perencanaan/detail/delete') ?>/" + id,
                dataType: "json",
                success: function(response) {
                    showToast('Terhapus', response.message, 'success', 1500);
                    $('#programTable').DataTable().ajax.reload(null, false);

                }
            });
        }
    })
}

// ===== Modal Kegiatan =====
function addKegiatan(id, parent_nama) {
    fetch(
            `<?= base_url('perencanaan/detail/modalFormKegiatan') ?>?idProgram=${id}&nama_program=${encodeURIComponent(parent_nama)}`)
        .then(res => res.text())
        .then(html => {
            document.querySelector('.viewmodal').innerHTML = html;
            const modalEl = document.getElementById('modalKegiatan');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
            modalEl.addEventListener('hidden.bs.modal', function() {
                modalEl.remove();
            });
        });
}


function editKegiatan(id, parent_id) {
    fetch(`<?= base_url('perencanaan/detail/modalFormKegiatan') ?>?id=${id}&idProgram=${parent_id}`)
        .then(res => res.text())
        .then(html => {
            document.querySelector('.viewmodal').innerHTML = html;
            const modalEl = document.getElementById('modalKegiatan');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
            modalEl.addEventListener('hidden.bs.modal', function() {
                modalEl.remove();
            });
        });
}

function deteleKegiatan(id) {
    // konfirmasi swetalert2
    swal.fire({
        title: 'Anda yakin?',
        text: "Anda akan menghapus data kegiatan ini. Data yang terkait akan ikut terhapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "get",
                url: "<?= base_url('perencanaan/kegiatan/delete') ?>/" + id,
                dataType: "json",
                success: function(response) {
                    showToast('Terhapus', response.message, 'success', 1500);
                    $('#programTable').DataTable().ajax.reload(null, false);
                }
            });
        }
    })

}




// ===== Modal Program =====
function addProgram(id, parent_nama) {
    fetch(`<?= base_url('perencanaan/detail/modalFormProgram') ?>?idProgram=${id}`)
        .then(res => res.text())
        .then(html => {
            document.querySelector('.viewmodal').innerHTML = html;
            const modalEl = document.getElementById('modalFormProgram');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
            modalEl.addEventListener('hidden.bs.modal', function() {
                modalEl.remove();
            });
        });
}

function editProgram(id) {
    fetch(`<?= base_url('perencanaan/detail/modalFormProgram') ?>?idProgram=${id}`)
        .then(res => res.text())
        .then(html => {
            document.querySelector('.viewmodal').innerHTML = html;
            const modalEl = document.getElementById('modalFormProgram');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
            modalEl.addEventListener('hidden.bs.modal', function() {
                modalEl.remove();
            });
        });
}

function deteleProgram(id) {
    // konfirmasi swetalert2
    swal.fire({
        title: 'Anda yakin?',
        text: "Anda akan menghapus data program ini. Data yang terkait akan ikut terhapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "get",
                url: "<?= base_url('perencanaan/program/delete') ?>/" + id,
                dataType: "json",
                success: function(response) {
                    showToast('Terhapus', response.message, 'success', 1500);
                    $('#programTable').DataTable().ajax.reload(null, false);

                }
            });
        }
    })

}

// Tambah Belanja
function addBelanja(idSub, namaSub) {
    fetch(`<?= base_url('perencanaan/detail/modalFormBelanja') ?>?id_sub=${idSub}`)
        .then(res => res.text())
        .then(html => {
            document.body.insertAdjacentHTML('beforeend', html);
            const modalEl = document.getElementById('modalBelanja');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            modalEl.addEventListener('hidden.bs.modal', function() {
                modalEl.remove();
            });
        });
}

// Edit Belanja
function editBelanja(idBelanja, idSub) {
    fetch(`<?= base_url('perencanaan/detail/modalFormBelanja') ?>?id_sub=${idSub}&id_belanja=${idBelanja}`)
        .then(res => res.text())
        .then(html => {
            document.body.insertAdjacentHTML('beforeend', html);
            const modalEl = document.getElementById('modalBelanja');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            modalEl.addEventListener('hidden.bs.modal', function() {
                modalEl.remove();
            });
        });
}

// Hapus Belanja
function deleteBelanja(id) {
    Swal.fire({
        title: 'Hapus Belanja?',
        text: 'Data yang dihapus tidak bisa dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`<?= base_url('perencanaan/detail/deleteBelanja') ?>/${id}`)
                .then(res => res.json())
                .then(res => {
                    if (res.status) {
                        Swal.fire('Terhapus!', res.message, 'success');
                        if ($.fn.DataTable.isDataTable('#programTable')) {
                            $('#programTable').DataTable().ajax.reload(null, false);
                        }
                    } else Swal.fire('Gagal', res.message, 'error');
                });
        }
    });
}


// ===== Submit Form (Delegated Event) =====
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form && form.classList.contains('ajaxForm')) {
        e.preventDefault();
        const url = form.dataset.url;
        const formData = new FormData(form);

        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success || res.status) {

                    showToast('Berhasil', res.message, 'success', 1500);
                    const modalEl = form.closest('.modal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    form.reset();
                    if ($.fn.DataTable.isDataTable('#programTable')) {
                        $('#programTable').DataTable().ajax.reload(null, false);
                    }
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            })
            .catch(err => Swal.fire('Terjadi Kesalahan', err.message || err, 'error'));
    }
});
</script>


<?= $this->endSection() ?>