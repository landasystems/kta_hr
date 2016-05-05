<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-karyawan-masuk.xls");
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
<div style="text-align: right">Dicetak: <?= date('d F Y'); ?></div>


<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th colspan="3" rowspan="2">
    <center><h3>REKAP KARYAWAN MASUK PER GAJI</h3></center>
    <span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span>
</th>
<th>Dibuat</th>
<th>Diperiksa</th>
</tr>
<tr>
    <th style="height: 80px;"></th>
    <th></th>
</tr>
<tr>
    <th>NO</th>
    <th>NIK</th>
    <th>NAMA</th>
    <th>JABATAN</th>
    <th>TANGGAL MASUK</th>
</tr>
</thead>
<tbody>
    <?php
    $no = 1;
    foreach ($models as $val) {
        $date = new \DateTime($val['tgl_masuk_kerja'])
        ?>
        <tr>
            <td align="center"><?= $no ?></td>
            <td align="center"><?= $val['nik'] ?></td>
            <td align="center"><?= $val['nama'] ?></td>
            <td align="center"><?= $val['nama_jabatan'] ?></td>
            <td align="center"><?= $date->format("d-M-Y") ?></td>
        </tr>
        <?php
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