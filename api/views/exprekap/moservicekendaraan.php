<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-monitoring-service.xls");
}

//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>

<style>


</style>
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
<center><h4>LAPORAN MONITORING SERVICE KENDARAAN</h4><br/></center>
<span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span>

<table width="100%" border="1" style="border-collapse: collapse;font-size: 9px !important;">
    <thead>
        <tr>
            <th style="text-align: center;vertical-align: center;" rowspan="2">NO</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">TANGGAL SERVICE</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">NOPOL</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">MERK</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">TYPE</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">MODEL</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">WARNA</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">USER</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">TOTAL BIAYA</th>
            <th style="text-align: center;vertical-align: center;width:40% !important" colspan="2">DETAIL</th>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center;">SERVICE</th>
            <th style="text-align: center;vertical-align: center;">BIAYA</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $ket = '';
        $biaya = '';
        foreach ($models as $val) {
            $detail = app\models\Tblmonitoringdservice::findAll(['no' => $val['no_mservice']]);
            if (!empty($detail)) {
                foreach ($detail as $key2 => $val2) {
                    $ket .= $val2->ket_service . '<br>';
                    $biaya .= Yii::$app->landa->rp($val2->biaya) . '<br>';
                }
            }
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . Yii::$app->landa->date2Ind(date('d-M-Y', strtotime($val['tgl']))) . '</td>';
            echo '<td align="center">' . $val['nopol'] . '</td>';
            echo '<td align="center">' . $val['merk'] . '</td>';
            echo '<td align="center">' . $val['tipe'] . '</td>';
            echo '<td align="center">' . $val['model'] . '</td>';
            echo '<td align="center">' . $val['warna'] . '</td>';
            echo '<td align="center">' . $val['user'] . '</td>';
            echo '<td align="center">' . Yii::$app->landa->rp($val['total_biaya']) . '</td>';
            echo '<td align="center">' . $ket . '</td>';
            echo '<td align="center">' . $biaya . '</td>';
            $no++;
            $ket = '';
            $biaya = '';
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