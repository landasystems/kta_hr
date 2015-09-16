<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-retur-Pergerakan-Barang.xls");
}
?>
<div style="font-size: 10px;">
    <table>
        <tr>
            <td rowspan="3" style="width:10% !important;"><img ng-src="img/logo.png" align="left" style="margin-right: 8px"/></td>
            <td style="width:40% !important;font-size: 14px !important;">PT. KARYA TUGAS ANDA</td>
            <td></td>
        </tr>
        <tr>
            <td style="font-size: 10px;">Autobody, Manufacturing - Transport Services</td>
            <td></td>
        </tr>
        <tr>
            <td style="font-size: 10px;">Minning Contractor - Trading Channel</td>
            <td></td>
        </tr>
    </table>
</div>

<br>
<br>
<hr>
<div style="text-align: right">Dicetak: <?= date('d/F/Y'); ?></div>
<table>
    <tr>
        <td>NO KONTRAK</td>
        <td style="text-align: right">:</td>
        <td style="width:35% !important;"><?= $params['no_kontrak'] ?></td>
        <td>NAMA</td>
        <td style="text-align: right">:</td>
        <td><?= $params['nama'] ?></td>
    </tr>
    <tr>
        <td>NIK</td>
        <td style="text-align: right">:</td>
        <td style="width:35% !important;"><?= $params['nik'] ?></td>
        <td>STATUS KARYAWAN</td>
        <td style="text-align: right">:</td>
        <td><?= $params['status_karyawan'] ?></td>
    </tr>
</table>
<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th>NO</th>
            <th>TANGGAL</th>
            <th>KONTRAK</th>
            <th>PENILAIAN KE</th>
            <th>KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . date('d/F/Y', strtotime($val['tgl'])) . '</td>';
            echo '<td align="center">' . $val['nm_kontrak'] . '</td>';
            echo '<td align="center">' . $val['penilaian'] . '</td>';
            echo '<td align="center">' . $val['keterangan'] . '</td>';
            echo '</tr>';
            $no++;
        }
        ?>
    </tbody>
</table>
<?php
if (isset($_GET['print'])) {
    ?>
    <script type="text/javascript">
        window.print();
        setTimeout(function () {
            window.close();
        }, 1);
    </script>
    <?php
}
?>