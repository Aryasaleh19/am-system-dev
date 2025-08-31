<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Harian Petugas</title>
    <style>
    body {
        font-family: sans-serif;
        font-size: 13px;
    }

    h3 {
        margin: 0;
        padding: 0;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 10px;
    }

    td {
        border: 1px solid #000;
        padding: 5px;
        font-size: 11px;
    }

    th {
        background: #f0f0f0;
        border: 1px solid #000;
        padding: 10px;
        font-size: 11px;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .no-border td {
        border: none;
    }
    </style>
</head>



<body>
    <?= view('laporan/koplaporan'); ?>
    <br>
    <br>
    <h3 style="text-align:center;">LAPORAN REKAPITULASI TRANSAKSI HARIAN PETUGAS</h3>
    <br>

    <table class="no-border">
        <tr>
            <td width="20%">PETUGAS / ADMIN</td>
            <td>: <?= $petugas ?></td>
        </tr>
        <tr>
            <td>PERIODE</td>
            <td>: <?= $header['PERIODE_HARIAN'] ?? $tanggal ?></td>
        </tr>
    </table>

    <br>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>NO.TRANSAKSI</th>
                <th>TANGGAL</th>
                <th>JENIS TRANSAKSI</th>
                <th>URAIAN TRANSAKSI</th>
                <th>KAS MASUK</th>
                <th>KAS KELUAR</th>
                <th>KAS BANK</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($transaksi as $row): 
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $row['NO_TRANSAKSI'] ?></td>
                <td><?= $row['TANGGAL'] ?></td>
                <td><?= $row['JENIS_TRANSAKSI'] ?></td>
                <td><?= $row['URAIAN'] ?></td>
                <td class="text-right"><?= number_format($row['KAS_MASUK'],0,",",".") ?></td>
                <td class="text-right"><?= number_format($row['KAS_KELUAR'],0,",",".") ?></td>
                <td><?= $row['KAS_BANK'] ?></td>
                <td class="text-right"><?= number_format($row['JUMLAH'],0,",",".") ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">TOTAL TRANSAKSI HARIAN :</th>
                <th class="text-right"><?= number_format($rekap['TOTAL_KAS_MASUK'] ?? 0,0,",",".") ?></th>
                <th class="text-right"><?= number_format($rekap['TOTAL_KAS_KELUAR'] ?? 0,0,",",".") ?></th>
                <th></th>
                <th class="text-right"><?= number_format($rekap['TOTAL_TRANSAKSI'] ?? 0,0,",",".") ?></th>
            </tr>
        </tfoot>
    </table>

</body>

</html>