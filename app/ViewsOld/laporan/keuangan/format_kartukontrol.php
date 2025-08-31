<style>
body {
    font-family: sans-serif;
    font-size: 12px;
}

table {
    border-collapse: collapse;
    width: 100%;
}

th,
td {
    border: 1px solid #000;
    padding: 4px;
}

th {
    background-color: #f2f2f2;
}

.table-secondary {
    background-color: #e9ecef;
}

.fw-bold {
    font-weight: bold;
}

.text-center {
    text-align: center;
}

.text-end {
    text-align: right;
}

.small {
    font-size: 10px;
}

.text-success {
    color: green;
}

.text-danger {
    color: red;
}

.text-info {
    color: blue;
}

.bg-school {
    background-color: #f7edc1ff;
}
</style>

<?= view('laporan/koplaporan'); ?>

<h3 style="text-align: center;">KARTU KONTROL PEMBAYARAN SISWA</h3>

<!-- informasi siswa -->
<table style="border-collapse: collapse; border: none; width: 100%;">
    <tr>
        <td style="border: none; width: 25%;">Nomor Induk Yayasan (NIY)</td>
        <td style="border: none;">: <?= esc($siswa['NIY'] ?? '-') ?></td>
    </tr>
    <tr>
        <td style="border: none;">Nomor Induk Siswa (NIS)</td>
        <td style="border: none;">: <?= esc($siswa['NISN'] ?? '-') ?></td>
    </tr>
    <tr>
        <td style="border: none;">Nama Siswa</td>
        <td style="border: none;">: <?= esc($siswa['NAMA_SISWA'] ?? '-') ?></td>
    </tr>
    <tr>
        <td style="border: none;">Pendidikan Saat Ini</td>
        <td style="border: none;">: <?= esc($siswa['NAMA_SEKOLAH'] ?? '-') ?></td>
    </tr>
</table>

<br>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Jumlah</th>
            <th>Bank</th>
            <th>Catatan</th>
            <th>Petugas</th>
        </tr>
    </thead>
    <tbody>
        <?php 
$totalKeseluruhan = 0;
foreach ($grouped as $pendidikan => $jenisList): ?>
        <tr class="fw-bold bg-school small">
            <td colspan="8">
                PENDIDIKAN: <?= esc($pendidikan) ?>
            </td>
        </tr>

        <?php foreach ($jenisList as $jenis => $data): ?>
        <tr class="fw-bold table-secondary small">
            <td colspan="8" style="display: flex; justify-content: space-between;">
                <div>
                    >> <?= esc($jenis) ?>
                    <span class="text-danger">Rp <?= number_format($data['JUMLAH_TOTAL'], 0, ',', '.') ?></span> -
                    <span class="text-info">Rp <?= number_format($data['TELAH_DIBAYAR'], 0, ',', '.') ?></span>
                    <?= $data['SISA_DIBAYAR'] > 0 
                        ? '(Sisa: Rp '.number_format($data['SISA_DIBAYAR'], 0, ',', '.').')' 
                        : '(<span class="text-success">Lunas</span>)' ?>
                </div>
            </td>
        </tr>

        <?php 
        $totalPerJenis = 0;
        foreach ($data['items'] as $i => $item): 
            $totalPerJenis += $item['JUMLAH'];
            $totalKeseluruhan += $item['JUMLAH'];
        ?>
        <tr>
            <td class="text-center small"><?= $i + 1 ?></td>
            <td class="text-center small"><?= $item['TANGGAL'] ? date('d/m/Y', strtotime($item['TANGGAL'])) : '-' ?>
            </td>
            <td class="text-center small"><?= esc($item['BULAN_TAGIHAN'] ?? '-') ?></td>
            <td class="text-center small"><?= esc($item['TAHUN_TAGIHAN'] ?? '-') ?></td>
            <td class="text-end small">Rp <?= number_format($item['JUMLAH'], 0, ',', '.') ?></td>
            <td class="small"><?= esc($item['NAMA_BANK'] ?? 'Tunai') ?></td>
            <td class="small"><?= esc($item['CATATAN'] ?? '-') ?></td>
            <td class="small"><?= esc($item['NAMA_PENGGUNA'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>

        <tr class="fw-bold text-info small">
            <td colspan="4" class="text-end">Total <?= esc($jenis) ?></td>
            <td class="text-end">Rp <?= number_format($totalPerJenis, 0, ',', '.') ?></td>
            <td colspan="3">Terbilang: <?= terbilang($totalPerJenis) ?> Rupiah</td>
        </tr>

        <?php endforeach; ?>
        <?php endforeach; ?>

        <tr class="fw-bold table-info small">
            <td colspan="4" class="text-end">Total Keseluruhan</td>
            <td class="text-end">Rp <?= number_format($totalKeseluruhan, 0, ',', '.') ?></td>
            <td colspan="3">Terbilang: <?= terbilang($totalKeseluruhan) ?> Rupiah</td>
        </tr>
    </tbody>
</table>