<?php
if ($_GET['excel'] == 'ex') {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekapitulasi-gaji-karyawan-bulanan.xls");
}

function edtnama($tes) {
    $ex1 = explode(" - ", $tes);
    $ex = explode(" ", $ex1[1]);
    $data = [];
    foreach ($ex as $val) {
        $data[] = ucfirst(strtolower($val));
    }
    return implode(' ', $data);
}

$data2 = [];
$i = 0;

foreach ($data as $value) {
    $data2[$value['department']]['title']['section'] = $value['department'];
    $data2[$value['department']]['body'][$i]['no'] = $value['no'];
    $data2[$value['department']]['body'][$i]['nama'] = edtnama($value['nama']);
    $data2[$value['department']]['body'][$i]['mg1'] = $value['mg1'];
    $data2[$value['department']]['body'][$i]['mg2'] = $value['mg2'];
    $data2[$value['department']]['body'][$i]['mg3'] = $value['mg3'];
    $data2[$value['department']]['body'][$i]['mg4'] = $value['mg4'];
    $data2[$value['department']]['body'][$i]['mg5'] = $value['mg5'];
    $data2[$value['department']]['body'][$i]['ttlinc'] = $value['ttlinc'];
    $data2[$value['department']]['body'][$i]['absh'] = $value['absh'];
    $data2[$value['department']]['body'][$i]['ijnh'] = $value['ijnh'];
    $data2[$value['department']]['body'][$i]['skh'] = $value['skh'];
    $data2[$value['department']]['body'][$i]['sdh'] = $value['sdh'];
    $data2[$value['department']]['body'][$i]['sth'] = $value['sth'];
    $data2[$value['department']]['body'][$i]['cth'] = $value['cth'];
    $data2[$value['department']]['body'][$i]['ptga'] = $value['ptga'];
    $data2[$value['department']]['body'][$i]['thp'] = $value['thp'];
    $data2[$value['department']]['body'][$i]['ptgs'] = $value['ptgs'];
    $data2[$value['department']]['body'][$i]['ket'] = $value['ket'];
    $i++;
}
?>
<style type="text/css">
    * {
        font-size: 12px;
    }
    table {
        width: 100%;
        border-collapse: collapse;

    }
    .border-left {
        border-left: 1px #000 solid;
        padding: 2px;
    }

    .border-top {
        border-top: 1px #000 solid;
        padding: 2px;
    }

    .border-right {
        border-right: 1px #000 solid;
        padding: 2px;
    }

    .border-bottom {
        border-bottom: 1px #000 solid;
        padding: 2px;
    }


    .border-all {
        border-bottom: 1px #000 solid;
        border-left: 1px #000 solid;
        border-top: 1px #000 solid;
        border-right: 1px #000 solid;
        padding: 2px;
    }

    thead , tbody {
        border: 1px solid black;
    }

    thead > tr > th{
        border: 1px solid black;
    }

    tbody > tr > td {
        border: 1px solid black;
    }

    tbody > tr { 
        line-height: 20px; 
    }
    .border-none {
        border:none;
    }
    thead {
        background-color:rgb(226, 222, 222) !important;;
        background-image: url("../../../img/print.png");
        background-repeat: repeat;
        -webkit-print-color-adjust:exact;
    }

    .back-grey{
        background-color:rgb(178,178,178) !important;;
        background-image: url("../../../img/print.png");
        background-repeat: repeat;
        -webkit-print-color-adjust:exact;
    }

