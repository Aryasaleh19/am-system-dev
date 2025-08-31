<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kwitansi Kas Masuk</title>
    <style>
    body {
        font-family: "Arial", sans-serif;
        font-size: 12px;
        margin: 20px;
        color: #333;
    }

    .header {
        text-align: center;
        margin-bottom: 10px;
    }

    .title {
        background-color: #FF8C00;
        color: #fff;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        padding: 10px;
        border-radius: 5px;
        margin: 15px 0;
    }

    .section {
        border: 1px solid #FF8C00;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 15px;
    }

    .section h4 {
        margin: 0 0 5px 0;
        color: #FF8C00;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    table,
    th,
    td {
        border: 1px solid #FF8C00;
    }

    th {
        background-color: #FFA500;
        color: white;
        padding: 6px;
        text-align: left;
    }

    td {
        padding: 6px;
    }

    .totals {
        background-color: #FFF8DC;
        border: 1px solid #FF8C00;
        border-radius: 5px;
        padding: 10px;
        margin-top: 10px;
        font-size: 13px;
    }

    .totals b {
        font-size: 14px;
        color: #FF8C00;
    }

    .signature-block {
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
        font-size: 12px;
    }

    .signature {
        text-align: center;
        width: 40%;
    }

    .footer-note {
        margin-top: 15px;
        font-size: 10px;
        color: #666;
        text-align: center;
        font-style: italic;
    }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <?= view('laporan/koplaporan'); ?>
    </div>

    <!-- Title -->
    <div class="title">KWITANSI PENERIMAAN KAS</div>

    <!-- Informasi Transaksi -->
    <div class="section">
        <h4>Informasi Transaksi</h4>
        <table>
            <tr>
                <th>No Transaksi</th>
                <td><?= $kas['ID'] ?></td>
                <th>Tanggal</th>
                <td><?= shortdate_indo($kas['TANGGAL']) ?></td>
            </tr>
            <tr>
                <th>Jenis Penerimaan</th>
                <td><?= $kas['JENIS_PENERIMAAN'] ?></td>
                <th>Diterima Dari</th>
                <td><?= $kas['DITERIMA_DARI'] ?></td>
            </tr>
            <tr>
                <th>Kas/Bank</th>
                <td><?= $kas['NO_REKENING'].' - '.$kas['NAMA_BANK'] ?></td>
                <th>Bukti</th>
                <td><?= $kas['BUKTI'] ?: '-' ?></td>
            </tr>
        </table>
    </div>

    <!-- Detail Penerimaan -->
    <div class="section">
        <h4>Detail Penerimaan</h4>
        <table>
            <tr>
                <th style="text-align:center;">Jenis Penerimaan</th>
                <th style="text-align:center;">Jumlah</th>
                <th style="text-align:center;">Keterangan</th>
            </tr>
            <tr>
                <td style="text-align: center; font-weight: bold;"><?= $kas['JENIS_PENERIMAAN'] ?></td>
                <td style="text-align: center; font-weight: bold;">Rp <?= number_format($kas['JUMLAH'],0,',','.') ?>
                </td>
                <td><?= $kas['KETERANGAN'] ?? '-' ?></td>
            </tr>
        </table>

        <div class="totals">
            <b>Total Diterima: Rp <?= number_format($kas['JUMLAH'],0,',','.') ?></b><br>
            <span style="font-style: italic;">Terbilang: <?= ucfirst($terbilang) ?> rupiah</span>
        </div>

        <table style="width: 100%; margin-top: 30px; border: none;">
            <tr>
                <td style="width: 50%; text-align: center; border: none;">
                    <br><br><br>
                    <div class="signature"></div>
                </td>
                <td style="width: 50%; text-align: center; border: none;">
                    Gorontalo, <?= shortdate_indo(date('Y-m-d')) ?><br>
                    Yang Menerima/Petugas
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="signature">(<?= $kas['NAMA_OLEH'] ?>)</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        Kwitansi ini dicetak otomatis oleh sistem, sah tanpa tanda tangan basah.
    </div>

</body>

</html>