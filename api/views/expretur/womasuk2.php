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


<table>
    <tr>
        <td colspan="3" style="border: 1px solid #000000">
            <br>
    <center><b>LAPORAN UNIT MASUK</b></center>
    <br>
    <center>No Dokumen : FR-PPC-004 Rev 00</center>
    <center>Applicable To Realisasi OI & Budget Opname</center>
    <br>

    </td>
    <td colspan="4" style="border: 1px solid #000000">
        <table>
            
            <tr>
                <td>Periode</td>
                <?php
                if (!empty($filter['tgl'])) {
                    $value = explode(' - ', $filter['tgl']);
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
                <td>Cetak</td>
                <td> : <?php echo date('d F Y') ?></td>
            </tr>
        </table>
    </td>
    <td  style="border: 1px solid #000000" valign="top">
    <center><b>Dibuat oleh</b></center>

</td>
<td  style="border: 1px solid #000000" valign="top">
<center><b>Diperiksa oleh</b></center>

</td>

</tr>
</table>
<?php
$data = array();
$i = 0;
foreach ($models as $key => $val) {
    $data[$val['nm_customer']]['title']['pro'] = $val['provinsi'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['customer'] = $val['nm_customer'];

    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['kd_titipan'] = $val['kd_titipan'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['jml_unit'] = $val['jml_unit'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['no_wo'] = $val['no_wo'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['model'] = $val['model'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['merk'] = $val['merk'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tipe'] = $val['tipe'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['nama'] = $val['nama'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['no_chassis'] = $val['no_chassis'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['no_mesin'] = $val['no_mesin'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['market'] = $val['market'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tgl'] = $val['tgl'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['nm_customer'] = $val['nm_customer'];
    $i++;
}
?>
<table border="1">
    <tr>
        <!--<th>IN CHASIS</th>-->
        <th>UNIT</th>
        <th>WO</th>
        <th>CUSTOMER</th>
        <th>MODEL</th>
        <th>MERK/TYPE</th>
        <th>MARKET</th>
        <th>TGL PEMB WO</th>
        <th>IN SPK</th>
        <th>KETERANGAN</th>
    </tr>
    <?php
    foreach ($data as $keys) {
        ?>
        <tr><td colspan="9" style="text-align: left;background-color: #008000;color:#ffffff;"><?= $keys['title']['pro']; ?>&nbsp;</td></tr>

        <?php
        $no = 0;
        $total = 0;
        foreach ($keys['customer'] as $val1) {
            echo'<tr><td colspan="9" style="text-align: left;background-color: chartreuse;">' . $val1['customer'] . '</td></tr>';
            foreach ($val1['body'] as $val) {
                ?>
                <tr>
                    <td><?= $val['jml_unit']; ?></td>
                    <td><?= $val['no_wo']; ?></td>
                    <td><?= $val['nm_customer']; ?></td>
                    <td><?= $val['model']; ?></td>
                    <td><?= $val['merk']; ?> <?= $val['tipe']; ?></td>
                    <td><?= $val['market']; ?></td>
                    <td></td>
                    <td><?= Yii::$app->landa->date2Ind($val['tgl']); ?>&nbsp;</td>
                    <td></td>
                    
                    

                </tr>
                <?php
            }
        }
    }
    ?>
</table>