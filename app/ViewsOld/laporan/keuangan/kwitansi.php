<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran Siswa</title>
    <style>
    body {
        font-family: "Arial", sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 0;
    }

    .header {
        padding: 15px;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }

    .logo {
        float: left;
        width: 60px;
        height: 60px;
    }

    .school-info {
        text-align: center;
    }

    .section {
        margin: 15px 10px;
        padding: 10px;
        border: 1px solid #FF8C00;
        border-radius: 5px;
    }

    .section h4 {
        margin: 0 0 5px 0;
        color: #FF8C00;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
    }

    table,
    th,
    td {
        border: 1px solid #FF8C00;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #FFA500;
        color: white;
    }

    .totals {
        margin-top: 10px;
        padding: 10px;
        border: 1px solid #FF8C00;
        border-radius: 5px;
        background-color: #FFE5B4;
    }

    .totals b {
        font-size: 14px;
    }

    .footer {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }

    .footer div {
        text-align: center;
    }

    .footer .signature {
        margin-top: 40px;
        border-top: 1px solid #000;
        width: 200px;
        margin-left: auto;
        margin-right: auto;
    }

    .title-kwitansi {
        background-color: #FF8C00;
        /* dominan orange */
        color: white;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        padding: 15px;
        margin-top: 5px;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <div class="header">
        <?= view('laporan/koplaporan'); ?>
    </div>

    <div class="title-kwitansi">
        KWITANSI PEMBAYARAN SISWA
    </div>


    <div class="section">
        <h4>Informasi Siswa</h4>
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <tr>
                <th style="padding: 3px; text-align: left;">No Transaksi</th>
                <td style="padding: 3px;"><?= $pembayaran['ID_PEMBAYARAN'] ?></td>
                <th style="padding: 3px; text-align: left;">NIY / NISN</th>
                <td style="padding: 3px;"><?= $pembayaran['NIY'] ?> / <?= $pembayaran['NISN'] ?></td>
            </tr>
            <tr>
                <th style="padding: 3px; text-align: left;">Nama Siswa</th>
                <td style="padding: 3px;"><?= $pembayaran['NAMA_SISWA'] ?></td>
                <th style="padding: 3px; text-align: left;">Pendidikan Saat Ini</th>
                <td style="padding: 3px;"><?= $pembayaran['NAMA_SEKOLAH'] ?></td>
            </tr>
            <tr>
                <th style="padding: 3px; text-align: left;">Tanggal</th>
                <td style="padding: 3px;"><?= $pembayaran['TANGGAL'] ?></td>
                <th style="padding: 3px; text-align: left;">Jam Cetak</th>
                <td style="padding: 3px;"><?= date('H:i') ?> WIB</td>
            </tr>

        </table>


    </div>

    <div class="section">
        <h4>Pembayaran</h4>
        <table style="font-size: 11px;">
            <tr>
                <td colspan="6" style="text-align: left; font-size: 14px;">
                    Kode/Rekening: <?= $pembayaran['NO_REKENING'] ?><br>
                    Kas/Bank : <?= $pembayaran['NAMA_BANK'] ?><br>
                    Keterangan: <?= $pembayaran['CATATAN'] ?>
                </td>
            </tr>

            <tr>
                <th style="text-align: center;">Jenis Pembayaran</th>
                <th style="text-align: center;">Tenor (x)</th>
                <th style="text-align: center;">Kewajiban</th>
                <th style="text-align: center;">Pembayaran Saat Ini</th>
                <th style="text-align: center;">Total Dibayar</th>
                <th style="text-align: center;">Sisa</th>
            </tr>

            <tr>
                <td><?= $pembayaran['GROUP_JENIS_PENERIMAAN'] ?></td>
                <td style="text-align: center;"><?= $pembayaran['GROUP_TENOR'] ?></td>
                <td style="text-align: right;">Rp <?= number_format($pembayaran['GROUP_JUMLAH_MASTER'],0,',','.') ?>
                </td>
                <td style="text-align: right;">Rp <?= number_format($pembayaran['JUMLAH_RINCIAN_DIBAYAR'],0,',','.') ?>
                </td>
                <td style="text-align: right;">Rp
                    <?= number_format($pembayaran['GROUP_JUMLAH_RINCIAN_DIBAYAR'],0,',','.') ?></td>

                <td style="text-align: right;">Rp <?= number_format($pembayaran['SISA_DIBAYAR'],0,',','.') ?></td>
            </tr>
            <tr>
                <td colspan="4" style="
                    text-align: right;
                    font-style: italic;
                    font-size: 14px;
                    padding: 8px;
                    background-color: #FFF8DC; /* Warna soft cream agar beda */
                    border: 1px dotted #FF8C00; /* Bintik-bintik oranye */
                    border-radius: 5px;
                ">
                    Terbilang: <?= terbilang($pembayaran['JUMLAH_RINCIAN_DIBAYAR']) ?> Rupiah
                </td>
                <td colspan="2" style="text-align: center; font-weight: bold; font-size: 14px;">
                    <?= $pembayaran['GROUP_STATUS_BAYAR'] ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="totals">
        <span>Total Dibayarkan: Rp <?= number_format($pembayaran['JUMLAH_RINCIAN_DIBAYAR'],0,',','.') ?></span><br>
        <span>Terbilang: <?= terbilang($pembayaran['GROUP_JUMLAH_RINCIAN_DIBAYAR']) ?> Rupiah</span>
    </div>

    <table style="width: 100%; margin-top: 30px; border: none;">
        <tr>
            <td style="width: 50%; text-align: left; border: none;">
                Gorontalo, <?= date('d F Y') ?><br>
                Yang Menerima
                <br><br><br>
                <div class="signature"><?= strtoupper($pembayaran['NAMA_PENGGUNA_PEMBAYARAN']) ?></div>
            </td>
            <td style="width: 50%; text-align: right; border: none;">
                Orang Tua / Wali:
                <br><br><br>
                <div class="signature">
                    <?= strtoupper($pembayaran['NAMA_AYAH']) ?? strtoupper($pembayaran['NAMA_IBU']) ?? 'Orang Tua / Wali' ?>
                </div>

            </td>
        </tr>
    </table>

</body>

</html>