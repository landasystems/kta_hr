<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-jadwal-hse.xls");
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
<center><h4>LAPORAN KARYAWAN SPD</h4><br/></center>
<span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span>

<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th style="text-align: center;vertical-align: center;">NO</th>
            <th style="text-align: center;vertical-align: center;">NO SPD</th>
            <th style="text-align: center;vertical-align: center;">TANGGAL BERANGKAT</th>
            <th style="text-align: center;vertical-align: center;">TANGGAL KEMBALI</th>
            <th style="text-align: center;vertical-align: center;">NIK</th>
            <th style="text-align: center;vertical-align: center;">NAMA</th>
            <th style="text-align: center;vertical-align: center;">TUJUAN</th>
            <th style="text-align: center;vertical-align: center;">KEPERLUAN</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . $val['no_spd'] . '</td>';
            echo '<td align="center">' . $val['tgl_berangkat'] . '</td>';
            echo '<td align="center">' . $val['tgl_kembali'] . '</td>';
            echo '<td align="center">' . $val['nik'] . '</td>';
            echo '<td align="center">' . $val['nama'] . '</td>';
            echo '<td align="center">' . $val['tujuan'] . '</td>';
            echo '<td align="center">' . $val['keperluan'] . '</td>';
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