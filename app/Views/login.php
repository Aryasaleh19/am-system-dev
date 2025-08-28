<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AM System - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .gradient-custom-2 {
            background: linear-gradient(135deg, #FFD580 0%, #FFA500 50%, #FF4500 100%);

            border-radius: 0px;
        }

        @media (min-width: 768px) {
            .gradient-form {
                height: 100vh !important;
            }
        }

        @media (min-width: 769px) {
            .gradient-custom-2 {
                border-top-right-radius: .3rem;
                border-bottom-right-radius: .3rem;
            }
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #FFD580 0%, #FFA500 50%, #FF4500 100%);

            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .captcha-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .captcha-question {
            font-size: 1.1rem;
            font-weight: 500;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-container {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .text-primary-custom {
            color: #667eea !important;
        }
    </style>
</head>

<body>
    <section class="h-100 gradient-form" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">

                                    <div class="text-center logo-container">
                                        <div class="mb-3">
                                            <!-- <img src="<?= base_url('assets/img/1756232933_4301eddc1a054aee1928.jpg') ?>" width="200" alt=""> -->
                                            <!-- <i class="bi bi-flower1 text-primary-custom" style="font-size: 4rem;"></i> -->
                                        </div>
                                        <h4 class="mt-1 mb-5  pb-1 text-warning">Al-Muhajirin System Login</h4>
                                    </div>

                                    <!-- Alert untuk menampilkan pesan error/success -->
                                    <?php if (session()->getFlashdata('error')) : ?>
                                        <div id="loginAlert" class="alert alert-danger " role="alert">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <span id="alertMessage"><?= session()->getFlashdata('error'); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <form id="loginForm" method="POST" action="<?= base_url('login') ?>">
                                        <p class="mb-4">Please login to your account</p>

                                        <div class="form-outline mb-3">
                                            <label class="form-label" for="username">
                                                <i class="bi bi-person me-1"></i>Username
                                            </label>
                                            <input type="text" id="username" name="email-username" class="form-control"
                                                placeholder="Enter your username" required />
                                            <div class="invalid-feedback">
                                                Please provide a valid username.
                                            </div>
                                        </div>

                                        <div class="form-outline mb-3">
                                            <label class="form-label" for="password">
                                                <i class="bi bi-lock me-1"></i>Password
                                            </label>
                                            <div class="input-group">
                                                <input type="password" id="password" name="password" class="form-control"
                                                    placeholder="Enter your password" required />
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback">
                                                Please provide a valid password.
                                            </div>
                                        </div>

                                        <!-- Improved CAPTCHA -->
                                        <div class="captcha-container">
                                            <div class="captcha-question mb-2">
                                                <?php
                                                $angka1 = rand(1, 10);
                                                $angka2 = rand(1, 10);
                                                $captcha_result = $angka1 + $angka2;
                                                session()->set('captcha_login', $captcha_result);
                                                ?>
                                                <i class="bi bi-calculator text-primary-custom"></i>
                                                <span>Security Question: What is <strong id="num1"><?= $angka1 ?></strong> + <strong id="num2"><?= $angka2 ?></strong>?</span>
                                                <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="refreshCaptcha">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </div>
                                            <input type="number" id="captcha" name="captcha" class="form-control"
                                                placeholder="Enter the answer" required />
                                            <div class="invalid-feedback">
                                                Please solve the math problem correctly.
                                            </div>
                                        </div>



                                        <div class="text-center pt-1 mb-3">
                                            <button type="submit" class="btn btn-primary btn-login w-100 btn-block fa-lg mb-3">
                                                <i class="bi bi-box-arrow-in-right me-2"></i>Log In
                                            </button>
                                        </div>



                                    </form>

                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">
                                        <i class="bi bi-shield-check me-2"></i>
                                        Secure & Reliable System
                                    </h4>
                                    <p class="mb-3">
                                        Our AM System provides a secure and user-friendly interface for managing your account.
                                        With advanced security features and intuitive design, we ensure your data is protected
                                        while providing seamless access to your dashboard.
                                    </p>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <small>Multi-layer security protection</small>
                                    </div>
                                    <div class="d-flex align-items-center mt-1">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <small>24/7 system monitoring</small>
                                    </div>
                                    <div class="d-flex align-items-center mt-1">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <small>Regular security updates</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>

</html>