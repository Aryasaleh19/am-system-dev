<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran Siswa</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 20px;
    }

    h2 {
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        table-layout: fixed;
        /* kolom tetap */
    }

    table,
    th,
    td {
        border: 1px solid #000;
    }

    th {
        background-color: #f2f2f2;
        text-align: center;
        padding: 5px;
        word-wrap: break-word;
    }

    td {
        padding: 5px;
        vertical-align: top;
        word-wrap: break-word;
    }

    .text-right {
        text-align: right;
    }

    .sub-total {
        font-weight: bold;
        background-color: #e6f7ff;
    }

    .total {
        font-weight: bold;
        background-color: #d9ead3;
    }

    /* Tetapkan lebar kolom */
    th:nth-child(1),
    td:nth-child(1) {
        width: 5%;
    }

    th:nth-child(2),
    td:nth-child(2) {
        width: 15%;
    }

    th:nth-child(3),
    td:nth-child(3) {
        width: 12%;
    }

    th:nth-child(4),
    td:nth-child(4) {
        width: 8%;
    }

    th:nth-child(n+5):nth-child(-n+16),
    td:nth-child(n+5):nth-child(-n+16) {
        width: 5%;
    }

    /* 12 bulan */
    th:nth-child(17),
    td:nth-child(17) {
        width: 8%;
    }

    /* Total */
    th:nth-child(18),
    td:nth-child(18) {
        width: 8%;
    }

    /* Sisa */
    </style>
</head>

<body>
    <?= view('laporan/koplaporan'); ?>
    <br>
    <br>
    <h2>LAPORAN PEMBAYARAN SISWA</h2>

    <table style="margin-bottom: 10px; width: 50%; border: none; border-collapse: collapse;">
        <tr>
            <td style="border: none; width: 30%; white-space: nowrap;">Siswa Angkatan</td>
            <td style="border: none;">: <?= $angkatan ?? 'All' ?></td>
        </tr>
        <tr>
            <td style="border: none; white-space: nowrap;">Departemen</td>
            <td style="border: none;">: <?= $departemen ?? 'SDIT' ?></td>
        </tr>
        <tr>
            <td style="border: none; white-space: nowrap;">Tahun Pembayaran</td>
            <td style="border: none;">: <?= $tahun ?? '2025' ?></td>
        </tr>
        <tr>
            <td style="border: none; white-space: nowrap;">Bank Penerima</td>
            <td style="border: none;">: <?= $bank ?? 'All' ?></td>
        </tr>
        <tr>
            <td style="border: none; white-space: nowrap;">Jenis Penerimaan</td>
            <td style="border: none;">: <?= $jenisPembayaran ?? 'All' ?></td>
        </tr>
    </table>



    <table>
        <tr>
            <th>NO</th>
            <th>NAMA SISWA</th>
            <th>JENIS PEMBAYARAN</th>
            <th>KEWAJIBAN</th>
            <th>JAN</th>
            <th>FEB</th>
            <th>MAR</th>
            <th>APR</th>
            <th>MEI</th>
            <th>JUNI</th>
            <th>JULI</th>
            <th>AGU</th>
            <th>SEP</th>
            <th>OKT</th>
            <th>NOV</th>
            <th>DES</th>
            <th>TOTAL</th>
            <th>SISA DIBAYAR</th>
            <th>STATUS</th>
        </tr>

        <?php 
        $no = 1; 
        $grandTotal = array_fill(0, 15, 0); // kewajiban+12bulan+total+sisa
        foreach($dataSiswa as $siswa): 
            $rowCount = count($siswa['pembayaran']); 
            $subTotal = array_fill(0, 15, 0);
        ?>
        <?php foreach($siswa['pembayaran'] as $i => $bayar): ?>
        <tr>
            <?php if($i==0): ?>
            <td style="text-align: center;" rowspan="<?= $rowCount ?>"><?= $no ?></td>
            <td rowspan="<?= $rowCount ?>"><?= $siswa['nama'] ?></td>
            <?php endif; ?>
            <td><?= $bayar['jenis'] ?></td>
            <td class="text-right"><?= number_format($bayar['kewajiban'],0,",",".") ?></td>
            <?php 
                foreach($bayar['bulan'] as $b){ 
                    echo '<td class="text-right">'.number_format($b,0,",",".").'</td>'; 
                } 
            ?>
            <td class="text-right"><?= number_format($bayar['total'],0,",",".") ?></td>
            <td class="text-right"><?= number_format($bayar['sisa'],0,",",".") ?></td>
            <td style="text-align: center;"><?= $bayar['status'] ?></td>
        </tr>
        <?php 
            // hitung subtotal per siswa
            $subTotal[0] += $bayar['kewajiban'];
            foreach($bayar['bulan'] as $k => $b){ $subTotal[$k+1] += $b; }
            $subTotal[13] += $bayar['total'];
            $subTotal[14] += $bayar['sisa'];
        ?>
        <?php endforeach; ?>

        <tr class="sub-total">
            <td colspan="3" class="text-right">Sub Total:</td>
            <?php foreach($subTotal as $st): ?>
            <td class="text-right"><?= number_format($st,0,",",".") ?></td>
            <?php endforeach; ?>
        </tr>

        <?php 
            // simpan ke grand total
            foreach($subTotal as $k => $v){ $grandTotal[$k] += $v; }
            $no++;
        endforeach; 
        ?>

        <tr class="total" style="font-weight: bold;">
            <td colspan="3" class="text-right">Total :</td>
            <?php foreach($grandTotal as $gt): ?>
            <td class="text-right"><?= number_format($gt,0,",",".") ?></td>
            <?php endforeach; ?>
        </tr>
    </table>

</body>

</html>