<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-monitoring-stnk.xls");
}

//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
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
<div style="text-align: right">Dicetak: <?= date('d F Y'); ?></div>
<center><h4>LAPORAN MONITORING STNK</h4><br/></center>
<span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span>

<table width="70%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th style="text-align: center;vertical-align: center;">NO</th>
            <th style="text-align: center;vertical-align: center;">MASA BERLAKU</th>
            <th style="text-align: center;vertical-align: center;">NOPOL</th>
            <th style="text-align: center;vertical-align: center;">MERK</th>
            <th style="text-align: center;vertical-align: center;">TYPE</th>
            <th style="text-align: center;vertical-align: center;">MODEL</th>
            <th style="text-align: center;vertical-align: center;">WARNA</th>
            <th style="text-align: center;vertical-align: center;">TAHUN PEMBUATAN</th>
            <th style="text-align: center;vertical-align: center;">NO RANGKA</th>
            <th style="text-align: center;vertical-align: center;">NO MESIN</th>
            <th style="text-align: center;vertical-align: center;">USER</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . $val['masa_berlaku'] . '</td>';
            echo '<td align="center">' . $val['nopol'] . '</td>';
            echo '<td align="center">' . $val['merk'] . '</td>';
            echo '<td align="center">' . $val['tipe'] . '</td>';
            echo '<td align="center">' . $val['model'] . '</td>';
            echo '<td align="center">' . $val['warna'] . '</td>';
            echo '<td align="center">' . $val['thn_pembuatan'] . '</td>';
            echo '<td align="center">' . $val['no_rangka'] . '</td>';
            echo '<td align="center">' . $val['no_mesin'] . '</td>';
            echo '<td align="center">' . $val['user'] . '</td>';
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