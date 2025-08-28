$(document).ready(function () {
  $(".laporan-link").click(function () {
    let laporan = $(this).text().trim();
    $(".title-laporan").text(laporan);
    renderParameter(laporan);

    // kosongkan area pdf
    $("#tampilLaporanPDF").html(
      '<h6 class="text-muted text-center p-5">Silahkan pilih parameter untuk menampilkan laporan.</h6>'
    );
    // Tambahkan class 'active' ke yang diklik, hapus dari yang lain
    $(".laporan-link").removeClass("active");
    $(this).addClass("active");
  });

  function loadSelect(apiUrl, selectId, textKey, valueKey, params = {}) {
    $.getJSON(apiUrl, params, function (data) {
      let $sel = $("#" + selectId);
      // Simpan opsi All jika ada
      let allOption = $sel.find('option[value="all"]').length
        ? $sel.find('option[value="all"]').prop("outerHTML")
        : "";
      $sel.empty();
      if (allOption) $sel.append(allOption); // tambahkan All
      $sel.append("<option></option>"); // placeholder kosong
      data.forEach((item) => {
        $sel.append(
          `<option value="${item[valueKey]}">${item[textKey]}</option>`
        );
      });
      $sel.select2({
        theme: "bootstrap-5",
        placeholder: "[ Pilih ]",
        allowClear: true,
        width: "100%",
      });
    });
  }

  function renderParameter(laporan) {
    let html = '<div class="row g-2">';

    switch (laporan) {
      case "Rekapitulasi Transaksi Harian":
        html += `
          
          <div class="alert alert-warning text-black p-2">
              <strong>Perhatian!</strong> Tanggal yang dimaksud adalah Tanggal Transaksi yang dilakukan oleh masing-masing Petugas/Pengguna.
          </div>
          
          <div class="col-md-3">
            <input type="date" class="form-control" id="tglRekap">
            <small class="text-muted">Tanggal</small>
          </div>
          <div class="col-md-3">
            <input type="date" class="form-control" id="tglRekap2">
            <small class="text-muted">S/D</small>
          </div>
          <div class="col-md-3">
            <select class="form-select" id="petugas">
                <option>[ Pilih ]</option>
            </select>
            <small class="text-muted">Petugas</small>
          </div>
        `;
        break;

      case "Kartu Kontrol Siswa":
        html += `
        
        <div class="alert alert-warning text-black p-2">
          <strong>Perhatian!</strong> Siswa yang tampil sesuai dengan Departemen/Sekolah yang dipilih adalah Siswa yang memiliki Kewajiban Membayar pada Departemen/Sekolah yang dipilih.
        </div>
        
        <div class="col-md-6">
          <select class="form-select" id="sekolah">
            <option>[ Pilih ]</option>
          </select>
          <small class="text-muted">Departemen</small>
        </div>
          <div class="col-md-6">
            <select class="form-select" id="siswa">
                <option>[ Pilih ]</option>
            </select>
            <small class="text-muted">Siswa</small>
          </div>
        `;
        break;

      case "Daftar Pembayaran Siswa":
        html += `
            <div class="col-md-6">
              <select class="form-select" id="sekolah">
                <option>[ Pilih ]</option>
              </select>
              <small class="text-muted">Departemen</small>
            </div>
            <div class="col-md-2">
            <input type="number" class="form-control " id="tahun" value="2025">
            <small class="text-muted">Tahun Pembayaran</small>
            </div>
            <div class="col-md-4">
            <select class="form-select" id="bank">
                <option value="all" selected>[ All ]</option>
            </select>
            <small class="text-muted">Bank Penerima</small>
            </div>
            <div class="col-md-4">
            <select class="form-select" id="jenisPembayaran">
                <option value="all" selected>[ All ]</option>
            </select>
            <small class="text-muted">Jenis Pembayaran</small>
            </div>
            <div class="col-md-2">
            <select class="form-select" id="angkatan">
                <option value="all" selected>[ All ]</option>
            </select>
            <small class="text-muted">Angkatan</small>
            </div>
        `;
        break;

      case "Laporan Penerimaan Kas":
      case "Laporan Pengeluaran Kas":
        let kasLabel =
          laporan === "Laporan Pengeluaran Kas"
            ? "Bank/Kas Pembayaran"
            : "Bank/Kas Penerima";
        html += `
            <div class="col-md-3">
                <input type="date" class="form-control" id="tglAwal">
                <small class="text-muted">Tanggal Awal</small>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" id="tglAkhir">
                <small class="text-muted">Tanggal Akhir</small>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="kasBank">
                    <option value="all" selected>[ All ]</option>
                </select>
                <small class="text-muted">${kasLabel}</small>
            </div>
            `;
        break;
    }

    html += `</div>`; // end row
    html += `<div class="text-end mt-2"><button class="btn btn-outline-info btn-sm" id="btnGenerate">🔍 Tampilkan Laporan</button></div>`;

    $("#parameterLaporan").html(html);

    // Load select dasar tanpa parameter
    if ($("#sekolah").length)
      loadSelect("/api/select2/sekolah", "sekolah", "NAMA_SEKOLAH", "ID");
    if ($("#siswa").length)
      loadSelect("/api/select2/siswa", "siswa", "NAMA", "NIS");
    if ($("#petugas").length)
      loadSelect("/api/select2/petugas", "petugas", "NAMA", "PEGAWAI_ID");
    if ($("#bank").length)
      loadSelect("/api/select2/bank", "bank", "NAMA_BANK", "ID");
    if ($("#angkatan").length)
      loadSelect("/api/select2/angkatan", "angkatan", "ANGKATAN", "ID");
    if ($("#kasBank").length)
      loadSelect("/api/select2/bank", "kasBank", "NAMA_BANK", "ID");

    // Event change sekolah untuk jenisPembayaran dan siswa
    $("#sekolah")
      .off("change")
      .on("change", function () {
        let sekolahId = $(this).val();

        if ($("#siswa").length) {
          if (sekolahId) {
            loadSelect(
              "/api/select2/getSiswaBySekolah",
              "siswa",
              "NAMA",
              "NIS",
              { sekolah_id: sekolahId }
            );
          } else {
            loadSelect("/api/select2/siswa", "siswa", "NAMA", "NIS");
          }
        }

        if ($("#jenisPembayaran").length) {
          if (sekolahId) {
            loadSelect(
              "/api/select2/getJenisPenerimaanBySekolah",
              "jenisPembayaran",
              "JENIS_PENERIMAAN",
              "ID",
              { sekolah_id: sekolahId }
            );
          } else {
            $("#jenisPembayaran")
              .empty()
              .append("<option value='all'>[ All ]</option>")
              .select2({
                theme: "bootstrap-5",
                placeholder: "[ Pilih ]",
                allowClear: true,
                width: "100%",
              });
          }
        }
      });

    // Event change angkatan untuk siswa
    $("#angkatan")
      .off("change")
      .on("change", function () {
        let angkatanId = $(this).val();

        if ($("#siswa").length) {
          if (angkatanId && angkatanId !== "all") {
            loadSelect(
              "/api/select2/getSiswaByAngkatan",
              "siswa",
              "NAMA",
              "NIS",
              { angkatanId: angkatanId }
            );
          } else {
            loadSelect("/api/select2/siswa", "siswa", "NAMA", "NIS");
          }
        }
      });

    // Tombol generate laporan
    $("#btnGenerate")
      .off("click")
      .on("click", function () {
        if (laporan === "Rekapitulasi Transaksi Harian") {
          let tgl = $("#tglRekap").val();
          let tgl2 = $("#tglRekap2").val();
          let petugas = $("#petugas").val();

          if (!tgl || !petugas) {
            alert("Mohon lengkapi parameter laporan!");
            return;
          }

          let url =
            "/laporan/keuangan/pdfLaporanHarianPetugas?" +
            "tgl=" +
            tgl +
            "&petugas=" +
            petugas +
            "&tgl2=" +
            tgl2;
          $("#tampilLaporanPDF").html(
            `<iframe src="${url}" style="width:100%;height:600px;" frameborder="0"></iframe>`
          );
        }

        if (laporan === "Kartu Kontrol Siswa") {
          let sekolah = $("#sekolah").val();
          let nis = $("#siswa").val();

          if (!sekolah || !nis) {
            alert("Mohon lengkapi parameter laporan!");
            return;
          }

          let url =
            "/laporan/keuangan/kartukontrol?" +
            "sekolah=" +
            sekolah +
            "&nis=" +
            nis;
          $("#tampilLaporanPDF").html(
            `<iframe src="${url}" style="width:100%;height:600px;" frameborder="0"></iframe>`
          );
        }

        if (laporan === "Daftar Pembayaran Siswa") {
          let sekolah = $("#sekolah").val();
          let tahun = $("#tahun").val();
          let bank = $("#bank").val();
          let jenisPembayaran = $("#jenisPembayaran").val();
          let angkatan = $("#angkatan").val();

          if (!sekolah || !tahun || !bank || !jenisPembayaran || !angkatan) {
            alert("Mohon lengkapi parameter laporan!");
            return;
          }

          let url =
            "/laporan/keuangan/LaporanTahunanPembayaranSiswa?" +
            "sekolah=" +
            sekolah +
            "&tahun=" +
            tahun +
            "&bank=" +
            bank +
            "&jenisPembayaran=" +
            jenisPembayaran +
            "&angkatan=" +
            angkatan;
          $("#tampilLaporanPDF").html(
            `<iframe src="${url}" style="width:100%;height:600px;" frameborder="0"></iframe>`
          );
        }

        if (laporan === "Laporan Penerimaan Kas") {
          let tglAwal = $("#tglAwal").val();
          let tglAkhir = $("#tglAkhir").val();
          let bank = $("#kasBank").val();
          if (bank == "all") bank = ""; // jika all maka jadikan null

          if (!tglAwal || !tglAkhir) {
            showToast(
              "Validasi",
              "Mohon lengkapi parameter laporan!",
              "danger"
            );
            return;
          }

          let url =
            "/keuangan/laporankas/LaporanPenerimaanKas?" +
            "tglAwal=" +
            tglAwal +
            "&tglAkhir=" +
            tglAkhir +
            "&bank=" +
            bank;
          $("#tampilLaporanPDF").html(
            `<iframe src="${url}" style="width:100%;height:600px;" frameborder="0"></iframe>`
          );
        }

        if (laporan === "Laporan Pengeluaran Kas") {
          let tglAwal = $("#tglAwal").val();
          let tglAkhir = $("#tglAkhir").val();
          let bank = $("#kasBank").val();
          if (bank == "all") bank = ""; // jika all maka jadikan null

          if (!tglAwal || !tglAkhir) {
            showToast(
              "Validasi",
              "Mohon lengkapi parameter laporan!",
              "danger"
            );
            return;
          }

          let url =
            "/keuangan/laporankas/LaporanPengeluaranKas?" +
            "tglAwal=" +
            tglAwal +
            "&tglAkhir=" +
            tglAkhir +
            "&bank=" +
            bank;
          $("#tampilLaporanPDF").html(
            `<iframe src="${url}" style="width:100%;height:600px;" frameborder="0"></iframe>`
          );
        }
      });
  }

  // Inisialisasi pertama
  $(".laporan-link.active").click();
});
