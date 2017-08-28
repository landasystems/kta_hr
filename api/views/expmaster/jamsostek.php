<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-master-bpjs.xls");
}
?>

<table width="100%" style="border-collapse: collapse; " border="1" >
    <tr>
        <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
        <td colspan="3" rowspan="2" style="text-align: center;">
            LAPORAN MASTER BPJS KETENAGA KERJAAN
        </td>
        <td rowspan="2" colspan="3" style="text-align: center"> Tgl Pelaporan :  <?= date('d F Y'); ?> </td>
        <td colspan="2" style="text-align: center;">Diketahui</td>
        <td colspan="2" style="text-align: center;">Diperiksa</td>
        <td colspan="2" style="text-align: center;">Dibuat</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 100px;height: 80px;"></td>
        <td colspan="2" style="width: 100px;"></td>
        <td colspan="2" style="width: 100px;" ></td>
    </tr>
<!--</table><table width="100%" style="border-collapse: collapse; " border="1">-->
    <tr >
        <th rowspan="2">NIK</th>
        <th rowspan="2">Nama Karwayan</th>
        <th rowspan="2">Status Pernikahan</th>
        <th rowspan="2">Upah TK</th>
        <th colspan="9">Detail BPJS Ketenaga Kerjaan</th>
    </tr>
    <tr>
        <th>NN/BU</th>
        <th>KPJ/No Kartu</th>
        <th>Periode Kepesertaan</th>
        <th>JHT</th>
        <th>JKM</th>
        <th>JKK</th>
        <th>Pensiun</th>
        <th>Iuran</th>
        <th>Keterangan</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        ?>
        <tr>

            <td >&nbsp;<?= $arr['nik'] ?></td>
            <td width="5%;"><?= $arr['nama'] ?></td>
            <td width="5%;" style="text-align: center">
                <?php
                if ($arr['status_pernikahan'] == 'Belum Kawin') {
                    echo 'Belum';
                } else {
                    echo 'Kawin';
                }
                ?>
            </td>

            <td style="text-align: right;">
                <?= Yii::$app->landa->rp($arr['upah_tetap'], FALSE) ?>
            </td>
            <td >
                <?php
                foreach ($details as $det) {
                    if ($arr['nik'] == $det['nik'])
                        echo $det['nn'] . '<br/>';
                }
                ?>
            </td>
            <td>
                <?php
                foreach ($details as $det) {
                    if ($arr['nik'] == $det['nik'])
                        echo $det['kpj'] . '<br/>';
                }
                ?>
            </td>
            <td style="text-align: center;">
                 <?php
                foreach ($details as $det) {
                    if ($arr['nik'] == $det['nik'])
                        echo date ('m-Y', strtotime ($det['periode_kepesertaan']))  . '<br/>';
                }
                ?>
            </td>
            <td style="text-align: right">
                <?php
                foreach ($details as $det) {
                    if ($arr['nik'] == $det['nik'])
                        echo Yii::$app->landa->rp($det['jht'],FALSE)  . '<br/>';
                }
                ?>
            </td>
            <td style="text-align: right">
                <?php
                foreach ($details as $det) {
                    if ($arr['nik'] == $det['nik'])
                        echo Yii::$app->landa->rp($det['jkm'],FALSE)  . '<br/>';
                }
                ?>
            </td>
            <td style="text-align: right"> 
             <?php
                foreach ($details as $det) {
                    if ($arr['nik'] == $det['nik'])
                        echo Yii::$app->landa->rp($det['jkk'],FALSE)  . '<br/>';
                }
                ?>
            </td >
            <td style="text-align: right">
                <?php
                foreach ($details as $det) {
                    if ($arr['nik'] == $det['nik'])
                        echo Yii::$app->landa->rp($det['pensiun'], FALSE)  . '<br/>';
                }
                ?>
            </td>
            <td style="text-align: right">
                <?php
                foreach ($details as $det) {
                    if ($arr['nik'] == $det['nik'])
                        echo Yii::$app->landa->rp($det['iuran'] , FALSE)  . '<br/>';
                }
                ?>
            </td>
            <td>
                <?php
                foreach ($details as $det) {
                    if ($arr['nik'] == $det['nik'])
                        echo $det['keterangan']  . '<br/>';
                }
                ?>
            </td>
        </tr>
        <?php
    }
    ?>
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