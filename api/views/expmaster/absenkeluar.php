<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-absen-keluar.xls");
}

//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>
<div id="printArea" style="margin-right: 30px;margin-left: 30px;">
    <style media="print">
        .printedArea,.printedArea table,printedArea span, printedArea div{
            font-size: 10pt;
        }
    </style>
    <div class="printedArea">
        <center style="font-weight: bold;font-size: 17px"><u>SURAT IJIN</u></center>
        <br>
        Yang bertandatangan di bawah ini:<br/>
        <table>
            <tr>
                <td width="125">Nama</td>
                <td>:</td>
                <td><u><?= $models['nama'] ?></u></td>
            </tr>
            <tr>
                <td>Bagian</td>
                <td>:</td>
                <td><u><?php
                $subSection = (!empty($models['kerja'])) ? $models['kerja'] : '';

                echo $subSection;
                ?></u></td>
            </tr>
            <tr>
                <td colspan="3">Mengajukan permohonan ijin pada:</td>
            </tr>
            <tr>
                <td>Hari/ Tanggal</td>
                <td>:</td>
                <td>
                    <?php
                    setlocale(LC_TIME, 'id_ID');
                    $arrDay = array("Monday" => "Senin", "Tuesday" => "Selasa", "Wednesday" => "Rabu", "Thursday" => "Kamis", "Friday" => "Jum'at", "Saturday" => "Sabtu", "Sunday" => "Minggu");
                    $day = strftime("%A", strtotime($models['tanggal']));
                    echo $arrDay[$day];
//                    setlocale(LC_TIME, 'id_ID');
//                    echo strftime("Today in Indonesia is %A");
                    ?>
                    ,
                    <?php echo Yii::$app->landa->date2ind($models['tanggal']); ?>
                    <?php
                    if (!empty($models['tanggal_kembali'])) {
                        if ($models['tanggal_kembali'] != $models['tanggal']) {
                            echo ' - ' . Yii::$app->landa->date2ind($models['tanggal_kembali']);
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td>
                    <?php
                    if ((!empty($models['jmasuk']) && !empty($models['jkeluar'])) && ($models['ket_absen'] == "Izin" || $models['ket_absen'] == "Dinas Luar"))
                        echo '<u>' . $models['jmasuk'] . '</u> s/d <u>' . $models['jkeluar'] . '</u>';
                    else
                        echo '-';
                    ?>
            </tr>
            <tr>
                <td>Keperluan</td>
                <td>:</td>
                <td>
                    <?php echo $models['ket_absen'] ?>
                    <!--                    a. Pribadi : <u></u>-->
                </td>
            </tr>
<!--            <tr>
                <td></td>
                <td></td>
                <td>b. Dinas : <u><?php
//                    if ($models['ket_absen'] == 'Dinas Luar') {
//                        echo $models['ket_uraian'];
//                    }
                    ?></u></td>
            </tr>-->
        </table>
        <hr/>
        <table style="border-collapse: collapse;width:100%;">
            <tr>
                <td style="vertical-align: top;height: 90px;" width="125">Keterangan</td>
                <td style="vertical-align: top;height: 90px;">:</td>
                <td style="vertical-align: top;height: 90px;"><?= $models['ket_uraian'] ?></td>
            </tr>
        </table>
        <!--<br/>-->
        <span style="text-align: right;">Demikian Surat Ijin ini dibuat, untuk dapat dipergunakan sebagaimana mestinya.</span>
        <br/>
        <div style="text-align: right;">Dibuat di Sukorejo, <?= date('d F Y', strtotime($models['tgl_pembuatan'])); ?></div>
        <hr/>
        <table style="width:100%;border-collapse:collapse;">
            <tr>
                <td style="height: 90px;vertical-align: bottom;width:33%;border-right: 3px;text-align: center;">HRD</td>
                <td style="height: 90px;vertical-align: bottom;width:33%;border-right: 3px;text-align: center;">PIMPINAN</td>
                <td style="height: 90px;vertical-align: bottom;width:33%;border-right: 3px;text-align: center;">PEMOHON</td>
            </tr>
        </table>
    </div>
</div>
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
