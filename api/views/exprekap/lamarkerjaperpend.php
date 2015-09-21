<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-retur-Pergerakan-Barang.xls");
}

$start = $params['tanggal']['startDate'];
$end = $params['tanggal']['endDate'];
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
<table border="1" style="border-collapse: collapse;width:100%" width="100%">
    <tr>
        <td colspan="8" rowspan="2" style="width:60%;text-align: center">
            <h4>DATA LAMARAN KERJA</h4><br/>
            <span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span>
        </td>
        <td style="text-align: center">Dibuat</td>
        <td style="text-align: center">Diketahui</td>
    </tr>
    <tr>
        <td style="height: 45px;"></td>
        <td></td>
    </tr>
</table>
<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th rowspan="3" style="text-align: center;vertical-align: center;">No</th>
            <th rowspan="3" style="text-align: center;vertical-align: center;">Tanggal</th>
            <th rowspan="3" style="text-align: center;vertical-align: center;">Untuk Posisi</th>
            <th rowspan="3" style="text-align: center;vertical-align: center;">Nama Lengkap</th>
            <th rowspan="3" style="text-align: center;vertical-align: center;">Jurusan</th>
            <th rowspan="3" style="text-align: center;vertical-align: center;">Pendidikan Terakhir</th>
            <th rowspan="3" style="text-align: center;vertical-align: center;">Informal</th>
            <th colspan="3" style="text-align: center;vertical-align: center;">Pengalaman Kerja</th>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center;">Perusahaan</th>
            <th style="text-align: center;vertical-align: center;">Perusahaan</th>
            <th style="text-align: center;vertical-align: center;">Perusahaan</th>

        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center;">Bagian</th>
            <th style="text-align: center;vertical-align: center;">Bagian</th>
            <th style="text-align: center;vertical-align: center;">Bagian</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . date('d/F/Y', strtotime($val['tgl'])) . '</td>';
            echo '<td align="center">' . $val['posisi'] . '</td>';
            echo '<td align="center">' . $val['nama'] . '</td>';
            echo '<td align="center">' . $val['jurusan'] . '</td>';
            echo '<td align="center">' . $val['pendidikan'] . '</td>';
            echo '<td align="center">' . $val['informal'] . '</td>';
            echo '<td align="center">' . $val['pk_perusahaan1'] . '<br>' . $val['pk_bagian1'] . '</td>';
            echo '<td align="center">' . $val['pk_perusahaan2'] . '<br>' . $val['pk_bagian2'] . '</td>';
            echo '<td align="center">' . $val['pk_perusahaan3'] . '<br>' . $val['pk_bagian3'] . '</td>';
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