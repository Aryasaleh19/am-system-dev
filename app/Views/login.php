<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Al-Muhajirin System Login</title>
    <!-- <link rel="icon" type="image/x-icon" href="<?= base_url() ?><?= session()->get('LOGO') ?>" /> -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-title {
            color: #f39c12;
            font-weight: 600;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-label {
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #f39c12;
            box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.25);
        }

        .input-group {
            margin-bottom: 1rem;
        }

        .btn-login {
            background: linear-gradient(45deg, #f39c12, #e67e22);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(45deg, #e67e22, #d35400);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(243, 156, 18, 0.3);
        }

        .security-question {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #f39c12;
        }

        .security-question h6 {
            color: #495057;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .math-question {
            font-size: 1.1rem;
            font-weight: 500;
            color: #212529;
        }

        .refresh-btn {
            border: none;
            background: transparent;
            color: #6c757d;
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .refresh-btn:hover {
            color: #f39c12;
        }

        .password-toggle {
            background: transparent;
            border: none;
            color: #6c757d;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #f39c12;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2 class="login-title">
            <i class="bi bi-shield-check me-2"></i>
            Al-Muhajirin System Login
        </h2>

        <?php if (session()->getFlashdata('error')) : ?>
            <div id="loginAlert" class="alert alert-danger " role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span id="alertMessage"><?= session()->getFlashdata('error'); ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('login') ?>">
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-person me-1"></i>Username
                </label>
                <input type="text" class="form-control" id="username" value="sumadi" name="email-username">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="bi bi-lock me-1"></i>Password
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" id="password" value="........">
                    <button class="btn password-toggle" type="button" onclick="togglePassword()">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="captcha-container">
                <div class="mb-2 captcha-question">
                    <?php
                    $angka1 = rand(1, 10);
                    $angka2 = rand(1, 10);
                    $captcha_result = $angka1 + $angka2;
                    session()->set('captcha_login', $captcha_result);
                    ?>
                    <i class="bi bi-calculator text-primary-custom"></i>
                    <span>Security Question: What is <strong id="num1"><?= $angka1 ?></strong> + <strong id="num2"><?= $angka2 ?></strong>?</span>
                    <!-- <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="refreshCaptcha"> -->
                    <!-- <i class="bi bi-arrow-clockwise"></i> -->
                    </button>
                </div>
                <input type="number" id="captcha" name="captcha" class="form-control"
                    placeholder="Enter the answer" required />
                <div class="invalid-feedback">
                    Please solve the math problem correctly.
                </div>
            </div>

            <button type="submit" class="mt-3 btn btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Log In
            </button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }

        function generateNewQuestion() {
            const num1 = Math.floor(Math.random() * 20) + 1;
            const num2 = Math.floor(Math.random() * 20) + 1;
            const operators = ['+', '-', '*'];
            const operator = operators[Math.floor(Math.random() * operators.length)];

            document.getElementById('mathQuestion').textContent = `${num1} ${operator} ${num2}`;
            document.getElementById('securityAnswer').value = '';
        }
    </script>
</body>

</html>