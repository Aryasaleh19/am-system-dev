$(document).ready(function () {
  // Inisialisasi Select2
  $("#PROVINSI, #KABUPATEN, #KECAMATAN, #KELURAHAN").select2({
    placeholder: "[ Pilih ]",
    theme: "bootstrap-5",
    allowClear: true,
    dropdownParent: $("#modalformSiswaBaru"), // <== ini penting
  });

  const resetSelect = (selector) => {
    $(selector).empty().trigger("change");
  };

  // Load provinsi saat halaman dibuka
  $.ajax({
    url: "/api/wilayah/provinsi",
    type: "GET",
    dataType: "json",
    success: function (data) {
      $("#PROVINSI")
        .append(new Option("-- Pilih --", "", true, true))
        .trigger("change");
      data.forEach((item) => {
        let option = new Option(item.NMPROV, item.KDPROV, false, false);
        $("#PROVINSI").append(option);
      });
    },
    error: function (xhr) {
      alert(`Gagal memuat provinsi: ${xhr.status} - ${xhr.responseText}`);
    },
  });

  // Load kabupaten saat provinsi dipilih
  $("#PROVINSI").on("change", function () {
    resetSelect("#KABUPATEN");
    resetSelect("#KECAMATAN");
    resetSelect("#KELURAHAN");

    const id = $(this).val();
    if (!id) return;

    $.ajax({
      url: `/api/wilayah/kabupaten/${id}`,
      type: "GET",
      dataType: "json",
      success: function (data) {
        $("#KABUPATEN")
          .append(new Option("-- Pilih --", "", true, true))
          .trigger("change");
        data.forEach((item) => {
          let option = new Option(item.NMKAB, item.KDKAB, false, false);
          $("#KABUPATEN").append(option);
        });
      },
      error: function (xhr) {
        alert(`Gagal memuat kabupaten: ${xhr.status} - ${xhr.responseText}`);
      },
    });
  });

  // Load kecamatan saat kabupaten dipilih
  $("#KABUPATEN").on("change", function () {
    resetSelect("#KECAMATAN");
    resetSelect("#KELURAHAN");

    const id = $(this).val();
    if (!id) return;

    $.ajax({
      url: `/api/wilayah/kecamatan/${id}`,
      type: "GET",
      dataType: "json",
      success: function (data) {
        $("#KECAMATAN")
          .append(new Option("-- Pilih --", "", true, true))
          .trigger("change");
        data.forEach((item) => {
          let option = new Option(item.NMKEC, item.KDKEC, false, false);
          $("#KECAMATAN").append(option);
        });
      },
      error: function (xhr) {
        alert(`Gagal memuat kecamatan: ${xhr.status} - ${xhr.responseText}`);
      },
    });
  });

  // Load kelurahan saat kecamatan dipilih
  $("#KECAMATAN").on("change", function () {
    resetSelect("#KELURAHAN");

    const id = $(this).val();
    if (!id) return;

    $.ajax({
      url: `/api/wilayah/kelurahan/${id}`,
      type: "GET",
      dataType: "json",
      success: function (data) {
        $("#KELURAHAN")
          .append(new Option("-- Pilih --", "", true, true))
          .trigger("change");
        data.forEach((item) => {
          let option = new Option(item.NMKEL, item.KDKEL, false, false);
          $("#KELURAHAN").append(option);
        });
      },
      error: function (xhr) {
        alert(`Gagal memuat kelurahan: ${xhr.status} - ${xhr.responseText}`);
      },
    });
  });

  // // set provinsi dulu, triger change otomatis load kabupaten
  // function setWilayahTerpilih(data) {
  //   if (!data) return;

  //   $("#PROVINSI").val(data.PROV_ID).trigger("change");

  //   $("#PROVINSI").on("change", function () {
  //     if ($("#KABUPATEN").children("option").length > 1) {
  //       $("#KABUPATEN").val(data.KAB_ID).trigger("change");
  //     }
  //   });

  //   $("#KABUPATEN").on("change", function () {
  //     if ($("#KECAMATAN").children("option").length > 1) {
  //       $("#KECAMATAN").val(data.KEC_ID).trigger("change");
  //     }
  //   });

  //   $("#KECAMATAN").on("change", function () {
  //     if ($("#KELURAHAN").children("option").length > 1) {
  //       $("#KELURAHAN").val(data.KEL_ID).trigger("change");
  //     }
  //   });
  // }
});
