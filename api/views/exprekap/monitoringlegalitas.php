<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-monitoring-legalitas.xls");
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
<center><h4>MONITORING FILE LEGALITAS</h4><br/></center>
<span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span>

<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th style="text-align: center;vertical-align: center;">NO</th>
            <th style="text-align: center;vertical-align: center;">MASA BERLAKU</th>
            <th style="text-align: center;vertical-align: center;">NO LEGALITAS</th>
            <th style="text-align: center;vertical-align: center;">LEGALITAS</th>
            <th style="text-align: center;vertical-align: center;">INSTANSI</th>
            <th style="text-align: center;vertical-align: center;">ATAS NAMA</th>
            <th style="text-align: center;vertical-align: center;">JENIS LEGALITAS</th>
            <th style="text-align: center;vertical-align: center;">TANGGAL PENGESAHAN</th>
            <th style="text-align: center;vertical-align: center;">KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $tgl = '';
        foreach ($models as $val) {
            $tgl = (empty($val['tgl_pengesahan']))? '' : $val['tgl_pengesahan'];
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . $val['masa_berlaku'] . '</td>';
            echo '<td align="center">' . $val['no_file'] . '</td>';
            echo '<td align="center">' . $val['nm_file'] . '</td>';
            echo '<td align="center">' . $val['instansi'] . '</td>';
            echo '<td align="center">' . $val['atas_nm'] . '</td>';
            echo '<td align="center">' . $val['jns_legalitas'] . '</td>';
            echo '<td align="center">' . $tgl . '</td>';
            echo '<td align="center">' . $val['ket'] . '</td>';
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