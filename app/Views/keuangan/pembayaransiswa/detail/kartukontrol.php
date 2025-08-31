<div class="card shadow-none bg-transparent border border-info mb-3">
    <div class="card-body p-2">
        <input type="hidden" id="inputNis" name="inputNis" class="form-control" value="<?= $nis ?>">
        <strong class="card-title">Info!</strong>
        <span class="card-text">Kartu Kontrol digunakan untuk memonitoring aktivitas pembayaran tagihan siswa.</span>
    </div>
</div>
<div class="row">
    <!-- Kiri -->
    <div class="col-4">
        <button id="previewBtn" class="btn btn-outline-info btn-lg w-100">
            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Preview
        </button>
    </div>

    <!-- Kanan -->
    <div class="col-8">
        <div class="card shadow-none bg-transparent border border-info">
            <div class="card-header">
                <i class="fa fa-eye" aria-hidden="true"></i> Preview Kartu Kontrol
            </div>
            <div class="card-body" id="rightContent">
                <div class="alert alert-info">
                    <strong>Informasi:</strong> klik Tombol Preview untuk melihat kartu kontrol.
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.getElementById("previewBtn").addEventListener("click", function() {
    const nis = document.getElementById('inputNis').value.trim();
    if (!nis) {
        alert('Silakan masukkan NIS terlebih dahulu.');
        return;
    }

    const pdfUrl = `/laporan/keuangan/kartukontrol?nis=${encodeURIComponent(nis)}`;

    const rightContent = document.getElementById("rightContent");

    // Kosongkan konten lama
    rightContent.innerHTML = "";

    // Buat dan append iframe langsung
    const iframe = document.createElement("iframe");
    iframe.className = "iframe-full";
    iframe.src = pdfUrl;
    iframe.style.width = "100%";
    iframe.style.height = "600px";
    iframe.style.border = "none";

    rightContent.appendChild(iframe);
});
</script>