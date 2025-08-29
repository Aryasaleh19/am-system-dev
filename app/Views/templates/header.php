<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="<?= base_url('template/assets/') ?>" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $title ?? 'Dashboard' ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?><?= session()->get('LOGO') ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <!-- sweet alert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Icons -->
    <link rel="stylesheet" href="<?= base_url('template/assets/vendor/fonts/boxicons.css') ?>" />




    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('template/assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('template/assets/vendor/css/theme-default.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('template/assets/css/demo.css') ?>" />

    <!-- Vendor CSS -->
    <link rel="stylesheet"
        href="<?= base_url('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('template/assets/vendor/libs/apex-charts/apex-charts.css') ?>" />

    <!-- fotnt awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />

    <!-- scroll card -->
    <link rel="stylesheet"
        href="<?= base_url('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>">
    <link rel="stylesheet"
        href="<?= base_url('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>">

    <!-- datatable -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">