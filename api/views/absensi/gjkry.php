<?php
if ($_GET['excel'] == 'ex') {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekapitulasi-gaji-karyawan.xls");
}
?>
<style type="text/css">
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

</style>
<div>
    <table>
        <tr style="line-height: 20px;">
            <th style="width:331px" rowspan="2"class="border-right">REKAPITULASI GAJI KARYAWAN</th>
            <th rowspan="2" style="text-align:left;">
                Periode &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp;<?= date('d M Y', $start); ?> - <?= date('d M Y', $end) ?><br>
                Tgl. Pelaporan : &nbsp;&nbsp;<?= Yii::$app->landa->date2ind(date('d-m-Y')); ?>

            </th>
            <th style="width:150px" class="border-all">Approved</th>
            <th style="width:150px" class="border-all">Checked</th>
            <th style="width:150px" class="border-all">Prepared</th>
        </tr>
        <tr>
            <td style="height:50px;" class="border-all"></td>
            <td  class="border-all"></td>
            <td  class="border-all"></td>
        </tr>
    </table>
    <?php
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
                    <th rowspan="3" style="text-align: center;vertical-align: middle;width: 30px">NO</th>
                    <th rowspan="3" style="text-align: center;vertical-align: middle;">NAMA</th>
                    <th colspan="5" style="text-align: center;vertical-align: middle;">INCENTIVE</th>
                    <th rowspan="3" style="text-align: center;vertical-align: middle;">TOTAL</th>
                    <th colspan="6" style="text-align: center;vertical-align: middle;">ABSENSI</th>
                    <th rowspan="3" style="text-align: center;vertical-align: middle;">TH</th>
                    <th colspan="2" style="text-align: center;vertical-align: middle;">POTONGAN UPAH</th>
                    <th rowspan="3" style="text-align: center;vertical-align: middle;" width="190">Ket</th>
                </tr>
                <tr>
                    <th colspan="5" style="text-align: center;vertical-align: middle;">MINGGU</th>
                    <th rowspan="2" style="text-align: center;vertical-align: middle;">A</th>
                    <th rowspan="2" style="text-align: center;vertical-align: middle;">I</th>
                    <th rowspan="2" style="text-align: center;vertical-align: middle;">S</th>
                    <th rowspan="2" style="text-align: center;vertical-align: middle;">SD</th>
                    <th rowspan="2" style="text-align: center;vertical-align: middle;">1/2</th>
                    <th rowspan="2" style="text-align: center;vertical-align: middle;">C</th>
                    <th rowspan="2" style="text-align: center;vertical-align: middle;">ABSENT</th>
                    <th rowspan="2" style="text-align: center;vertical-align: middle;">1/2</th>
                </tr>
                <tr>
                    <th style="text-align: center;vertical-align: middle;">I</th>
                    <th style="text-align: center;vertical-align: middle;">II</th>
                    <th style="text-align: center;vertical-align: middle;">III</th>
                    <th style="text-align: center;vertical-align: middle;">IV</th>
                    <th style="text-align: center;vertical-align: middle;">V</th>
                </tr>
            </thead>
            <tbody >
                <?php
                foreach ($data as $val) {
                    ?>
                    <tr>
                        <td style="width:20px;text-align: center"><?= $val['no'] ?></td>
                        <td style="text-align: left"><?= $val['nama'] ?></td>
                        <td style="width:25px;text-align: center"><?= $val['mg1'] ?></td>
                        <td style="width:25px;text-align: center"><?= $val['mg2'] ?></td>
                        <td style="width:25px;text-align: center"><?= $val['mg3'] ?></td>
                        <td style="width:25px;text-align: center"><?= $val['mg4'] ?></td>
                        <td style="width:25px;text-align: center"><?= $val['mg5'] ?></td>
                        <td style="width:90px;text-align: center"><?= $val['ttlinc'] ?></td>
                        <td style="width:25px;text-align: center"><?= $val['absh'] ?></td>
                        <td style="width:25px;text-align: center"><?= $val['ijnh'] ?></td>
                        <td style="width:25px;text-align: center"><?= $val['skh'] ?></td>
                        <td style="width:25px;text-align: center"><?= $val['sdh'] ?></td>
                        <td style="width:25px;text-align: center"><?= $val['sth'] ?></td>
                        <td style="width:25px;text-align: center"><?= $val['cth'] ?></td>
                        <td style="text-align: center"><?= $val['thp'] ?></td>
                        <td style="width:90px;text-align: center"><?= $val['ptga'] ?></td>
                        <td style="width:90px;text-align: center"><?= $val['ptgs'] ?></td>
                        <td style="width:190px;text-align: center"><?= $val['ket'] ?></td>
                    </tr>
                    <?php
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
        setTimeout(function () {
            window.close();
        }, 1);
    </script>
    <?php
}
?>