<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-karyawan-masuk-pergaji.xls");
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
<center><h3>REKAP KARYAWAN MASUK PER GAJI</h3><br/></center>
<span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span>

<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th  style="text-align: center;vertical-align: center;">No</th>
            <th  style="text-align: center;vertical-align: center;">NIK</th>
            <th  style="text-align: center;vertical-align: center;">NAMA LENGKAP</th>
            <th  style="text-align: center;vertical-align: center;">KODE BANK</th>
            <th  style="text-align: center;vertical-align: center;">GAJI POKOK</th>
            <th  style="text-align: center;vertical-align: center;">TUNJANGAN FUNGSIONAL</th>
            <th  style="text-align: center;vertical-align: center;">TUNJANGAN KEHADIRAN</th>
            <th  style="text-align: center;vertical-align: center;">THP 2013</th>
            <th  style="text-align: center;vertical-align: center;">UPAH TETAP</th>
            <th  style="text-align: center;vertical-align: center;">PESANGON</th>
            <th  style="text-align: center;vertical-align: center;">TUNJANGAN MASA KERJA</th>
            <th  style="text-align: center;vertical-align: center;">PENGGATIAN HAK</th>
            <th  style="text-align: center;vertical-align: center;">NORMATIF</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . $val['nik'] . '</td>';
            echo '<td align="center">' . $val['nama'] . '</td>';
            echo '<td align="center">' . $val['kode_bank'] . '</td>';
            echo '<td align="center">' . $val['gaji_pokok'] . '</td>';
            echo '<td align="center">' . $val['t_fungsional'] . '</td>';
            echo '<td align="center">' . $val['t_kehadiran'] . '</td>';
            echo '<td align="center">' . $val['thp'] . '</td>';
            echo '<td align="center">' . $val['upah_tetap'] . '</td>';
            echo '<td align="center">' . $val['pesangon'] . '</td>';
            echo '<td align="center">' . $val['t_masa_kerja'] . '</td>';
            echo '<td align="center">' . $val['penggantian_hak'] . '</td>';
            echo '<td align="center">' . $val['normatif'] . '</td>';
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