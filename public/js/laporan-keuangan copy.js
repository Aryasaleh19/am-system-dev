$(document).ready(function () {
  $(".laporan-link").click(function () {
    let laporan = $(this).text().trim();
    $(".title-laporan").text(laporan);

    $(".laporan-link").removeClass("active");
    $(this).addClass("active");

    renderParameter(laporan);
  });

  function loadSelect(apiUrl, selectId, textKey, valueKey, params = {}) {
    $.getJSON(apiUrl, params, function (data) {
      let $sel = $("#" + selectId);
      $sel.empty().append('<option value="">[ Pilih ]</option>');
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
          <div class="col-md-4">
            <input type="date" class="form-control" id="tglRekap">
            <small class="text-muted">Tanggal</small>
          </div>
          <div class="col-md-4">
            <select class="form-select" id="petugas">
              <option>[ Pilih ]</option>
            </select>
            <small class="text-muted">Petugas</small>
          </div>
        `;
        break;
      case "Kartu Kontrol Siswa":

      case "Kartu Kontrol Siswa":
        html += `
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
            <div class="col-md-2">
            <input type="number" class="form-control " id="tahun" value="2025">
            <small class="text-muted">Tahun</small>
            </div>
            <div class="col-md-4">
            <select class="form-select" id="bank">
                <option value="all" selected>[ All ]</option>
            </select>
            <small class="text-muted">Bank</small>
            </div>
            <div class="col-md-4">
            <select class="form-select" id="jenisPembayaran">
                <option>[ Pilih ]</option>
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

    html += `</div>`;
    html += `<div class="text-end mt-2">
               <button class="btn btn-outline-info btn-sm" id="btnGenerate">🔍 Tampilkan Laporan</button>
             </div>`;

    $("#parameterLaporan").html(html);

    if (laporan === "Rekapitulasi Transaksi Harian") {
      loadSelect("/api/select2/petugas", "petugas", "NAMA", "PEGAWAI_ID");
    }

    $("#btnGenerate")
      .off("click")
      .on("click", function () {
        if (laporan === "Rekapitulasi Transaksi Harian") {
          let tgl = $("#tglRekap").val();
          let petugas = $("#petugas").val();

          if (!tgl || !petugas) {
            alert("Mohon lengkapi parameter laporan!");
            return;
          }

          // Tampilkan PDF di div #tampilLaporanPDF
          let url =
            "/laporan/keuangan/pdfLaporanHarianPetugas?" +
            "tgl=" +
            tgl +
            "&petugas=" +
            petugas;

          let iframe = `<iframe src="${url}" style="width:100%;height:600px;" frameborder="0"></iframe>`;
          $("#tampilLaporanPDF").html(iframe);
        }
      });
  }
});
