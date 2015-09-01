<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-bom.xls");
$i = 0;

$detBbk = array();
foreach ($modelbbk as $valBbk) {
    $detBbk[$valBbk['kd_barang']]['jml'] = isset($detBbk[$valBbk['kd_barang']]['jml']) ? $detBbk[$valBbk['kd_barang']]['jml'] + $valBbk['jml'] : $valBbk['jml'];
}

$data = array();
foreach ($models as $val) {
    $jKeluar = isset($detBbk[$val['kd_barang']]['jml']) ? $detBbk[$val['kd_barang']]['jml'] : ' ';

    $data[$val['no_wo']]['no_wo'] = $val['no_wo'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['jabatan'] = $val['jabatan'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['no_wo'] = $val['no_wo'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['kode_barang'] = $val['kd_barang'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['nama_barang'] = $val['nm_barang'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['satuan'] = $val['satuan'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['qty'] = $val['qty'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['ket'] = $val['ket'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['harga'] = $val['harga'];
    $data[$val['no_wo']]['jab'][$val['id_jabatan']]['body'][$val['kd_barang']]['jml_keluar'] = $jKeluar;
    $i++;
}
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>REALISASI BILL OF MATERIAL</b></center>
<br>
<br>
<?php
foreach ($data as $value) {
    ?>
    <b>NO WO : <?php echo isset($value['no_wo']) ? $value['no_wo'] : '-' ?></b>
    <br><br><br>
    <table width="100%" style="padding: 5px;" border="1">
        <tr height="25">
            <td>Kode Barang</td>
            <td>Nama Barang</td>
            <td>Satuan</td>
            <td>Harga</td>
            <td>Standar</td>
            <td>Realisasi</td>
            <td>Keterangan</td>
        </tr>
        <?php
        foreach ($value['jab'] as $key => $bag) {
            echo '<tr height="25">
                        <td colspan="7" style="background-color: rgb(226, 222, 222)">' . $bag['jabatan'] . '</td>
                    </tr>';
            foreach ($bag['body'] as $det) {
                echo '<tr height="25">
                        <td align="center">' . $det['kode_barang'] . '</td>
                        <td>' . $det['nama_barang'] . '</td>
                        <td align="right">' . $det['harga'] . '</td>
                        <td align="center">' . $det['satuan'] . '</td>
                        <td align="center">' . $det['qty'] . '</td>
                        <td align="center">' . $det['jml_keluar'] . '</td>
                        <td>' . $det['ket'] . '</td>
                    </tr>';
            }
        }
        ?>
    </table>
    <?php
}
?>