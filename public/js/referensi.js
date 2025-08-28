$(document).ready(function () {
  // Inisialisasi Select2 untuk modal form
  $("#agama_id, #angkatan_id, #id_sekolah, #id_ruangan").select2({
    theme: "bootstrap-5",
    dropdownParent: $("#modalformSiswaBaru"),
  });

  // Inisialisasi Select2 untuk filter (di luar modal)
  $("#filterFrom, #filterTo, #filterSekolah").select2({
    theme: "bootstrap-5",
    placeholder: "[ All ]",
    allowClear: true,
    width: "resolve",
  });

  // Load data angkatan untuk filterFrom dan filterTo
  $.ajax({
    url: "/api/referensi/angkatan",
    type: "GET",
    dataType: "json",
    success: function (data) {
      let selFrom = $("#filterFrom");
      let selTo = $("#filterTo");

      selFrom.empty().append('<option value=""></option>'); // allowClear
      selTo.empty().append('<option value=""></option>');

      data.forEach(function (item) {
        let option = new Option(item.ANGKATAN, item.ID, false, false);
        selFrom.append(option.cloneNode(true));
        selTo.append(option.cloneNode(true));
      });

      selFrom.trigger("change");
      selTo.trigger("change");
    },
  });

  // 🔹 Load Ruangan untuk modal
  $.ajax({
    url: "/api/referensi/ruangan",
    type: "GET",
    dataType: "json",
    success: function (data) {
      let sel = $("#id_ruangan");
      sel.empty().append('<option value="">[ Pilih ]</option>');
      data.forEach(function (item) {
        sel.append(new Option(item.RUANGAN, item.ID));
      });
      sel.val("").trigger("change");
    },
  });

  // Load data sekolah untuk filterSekolah
  $.ajax({
    url: "/api/referensi/sekolah",
    type: "GET",
    dataType: "json",
    success: function (data) {
      let selSekolah = $("#filterSekolah");
      selSekolah.empty().append('<option value=""></option>'); // allowClear
      data.forEach(function (item) {
        selSekolah.append(new Option(item.NAMA_SEKOLAH, item.ID));
      });
      selSekolah.trigger("change");
    },
  });

  // Event saat modal form siswa baru muncul
  $("#modalformSiswaBaru").on("shown.bs.modal", function () {
    initReferensiSelects(this);

    // Load Agama
    $.ajax({
      url: "/api/referensi/agama",
      type: "GET",
      dataType: "json",
      success: function (data) {
        let sel = $("#agama_id");
        sel.empty().append('<option value="">[ Pilih ]</option>');
        data.forEach(function (item) {
          sel.append(new Option(item.AGAMA, item.ID));
        });
        sel.val("").trigger("change"); // reset select2
      },
    });

    // Load Angkatan untuk modal
    $.ajax({
      url: "/api/referensi/angkatan",
      type: "GET",
      dataType: "json",
      success: function (data) {
        let sel = $("#angkatan_id");
        sel.empty().append('<option value="">[ Pilih ]</option>');
        data.forEach(function (item) {
          sel.append(new Option(item.ANGKATAN, item.ID));
        });
        sel.val("").trigger("change");
      },
    });
  });
});

// Inisialisasi Select2 dalam modal
function initReferensiSelects(selector) {
  const $container = $(selector);

  // Pendidikan select2 init (jika ada)
  const $pendidikan = $container.find("#id_pendidikan");
  if ($pendidikan.length) {
    $pendidikan.select2({
      theme: "bootstrap-5",
      placeholder: "[ Pilih ]",
      dropdownParent: $("#modalformSiswaBaru"),
    });
    // load pendidikan via AJAX kalau perlu
  }

  // Sekolah select2 init dan load data
  const $sekolah = $container.find("#id_sekolah");
  if ($sekolah.length) {
    $sekolah.select2({
      theme: "bootstrap-5",
      placeholder: "[ Pilih ]",
      dropdownParent: $("#modalformSiswaBaru"),
    });
    $.ajax({
      url: "/api/referensi/sekolah",
      type: "GET",
      dataType: "json",
      success: function (data) {
        $sekolah.empty().append('<option value="">[ Pilih ]</option>');
        data.forEach((item) => {
          $sekolah.append(new Option(item.NAMA_SEKOLAH, item.ID));
        });
        $sekolah.trigger("change");
      },
    });
  }

  // Angkatan select2 init dan load data
  const $angkatan = $container.find("#angkatan_id");
  if ($angkatan.length) {
    $angkatan.select2({
      theme: "bootstrap-5",
      placeholder: "[ Pilih ]",
      dropdownParent: $("#modalformSiswaBaru"),
    });
    $.ajax({
      url: "/api/referensi/angkatan",
      type: "GET",
      dataType: "json",
      success: function (data) {
        $angkatan.empty().append('<option value="">[ Pilih ]</option>');
        data.forEach((item) => {
          $angkatan.append(new Option(item.ANGKATAN, item.ID));
        });
        $angkatan.trigger("change");
      },
    });
  }

  // Ruangan select2 init dan load data
  const $ruangan = $container.find("#id_ruangan");
  if ($ruangan.length) {
    $ruangan.select2({
      theme: "bootstrap-5",
      placeholder: "[ Pilih ]",
      dropdownParent: $("#modalformSiswaBaru"),
    });
    $.ajax({
      url: "/api/referensi/ruangan",
      type: "GET",
      dataType: "json",
      success: function (data) {
        $ruangan.empty().append('<option value="">[ Pilih ]</option>');
        data.forEach((item) => {
          $ruangan.append(new Option(item.RUANGAN, item.ID));
        });
        $ruangan.trigger("change");
      },
    });
  }
}
