<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-bstk.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>LAPORAN CHASSIS IN</b></center>
<br><br>


<table border="1">
    <tr>
        <td colspan="5">
            <br>
    <center><b></b></center>
    <br><br>
    <center>DATA CHASSIS IN</center>
    <br><br>

    </td>
    <td  colspan="5" valign="top">
        <table>
            <tr>
                <td>DEPARTEMENT</td>
               
                <td> Sales Report</td>
            </tr>
            <tr>
                <td>PERIODE</td>
                <?php
                if (!empty($filter['tgl_terima'])) {
                    $value = explode(' - ', $filter['tgl_terima']);
                    $start = date("d/m/Y", strtotime($value[0]));
                    $end = date("d/m/Y", strtotime($value[1]));
                } else {
                    $start = '';
                    $end = '';
                }
                ?>
                <td> : <?php echo $start . ' - ' . $end ?></td>
            </tr>
            <tr>
                <td>CETAK</td>
                <td> : <?php echo date('d/M/Y') ?></td>
            </tr>
        </table>
    </td>
</tr>

</table>
<?php
$data = array();
$i = 0;
foreach ($models as $key => $val) {
    $data[$val['provinsi']]['title']['pro'] = $val['provinsi'];
    $data[$val['provinsi']]['tipe'][$val['jenis']]['tipe'] = $val['jenis'];

    $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['tgl_terima'] = $val['tgl_terima'];
    $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['nama'] = $val['nama'];
    $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['nm_customer'] = $val['nm_customer'];
    $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['kd_titipan'] = $val['kd_titipan'];
    $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['no_wo'] = $val['no_wo'];
    $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['total_harga'] = $val['total_harga'];
    $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['merk'] = $val['merk'];
    $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['type'] = $val['tipe'];
    $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['no_chassis'] = $val['no_chassis'];
    $data[$val['provinsi']]['tipe'][$val['jenis']]['body'][$i]['no_mesin'] = $val['no_mesin'];
    $i++;
}
?>
<table border="1">
    <tr>
        <th>NO</th>
        <th>IN CHASSIS</th>
        <th>SALES</th>
        <th>CUSTOMER</th>
        <th >KODE TITIPAN</th>
        <th>NO WO</th>
        <th>REVENUE</th>
        <th>MERK/TYPE</th>
        <th>NO RANGKA</th>
        <th>NO ENGINE</th>
    </tr>
    <?php
    foreach ($data as $keys) {
        ?>
        <tr><td colspan="10" style="text-align: left;background-color: #008000;color:#ffffff;"><?= $keys['title']['pro']; ?>&nbsp;</td></tr>

        <?php
        $no = 0;
        $total = 0;
        foreach ($keys['tipe'] as $val1) {
            echo'<tr><td colspan="4" style="text-align: left;background-color: chartreuse;">' . $val1['tipe'] . '</td></tr>';
            foreach ($val1['body'] as $val) {
                $no++;
                $total += $val['total_harga'];
                ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= Yii::$app->landa->date2Ind($val['tgl_terima']); ?>&nbsp;</td>
                    <td><?= $val['nama']; ?></td>
                    <td><?= $val['nm_customer']; ?></td>
                    <td><?= $val['kd_titipan']; ?></td>
                    <td><?= $val['no_wo']; ?></td>
                    <td><?= $val['total_harga']; ?></td>
                    <td><?= $val['merk']; ?> <?= $val['type']; ?></td>
                    <td><?= $val['no_chassis']; ?></td>
                    <td><?= $val['no_mesin']; ?></td>
                </tr>
                <?php
            }
            echo'<tr>
                <th colspan="5"></th>
                <th>TOTAL</th>
                <th >' . $total . '</th>
                    <th colspan="">TOTAL</th>
                </tr>';
        }
    }
    ?>
</table>