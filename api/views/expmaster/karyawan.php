<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-barang.xls");
$data = array();
$i = 0;
foreach ($models as $key => $val) {
    $data[$val['kat']]['title']['kategory'] = $val['kat'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['title'] = $val['jenis_brg'];

    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['kd_barang'] = $val['kd_barang'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['jenis_brg'] = $val['jenis_brg'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['satuan'] = $val['satuan'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['max'] = $val['max'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['min'] = $val['min'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['saldo'] = $val['saldo'];
    $data[$val['kat']]['jenis_brg'][$val['jenis_brg']]['body'][$i]['qty'] = $val['qty'];
    $i++;
}
?>
<h3>Data Master Barang</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode Barang</th>
        <th>Nama barang</th>
        <th>Satuan</th>
        <th>Min Stok</th>
        <th>Maks Stok</th>
        <th>Stok</th>
        <th>Qty</th>
    </tr>
    <?php
    foreach ($data as $arr) {
        ?>
    <tr><td colspan="7" style="background-color: rgb(226, 222, 222);">&nbsp;<b><?= $arr['title']['kategory'] ?></b></td><tr>
            <?php
            foreach ($arr['jenis_brg'] as $keys) {
                ?>
            <tr>
                <td></td>
                <td><b><?= $keys['title'] ?></b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

            </tr>
            <?php
            foreach ($keys['body'] as $value) {
                ?>
                <tr>
                    <td>&nbsp;<?= $value['kd_barang'] ?></td>
                    <td><?= $value['nm_barang'] ?></td>
                    <td>&nbsp;<?= $value['satuan'] ?></td>
                    <td>&nbsp;<?= $value['min'] ?></td>
                    <td>&nbsp;<?= $value['max'] ?></td>
                    <td>&nbsp;<?= $value['qty'] ?></td>
                    <td>&nbsp;<?= $value['saldo'] ?></td>

                </tr>
                <?php
            }
        }
    }
    ?>
</table>

