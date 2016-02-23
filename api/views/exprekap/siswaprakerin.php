<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-siswa-prakerin.xls");
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
<table border="1" style="border-collapse: collapse;width:100%">
    <tr>
         <?php
        if (!isset($_GET['print'])) {
        ?>
        <td colspan="4" rowspan="2" style="width:60%;text-align: center">
            <h4>DATA SISWA PRAKERIN</h4><br/>
            <span>PERIODE : <?= date('d F Y',strtotime($start)).' S/D '.date('d F Y',strtotime($end)); ?></span>
        </td>
        <?php
        }else{
        ?>
        <td colspan="3" rowspan="2" style="width:60%;text-align: center">
            <h4>DATA SISWA PRAKERIN</h4><br/>
            <span>PERIODE : <?= date('d F Y',strtotime($start)).' S/D '.date('d F Y',strtotime($end)); ?></span>
        </td>
        <?php
        }
        ?>
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
            <th style="text-align: center;vertical-align: center;">NO</th>
            <th style="text-align: center;vertical-align: center;">NAMA</th>
            <th style="text-align: center;vertical-align: center;">BAGIAN</th>
            <th style="text-align: center;vertical-align: center;">TANGGAL MASUK</th>
            <th style="text-align: center;vertical-align: center;">TANGGAL KELUAR</th>
            <th style="text-align: center;vertical-align: center;">ASAL SEKOLAH</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . $val['nama'] . '</td>';
            echo '<td align="center">' . $val['bagian'] . '</td>';
            echo '<td align="center">' . $val['tgl_mulai'] . '</td>';
            echo '<td align="center">' . $val['tgl_selesai'] . '</td>';
            echo '<td align="center">' . $val['asal_sekolah'] . '</td>';
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