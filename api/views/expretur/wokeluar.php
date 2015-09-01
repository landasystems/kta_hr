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
        <td colspan="2" style="border: 1px solid #000000">
            <br>
    <center><b>LAPORAN UNIT KELUAR</b></center>
    <br>
    <center>No Dokumen : FR-PPC-017 Rev 00</center>
    <center>Applicable To Realisasi OI & Budget Opname</center>
    <br>

    </td>
    <td colspan="4" style="border: 1px solid #000000">
        <table>
            <tr>
                <td>Nomer</td>
                <td> : </td>
            </tr>
            <tr>
                <td>Periode</td>
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
                <td>Cetak</td>
                <td> : <?php echo date('d/m/Y') ?></td>
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

    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tgl_terima'] = $val['tgl_terima'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['jml_unit'] = $val['jml_unit'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['no_wo'] = $val['no_wo'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['model'] = $val['model'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['merk'] = $val['merk'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tipe'] = $val['tipe'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['nama'] = $val['nama'];
    $data[$val['nm_customer']]['customer'][$val['nm_customer']]['body'][$i]['tgl_keluar'] = $val['tgl_keluar'];
    $i++;
}
?>
<table border="1">
    <tr>
        <th>IN CHASIS</th>
        <th>UNIT</th>
        <th>NO WO</th>
        <th >MODEL</th>
        <th>MERK TYPE</th>
        <th>SALES</th>
        <th>OUT</th>
        <th>KETERANGAN</th>
    </tr>
    <?php
    foreach ($data as $keys) {
        ?>
        <tr><td colspan="8" style="text-align: left;background-color: #008000;color:#ffffff;"><?= $keys['title']['pro']; ?>&nbsp;</td></tr>

        <?php
        $no = 0;
        $total = 0;
        foreach ($keys['customer'] as $val1) {
            echo'<tr><td colspan="8" style="text-align: left;background-color: chartreuse;">' . $val1['customer'] . '</td></tr>';
            foreach ($val1['body'] as $val) {
                ?>
                <tr>
                    <td><?= Yii::$app->landa->date2Ind($val['tgl_terima']); ?>&nbsp;</td>
                    <td><?= $val['jml_unit']; ?></td>
                    <td><?= $val['no_wo']; ?></td>
                    <td><?= $val['model']; ?></td>
                    <td><?= $val['merk']; ?> <?= $val['tipe']; ?></td>
                    <td><?= $val['nama']; ?></td>
                    <td><?= Yii::$app->landa->date2Ind($val['tgl_keluar']); ?>&nbsp;</td>
                    <td></td>

                </tr>
                <?php
            }
        }
    }
    ?>
</table>