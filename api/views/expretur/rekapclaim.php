<?php
header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-rekap-PO.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>REKAP CLAIM UNIT</b></center>
<br><br>
<?php
$data = array();
$i = 0;

foreach ($models as $key => $val) {
    $data[$val['no_wo']]['title']['no_wo'] = $val['no_wo'];
    $data[$val['no_wo']]['title']['model'] = $val['model'];
    $data[$val['no_wo']]['title']['nm_customer'] = $val['nm_customer'];
    $data[$val['no_wo']]['title']['lokasi_kntr'] = $val['lokasi_kntr'];
    $data[$val['no_wo']]['title']['nama'] = $val['nama'];
    //
    $data[$val['no_wo']]['stat'][$val['stat']]['title'] = $val['stat'];
//
    $data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl'] = isset($data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl']) ? $data[$val['no_wo']]['stat'][$val['stat']]['body']['tgl'] . $val['tgl'] . '<br>' : $val['tgl'] . '<br>';
//    $data[$val['no_wo']]['body']['tgl_pelaksanaan'] = isset($data[$val['no_wo']]['body']['tgl_pelaksanaan']) ? $data[$val['no_wo']]['body']['tgl_pelaksanaan'] . $val['tgl_pelaksanaan'] . '<br>' : $val['tgl_pelaksanaan'] . '<br>';
//    $data[$val['no_wo']]['body']['pelaksana'] = isset($data[$val['no_wo']]['body']['pelaksana']) ? $data[$val['no_wo']]['body']['pelaksana'] . $val['pelaksana'] . '<br>' : $val['pelaksana'] . '<br>';
//    $data[$val['no_wo']]['body']['jns_komplain'] = isset($data[$val['no_wo']]['body']['jns_komplain']) ? $data[$val['no_wo']]['body']['jns_komplain'] . $val['jns_komplain'] . '<br>' : $val['jns_komplain'] . '<br>';
//    $data[$val['no_wo']]['body']['problem'] = isset($data[$val['no_wo']]['body']['problem']) ? $data[$val['no_wo']]['body']['problem'] . $val['problem'] . '<br>' : $val['problem'] . '<br>';
//    $data[$val['no_wo']]['body']['solusi'] = isset($data[$val['no_wo']]['body']['solusi']) ? $data[$val['no_wo']]['body']['solusi'] . $val['solusi'] . '<br>' : $val['solusi'] . '<br>';
//    $data[$val['no_wo']]['body']['biaya_mat'] = isset($data[$val['no_wo']]['body']['biaya_mat']) ? $data[$val['no_wo']]['body']['biaya_mat'] . $val['biaya_mat'] . '<br>' : $val['biaya_mat'] . '<br>';
//    $data[$val['no_wo']]['body']['biaya_tk'] = isset($data[$val['no_wo']]['body']['biaya_tk']) ? $data[$val['no_wo']]['body']['biaya_tk'] . $val['biaya_tk'] . '<br>' : $val['biaya_tk'] . '<br>';
//    $data[$val['no_wo']]['body']['biaya_spd'] = isset($data[$val['no_wo']]['body']['biaya_spd']) ? $data[$val['no_wo']]['body']['biaya_spd'] . $val['biaya_spd'] . '<br>' : $val['biaya_spd'] . '<br>';
//    $data[$val['no_wo']]['body']['total'] = isset($data[$val['no_wo']]['body']['total']) ? $data[$val['no_wo']]['body']['total'] . ($val['biaya_mat'] + $val['biaya_tk'] + $val['biaya_spd']) . '<br>' : ($val['biaya_mat'] + $val['biaya_tk'] + $val['biaya_spd']) . '<br>';


    $i++;
}
?>

<table border="1">
    <tr>
        <th style="text-align: center;">No</th>
        <th>No WO</th>
        <th>Model</th>
        <th>Customer</th>
        <th>Area</th>
        <th>Sales</th>
        <th>Tgl Claim</th>
        <th>Tgl Penanganan</th>
        <th>Pelakasana</th>
        <th>Jenis Komplain</th>
        <th>Problem</th>
        <th>Perubahan yg dilakukan</th>
        <th>Biaya Material</th>
        <th>Biaya TK</th>
        <th>Biaya SPD</th>
        <th>Biaya Total</th>
    </tr>
    <?php
    $n = 1;
    foreach ($data as $key) {
        ?>
        <tr>
            <td valign="top"><?= $n ?></td>
            <td valign="top"><?php echo $key['title']['no_wo'] ?></td>
            <td valign="top"><?php echo $key['title']['model'] ?></td>
            <td valign="top"><?php echo $key['title']['nm_customer'] ?></td>
            <td valign="top"><?php echo $key['title']['lokasi_kntr'] ?></td>
            <td valign="top"><?php echo $key['title']['nama'] ?></td>
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
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><?php echo $key['body']['tgl'] ?></td>
            <td><?php //echo $key['body']['tgl_pelaksanaan'] ?></td>
            <td><?php// echo $key['body']['pelaksana'] ?></td>
            <td><?php// echo $key['body']['jns_komplain'] ?></td>
            <td><?php// echo $key['body']['problem'] ?></td>
            <td><?php //echo $key['body']['solusi'] ?></td>
            <td style="text-align: right">&nbsp;<?php //echo $key['body']['biaya_mat'] ?></td>
            <td style="text-align: right">&nbsp;<?php //echo $key['body']['biaya_tk'] ?></td>
            <td style="text-align: right">&nbsp;<?php// echo $key['body']['biaya_spd'] ?></td>
            <td style="text-align: right">&nbsp;<?php// echo $key['body']['total'] ?></td>
        </tr>
        <?php
        $n++;
    }
    ?>
</table>
