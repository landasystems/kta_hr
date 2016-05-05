<?php
//header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-master-jabatan.xls");
?>
<!--<h3>Data Master Jabatan</h3>
<br><br>-->
<!--<link rel="stylesheet" href="../../../css/print.css" type="text/css" />-->
<!--<div style="width:26cm">-->
    <?php
//    if (isset($_GET['print'])) {
        ?>
<table width="100%" style="border-collapse: collapse;" border="1">
            <tr>
                <td width="80" rowspan="3"><img src="../../../img/logo.png"></td>
                <td rowspan="3" >
                    <h2> LAPORAN ABSENSI KARYAWAN</h2>
                </td>
                <td rowspan="3"></td>
                <td style="text-align: center;">Diketahui</td>
                <td style="text-align: center;">Diperiksa</td>
                <td style="text-align: center;">Dibuat</td>
            </tr>
            <tr>
                <td style="width: 80px;height: 100px;"></td>
                <td style="width: 80px;"></td>
                <td style="width: 80px;" ></td>
            </tr>
            
        </table>
        <!--<hr>-->
        <?php
//    }
    ?>

<br>
<br>
<div style="text-align: right">Dicetak: <?= date('d F Y'); ?></div>
<table width="100%" border="1" style="border-collapse: collapse">
    <tr>
        <th>Kode</th>
        <th>Jabatan</th>
        <th>Sub Section</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['id_jabatan']?></td>
            <td style="text-align: center;"><?=$arr['jabatan']?></td>
            <td><?=$arr['kerja']?></td>
            
        </tr>
    <?php } ?>
</table>

<!--</div>-->