</style>
<div>
    <?php
    if ($_GET['excel'] != 'ex') {
        ?>
        <table >
            <tr style="line-height: 20px;">
                <th style="font-size: 12px;width:331px" rowspan="2"class="border-right"><u>REKAPITULASI GAJI KARYAWAN BULANAN</u><br><center>FR-HRD-026</center></th>
            <th rowspan="2" style="font-size: 12px;text-align:left;">
                Periode &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp;<?= Yii::$app->landa->date2ind(date('d-m-Y', $start)); ?> - <?= Yii::$app->landa->date2ind(date('d-m-Y', $end)) ?><br>
                Tgl. Pelaporan : &nbsp;&nbsp;<?= Yii::$app->landa->date2ind(date('d-m-Y')); ?>

            </th>
            <th style="font-size: 12px;width:110px" class="border-all">Approved</th>
            <th style="font-size: 12px;width:110px" class="border-all">Checked</th>
            <th style="font-size: 12px;width:110px" class="border-all">Prepared</th>
            </tr>
            <tr>
                <td style="height:50px;" class="border-all"></td>
                <td  class="border-all"></td>
                <td  class="border-all"></td>
            </tr>
        </table>
        <?php
    } else {
        ?>
        <table>
            <tr style="font-family: arial;font-size: 14;line-height: 20px;">
                <th style="width:331px" rowspan="2" colspan="5"class="border-right"><u>REKAPITULASI GAJI KARYAWAN BULANAN</u><br><center>FR-HRD-026</center></th>
            <th rowspan="2" colspan="10"style="text-align:left;">
                Periode &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp;<?= Yii::$app->landa->date2ind(date('d-m-Y', $start)); ?> - <?= Yii::$app->landa->date2ind(date('d-m-Y', $end)) ?><br>
                Tgl. Pelaporan : &nbsp;&nbsp;<?= Yii::$app->landa->date2ind(date('d-m-Y')); ?>

            </th>
            <th style="width:110px" class="border-all">Approved</th>
            <th style="width:110px"  class="border-all">Checked</th>
            <th width="110" class="border-all">Prepared</th>
            </tr>
            <tr>
                <td style="height:50px;" class="border-all"></td>
                <td  class="border-all" ></td>
                <td  width="110" class="border-all"></td>
            </tr>
        </table> 
        <?php
    }
    if ($_GET['excel'] == 'ex') {
        ?>
        <table border="1">
            <?php
        } else {
            ?>
            <table>
                <?php
            }
            ?>
            <thead>
                <tr>
                    <th rowspan="3" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;width: 30px">NO</th>
                    <th rowspan="3" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">NAMA</th>
                    <th colspan="5" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">INCENTIVE</th>
                    <th rowspan="3" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">TOTAL</th>
                    <th colspan="6" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">ABSENSI</th>
                    <th rowspan="3" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">TH</th>
                    <th colspan="2" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">POTONGAN UPAH</th>
                    <?php
                    if ($_GET['excel'] != 'ex') {
                        ?>
                        <th rowspan="3" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;" width="190">Ket</th>
                        <?php
                    } else {
                        ?>
                        <th rowspan="3" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;" width="110">Ket</th>

                    <?php } ?>
                </tr>
                <tr>
                    <th colspan="5" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">MINGGU</th>
                    <th rowspan="2" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">A</th>
                    <th rowspan="2" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">I</th>
                    <th rowspan="2" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">S</th>
                    <th rowspan="2" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">SD</th>
                    <th rowspan="2" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">&nbsp;1/2</th>
                    <th rowspan="2" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">C</th>
                    <th rowspan="2" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">ABSENT</th>
                    <th rowspan="2" class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">&nbsp;1/2&nbsp;</th>
                </tr>
                <tr>
                    <th class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">I</th>
                    <th class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">II</th>
                    <th class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">III</th>
                    <th class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">IV</th>
                    <th class="back-grey" style="font-size: 12px;text-align: center;vertical-align: middle;">V</th>
                </tr>
            </thead>
            <tbody >
                <?php
                $sorted = Yii::$app->landa->array_orderby($data2, 'title', SORT_ASC);
                $no_urut = 1;
                foreach ($sorted as $key) {
                    ?>
                    <tr>
                        <td colspan="2" style="font-size: 12px;text-align:left;font-weight: bold;"><?= strtoupper($key['title']['section']) ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td width="110"></td>
                    </tr>
                    <?php
                    $sorted2 = Yii::$app->landa->array_orderby($key['body'], 'nama', SORT_ASC);

                    foreach ($sorted2 as $val) {
                        ?>
                        <tr>
                            <td style="font-size: 12px;width:20px;text-align: center"><?= $no_urut ?></td>
                            <td style="font-size: 12px;text-align: left">&nbsp;<?= $val['nama'] ?></td>
                            <td style="font-size: 12px;width:25px;text-align: center"><?= $val['mg1'] ?></td>
                            <td style="font-size: 12px;width:25px;text-align: center"><?= $val['mg2'] ?></td>
                            <td style="font-size: 12px;width:25px;text-align: center"><?= $val['mg3'] ?></td>
                            <td style="font-size: 12px;width:25px;text-align: center"><?= $val['mg4'] ?></td>
                            <td style="font-size: 12px;width:25px;text-align: center"><?= $val['mg5'] ?></td>
                            <td style="font-size: 12px;width:90px;text-align: center"><?= $val['ttlinc'] ?></td>
                            <td style="font-size: 12px;width:27px;text-align: center"><?= $val['absh'] ?></td>
                            <td style="font-size: 12px;width:25px;text-align: center"><?= $val['ijnh'] ?></td>
                            <td style="font-size: 12px;width:25px;text-align: center"><?= $val['skh'] ?></td>
                            <td style="font-size: 12px;width:25px;text-align: center"><?= $val['sdh'] ?></td>
                            <td style="font-size: 12px;width:30px;text-align: center"><?= $val['sth'] ?></td>
                            <td style="font-size: 12px;width:25px;text-align: center"><?= $val['cth'] ?></td>
                            <td style="font-size: 12px;text-align: center"><?= $val['thp'] ?></td>
                            <?php
                            if ($_GET['excel'] != 'ex') {
                                ?>
                                <td style="font-size: 12px;width:90px;text-align: center"><?= $val['ptga'] ?></td>
                                <td style="font-size: 12px;width:90px;text-align: center"><?= $val['ptgs'] ?></td>
                                <td style="font-size: 12px;width:190px;text-align: center"><?= $val['ket'] ?></td>
                                <?php
                            } else {
                                ?>
                                <td style="font-size: 12px;width:100px;text-align: center"><?= $val['ptga'] ?></td>
                                <td style="font-size: 12px;width:100px;text-align: center"><?= $val['ptgs'] ?></td>
                                <td style="font-size: 12px;text-align: center;" width="110"><?= $val['ket'] ?></td>

                            <?php } ?>
                        </tr>
                        <?php
                        $no_urut++;
                    }
                }
                ?>
            </tbody>
        </table>
</div>
<?php
if ($_GET['excel'] == 'print') {
    ?>
    <script type="text/javascript">
        window.print();
//        setTimeout(function () {
//            window.close();
//        }, 1);
    </script>
    <?php
}
?>