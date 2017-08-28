<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-monitoring-service.xls");
}

//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>

<table width="100%" border="1" style="border-collapse: collapse;font-size: 9px !important;">
    <thead>
        <tr>
            <td rowspan="2"><img src="../../../img/logo.png"/></td>
            <td rowspan="2"colspan="4"><h2>LAPORAN MONITORING SERVICE KENDARAAN</h2></td>
            <td rowspan="2"colspan="5">PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></td>
            <td align="center">Dibuat</td>
            <td align="center">Diketahui</td>
            <td align="center" style="width:70px">Disetujui</td>
        </tr>
        <tr>
            <td style="height:70px"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center;" rowspan="2">NO</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2"colspan="2">TANGGAL SERVICE</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">NOPOL</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">MERK</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">TYPE</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">MODEL</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">WARNA</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">USER</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">TOTAL BIAYA</th>
            <th style="text-align: center;vertical-align: center;width:40% !important" colspan="3">DETAIL</th>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center;" colspan="2">SERVICE</th>
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
            echo '<td align="center" colspan="2">' . Yii::$app->landa->date2Ind(date('d-M-Y', strtotime($val['tgl']))) . '</td>';
            echo '<td align="center">' . $val['nopol'] . '</td>';
            echo '<td align="center">' . $val['merk'] . '</td>';
            echo '<td align="center">' . $val['tipe'] . '</td>';
            echo '<td align="center">' . $val['model'] . '</td>';
            echo '<td align="center">' . $val['warna'] . '</td>';
            echo '<td align="center">' . $val['user'] . '</td>';
            echo '<td align="center">' . Yii::$app->landa->rp($val['total_biaya']) . '</td>';
            echo '<td align="center" colspan="2">' . $ket . '</td>';
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