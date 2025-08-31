<?= $this->include('templates/header') ?>
<?= $this->include('templates/menu') ?>

<!-- Layout Page -->
<div class="layout-page">
    <?= $this->include('templates/navbar') ?>

    <div class="content-wrapper">

        <div id="preloader"
            style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,0.7); z-index:9999; text-align:center; padding-top:20%;">
            <div class="spinner-border text-info" role="status" style="width:3rem;height:3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-info">Memuat data...</p>
        </div>

        <?= $this->renderSection('content') ?>
    </div>

    <!-- info toast -->
    <div id="toastContainer"
        style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 500000;">
    </div>




</div>

<?= $this->include('templates/footer') ?>