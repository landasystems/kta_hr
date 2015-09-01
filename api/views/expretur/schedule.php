<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-retur-schedule.xls");
$data = array();
$i = 0;
foreach ($models as $val) {
    $data[$val['no_wo']]['in_chasis'] = '<p align="left">' . $val['nm_customer'] . '</p><p align="center">' . $val['no_wo'] . '</p>';
    $data[$val['no_wo']]['model'] = $val['model'];
    $data[$val['no_wo']]['merk_tipe'] = $val['merk'] . ' / ' . $val['tipe'];
    $data[$val['no_wo']]['body'][$i]['proses'] = $val['bagian'];
    $data[$val['no_wo']]['body'][$i]['ps'] = $val['plan_start'];
    $data[$val['no_wo']]['body'][$i]['pf'] = $val['plan_finish'];
    $data[$val['no_wo']]['body'][$i]['as'] = $val['act_start'];
    $data[$val['no_wo']]['body'][$i]['af'] = $val['act_finish'];
    $data[$val['no_wo']]['body'][$i]['keterangan'] = $val['keterangan'];
    $i++;
}
?>
<link href="../../../css/print.css" rel="stylesheet" type="text/css" />
<div style="margin-top: 30px; margin-left: 30px;">
    <table width="100%" class="print-detail" border="1">
        <thead>
            <tr>
                <td rowspan="3" width="100"><img src="../../../img/logo.png" alt="." style="display: inline"></td>
                <td colspan="3" rowspan="3" valign="top">
                    <b>MASTER SCHEDULLE</b>
                    <br>
                    Kode Dokumen :
                </td>
                <td colspan="4" rowspan="3" valign="top">
                    Cetak : <?php echo date("d-M-Y") ?>
                </td>
                <td height="25" colspan="2">
                    Disetujui
                </td>
                <td height="25">
                    Dibuat
                </td>
            </tr>
            <tr height="45">
                <td colspan="2"></td>
                <td></td>
            </tr>
            <tr height="25">
                <td colspan="2">Tgl :</td>
                <td>Tgl : </td>
            </tr>
            <tr>
                <td rowspan="2" align="left" colspan="2" width="400">In Chasis</td>
                <td rowspan="2" align="center">Model</td>
                <td rowspan="2" align="center" width="200">Merk / Tipe</td>
                <td rowspan="2" align="center">Proses</td>
                <td align="center" colspan="2" align="center">Plan</td>
                <td align="center" colspan="2" align="center">Actual</td>
                <td rowspan="2" colspan="2" align="center">Keterangan</td>
            </tr>
            <tr>
                <td align="center">Start</td>
                <td align="center">Finish</td>
                <td align="center">Start</td>
                <td align="center">Finish</td>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($data as $value) {
                ?>
                <tr>
                    <td colspan="2" valign="top"  style="border-bottom: none;"><?php echo $value['in_chasis'] ?></td>
                    <td valign="top"  style="border-bottom: none;"><?php echo $value['model'] ?></td>
                    <td valign="top"  style="border-bottom: none;"><?php echo $value['merk_tipe'] ?></td>
                    <td valign="top"  style="border-bottom: none;"></td>
                    <td  style="border-bottom: none;"></td>
                    <td  style="border-bottom: none;"></td>
                    <td  style="border-bottom: none;"></td>
                    <td  style="border-bottom: none;"></td>
                    <td style="border-bottom: none;" colspan="2"></td>
                </tr>
                <?php
                foreach ($value['body'] as $scedule) {
                    ?>
                    <tr>
                        <td colspan="2" valign="top" style="border-top: none; border-bottom: none;"></td>
                        <td valign="top" style="border-top: none; border-bottom: none;"></td>
                        <td valign="top" style="border-top: none; border-bottom: none;"></td>
                        <td valign="top" style="border-top: none; border-bottom: none;" align="left"><?php echo $scedule['proses'] ?></td>
                        <td valign="top" style="border-top: none; border-bottom: none;" align="right"><?php echo $scedule['ps'] ?></td>
                        <td valign="top" style="border-top: none; border-bottom: none;" align="right"><?php echo $scedule['pf'] ?></td>
                        <td valign="top" style="border-top: none; border-bottom: none;" align="right"><?php echo $scedule['as'] ?></td>
                        <td valign="top" style="border-top: none; border-bottom: none;" align="right"><?php echo $scedule['af'] ?></td>
                        <td colspan="2" valign="top" style="border-top: none; border-bottom: none;"><?php echo $scedule['keterangan'] ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>