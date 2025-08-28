<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="<?= base_url('template') ?>/assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        tent="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login Sistem</title>

    <meta name="description" content="Sistem Login Tinelo Lipu Gorontalo - Created By MokarajaNET - IwanMaksud" />
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/logo-lh.jpg') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="<?= base_url('template') ?>/assets/vendor/fonts/boxicons.css" />

    <link rel="stylesheet" href="<?= base_url('template') ?>/assets/vendor/css/core.css"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?= base_url('template') ?>/assets/vendor/css/theme-default.css"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?= base_url('template') ?>/assets/css/demo.css" />
    <link rel="stylesheet"
        href="<?= base_url('template') ?>/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?= base_url('template') ?>/assets/vendor/css/pages/page-auth.css" />
    <script src="<?= base_url('template') ?>/assets/vendor/js/helpers.js"></script>
    <script src="<?= base_url('template') ?>/assets/js/config.js"></script>
</head>

<body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card">
                    <div class="card-body">
                        <div class="app-brand justify-content-center">
                            <a href="<?= base_url() ?>" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img src="<?= base_url() ?>/assets/img/logo-lh.jpg" style="width: 60px;" alt="">
                                </span>
                                <span class="app-brand-text fw-bolder text-body text-uppercase h4">LH CARE SYSTEM</span>
                            </a>
                        </div>
                        <h5 class="mb-2">Welcome to LH Car System 🙏</h5>
                        <p class="mb-4">Silahkan masukan username dan password</p>

                        <form id="formAuthentication" class="mb-3" method="POST" action="<?= base_url('login') ?>">
                            <?php if(session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="email" class="form-label">Username</label>
                                <input type="text" class="form-control" id="email" name="email-username"
                                    placeholder="Masukan username Anda" autofocus required />
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" required />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <?php
                            $angka1 = rand(1, 10);
                            $angka2 = rand(1, 10);
                            $captcha_result = $angka1 + $angka2;
                            session()->set('captcha_login', $captcha_result);
                            ?>

                            <div class="mb-3">
                                <label for="captcha" class="form-label">Berapa hasil dari
                                    <?= $angka1 . ' + ' . $angka2 ?> ?</label>
                                <input type="text" class="form-control" id="captcha" name="captcha"
                                    placeholder="Masukkan jawaban">
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-outline-success d-grid w-100" type="submit"> Masuk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('template') ?>/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="<?= base_url('template') ?>/assets/vendor/libs/popper/popper.js"></script>
    <script src="<?= base_url('template') ?>/assets/vendor/js/bootstrap.js"></script>
    <script src="<?= base_url('template') ?>/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="<?= base_url('template') ?>/assets/vendor/js/menu.js"></script>
    <script src="<?= base_url('template') ?>/assets/js/main.js"></script>
</body>

</html>