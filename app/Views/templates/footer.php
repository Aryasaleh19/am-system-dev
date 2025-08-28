<!-- build:js assets/vendor/js/core.js -->
<script src="<?= base_url('template/assets/vendor/js/helpers.js') ?>"></script>
<script src="<?= base_url('template/assets/js/config.js') ?>"></script>


<!-- <script src="<?= base_url('template/') ?>assets/vendor/libs/jquery/jquery.js"></script> -->
<script src="<?= base_url('template/') ?>assets/vendor/libs/popper/popper.js"></script>
<script src="<?= base_url('template/') ?>assets/vendor/js/bootstrap.js"></script>
<script src="<?= base_url('template/') ?>assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

<script src="<?= base_url('template/') ?>assets/vendor/js/menu.js"></script>
<!-- endbuild -->

<!-- scroll card js -->
<script src="<?= base_url('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') ?>"></script>
<!-- select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>


<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>


<!-- Vendors JS -->
<script src="<?= base_url('template/') ?>assets/vendor/libs/apex-charts/apexcharts.js"></script>

<!-- Main JS -->
<script src="<?= base_url('template/') ?>assets/js/main.js"></script>

<!-- Page JS -->
<script src="<?= base_url('template/') ?>assets/js/dashboards-analytics.js"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form').forEach(form => {
        form.setAttribute('autocomplete', 'off');
    });

    document.querySelectorAll(
        'input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="date"]'
    ).forEach(input => {
        input.setAttribute('autocomplete', 'off');
    });
});

function disableAutocomplete() {
    $('form').attr('autocomplete', 'off');
    $('input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="date"]').attr(
        'autocomplete', 'off');
}
</script>

<script>
// menggunakan animasi timeout
function showToast(title, message, type = 'success') {
    // type bisa: success, danger, warning, info
    let bgClass = 'bg-success';
    switch (type) {
        case 'success':
            bgClass = 'bg-success';
            break;
        case 'danger':
            bgClass = 'bg-danger';
            break;
        case 'warning':
            bgClass = 'bg-warning';
            break;
        case 'info':
            bgClass = 'bg-info';
            break;
    }

    const toastId = 'toast-' + Date.now();
    const toastHTML = `
        <div id="${toastId}" class="bs-toast toast ${bgClass}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bx bx-bell me-2"></i>
                <div class="me-auto fw-semibold">${title}</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    // append toast ke container
    $("#toastContainer").append(toastHTML);

    const toastEl = document.getElementById(toastId);
    const bsToast = new bootstrap.Toast(toastEl, {
        autohide: false
    });
    bsToast.show();

    // Tambahkan class show untuk animasi slide-down
    setTimeout(() => {
        toastEl.classList.add('show');
    }, 700000);

    // Auto hide
    setTimeout(() => {
        toastEl.classList.remove('show');
        setTimeout(() => {
            bsToast.dispose();
            toastEl.remove();

            // Reload DataTable setelah toast hilang
            if ($.fn.DataTable.isDataTable('#programTable')) {
                $('#programTable').DataTable().ajax.reload(null, false);
            }
        }, 700000); // delay agar animasi selesai
    }, duration);
}

// tanpa animasi time out
// function showToast(title, message, type = 'success') {
//     // type bisa: success, danger, warning, info
//     let bgClass = 'bg-success';
//     switch (type) {
//         case 'success':
//             bgClass = 'bg-success';
//             break;
//         case 'danger':
//             bgClass = 'bg-danger';
//             break;
//         case 'warning':
//             bgClass = 'bg-warning';
//             break;
//         case 'info':
//             bgClass = 'bg-info';
//             break;
//     }

//     const toastId = 'toast-' + Date.now();
//     const toastHTML = `
//         <div id="${toastId}" class="bs-toast toast ${bgClass} show" role="alert" aria-live="assertive" aria-atomic="true">
//             <div class="toast-header">
//                 <i class="bx bx-bell me-2"></i>
//                 <div class="me-auto fw-semibold">${title}</div>
//                 <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
//             </div>
//             <div class="toast-body">
//                 ${message}
//             </div>
//         </div>
//     `;

//     // append toast ke container
//     $("#toastContainer").append(toastHTML);

//     const toastEl = document.getElementById(toastId);
//     const bsToast = new bootstrap.Toast(toastEl, {
//         autohide: false // tidak otomatis hilang
//     });
//     bsToast.show();

//     // event ketika toast di-close
//     toastEl.addEventListener('hidden.bs.toast', function() {
//         bsToast.dispose();
//         toastEl.remove();

//         // reload DataTable setelah toast di-close
//         if ($.fn.DataTable.isDataTable('#programTable')) {
//             $('#programTable').DataTable().ajax.reload(null, false);
//         }
//     });
// }



function formatRupiah(angka) {
    if (isNaN(angka)) return '';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

$(document).ready(function() {
    $(document).on('click', '.focusable-table tbody tr', function() {
        const $table = $(this).closest('table');

        // Hapus fokus di semua baris dalam tabel itu
        $table.find('tbody tr').removeClass('focused');

        // Tambahkan fokus di baris yang diklik
        $(this).addClass('focused');
    });
});
</script>
</body>

</html>