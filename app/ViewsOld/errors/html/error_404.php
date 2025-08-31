<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title><?= lang('Errors.pageNotFound') ?></title>
    <style>
    body {
        margin: 0;
        padding: 0;
        background: #f4f6f8;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
    }

    .container {
        max-width: 600px;
        margin: 80px auto;
        padding: 40px;
        background-color: #fff;
        text-align: center;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    h1 {
        font-size: 72px;
        margin-bottom: 10px;
        color: #e74c3c;
    }

    h2 {
        font-size: 24px;
        font-weight: normal;
        margin-bottom: 20px;
    }

    p {
        font-size: 16px;
        color: #666;
        margin-bottom: 30px;
    }

    a.button {
        display: inline-block;
        padding: 12px 24px;
        background-color: #3498db;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.3s;
    }

    a.button:hover {
        background-color: #2980b9;
    }

    .footer {
        margin-top: 40px;
        font-size: 14px;
        color: #999;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>404</h1>
        <h2>Halaman tidak ditemukan!</h2>

        <div class="alert alert-warning">
            <strong>Maintenance</strong> Halaman ini masih dalam tahap pengembangan.
        </div>


        <p>
            <?php if (ENVIRONMENT !== 'production') : ?>
            <?= nl2br(esc($message)) ?>
            <?php else : ?>
            <?= lang('Errors.sorryCannotFind') ?>
            <?php endif; ?>
        </p>

        <a href="<?= base_url() ?>" class="button">⬅️ Kembali ke Halaman Utama</a>

        <div class="footer">
            &mdash; By Tim MokarajaNET
        </div>
    </div>
</body>

</html>