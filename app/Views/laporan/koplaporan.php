<div style="text-align: center;">
    <img src="assets/img/<?= session()->get('LOGO'); ?>" alt="Logo"
        style="max-width:70px; max-height:70px; border:none;">
    <br>
    <h2 style="margin:0;"><?= session()->get('NAMA_LEMBAGA'); ?></h2>
    <p style="margin:0; font-size:12px;">
        <?= session()->get('ALAMAT'); ?><br>
        Telp: <?= session()->get('TELP'); ?> | Fax: <?= session()->get('FAX'); ?><br>
        Email: <?= session()->get('EMAIL'); ?>
    </p>
</div>
<hr style="margin:0; border-color: #000;">