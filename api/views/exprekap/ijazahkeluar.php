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
<div style="text-align: right">Dicetak: <?= date('d F Y'); ?></div>
<table border="1" style="border-collapse: collapse;width:100%">
    <tr>
        <td colspan="3" rowspan="2" style="width:60%;text-align: center">
            <h4>DATA IJAZAH KELUAR</h4><br/>
            <span>PERIODE : <?= date('d F Y',strtotime($start)).' S/D '.date('d F Y',strtotime($end)); ?></span>
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
            <th>NO</th>
            <th>TANGGAL</th>
            <th>ATAS NAMA</th>
            <th>NO IJAZAH</th>
            <th>SEKOLAH</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . date('d/F/Y', strtotime($val['tgl_keluar'])) . '</td>';
            echo '<td align="center">' . $val['atas_nama'] . '</td>';
            echo '<td align="center">' . $val['no_ijazah'] . '</td>';
            echo '<td align="center">' . $val['nama_sekolah'] . '</td>';
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