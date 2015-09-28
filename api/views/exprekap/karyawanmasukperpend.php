<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-retur-Pergerakan-Barang.xls");
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
<center><h4>DATA KARYAWAN MASUK PER PENDIDIKAN</h4><br/></center>
<span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span>

<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th style="text-align: center;vertical-align: center;">NO</th>
            <th style="text-align: center;vertical-align: center;">NIK</th>
            <th style="text-align: center;vertical-align: center;">NAMA LENGKAP</th>
            <th style="text-align: center;vertical-align: center;">PENDIDIKAN</th>
            <th style="text-align: center;vertical-align: center;">SEKOLAH</th>
            <th style="text-align: center;vertical-align: center;">JURUSAN</th>
            <th style="text-align: center;vertical-align: center;">NO IJAZAH</th>
            <th style="text-align: center;vertical-align: center;">TEMPAT LAHIR</th>
            <th style="text-align: center;vertical-align: center;">TANGGAL LAHIR</th>
            <th style="text-align: center;vertical-align: center;">BLN LAHIR</th>
            <th style="text-align: center;vertical-align: center;">USIA</th>
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
            echo '<td align="center">' . $val['pendidikan'] . '</td>';
            echo '<td align="center">' . $val['sekolah'] . '</td>';
            echo '<td align="center">' . $val['jurusan'] . '</td>';
            echo '<td align="center">' . $val['no_ijazah'] . '</td>';
            echo '<td align="center">' . $val['tmpt_lahir'] . '</td>';
            echo '<td align="center">' . $val['tgl_lahir'] . '</td>';
            echo '<td align="center">' . $val['bulan_lahir'] . '</td>';
            echo '<td align="center"></td>';
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