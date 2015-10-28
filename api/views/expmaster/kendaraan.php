<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-kendaraan.xls");
?>
<h3>Data Master Kendaraan</h3>
<br><br>
<table border="1">
    <tr>
        <th>NOPOL</th>
        <th>MERK</th>
        <th>TIPE</th>
        <th>MODEL</th>
        <th>WARNA</th>
        <th>THN PEMBUATAN</th>
        <th>NO RANGKA</th>
        <th>NO MESIN</th>
        <th>USER</th>
    </tr>
    <?php
    $arrIn = array();
    foreach ($models as $key => $arr) {
        $nopol = (!empty($arr['nopol'])) ? $arr['nopol'] : '';
        $merk = (!empty($arr['merk'])) ? $arr['merk'] : '';
        $type = (!empty($arr['tipe'])) ? $arr['tipe'] : '';
        $model = (!empty($arr['model'])) ? $arr['model'] : '';
        $warna = (!empty($arr['warna'])) ? $arr['warna'] : '';
        $thn_pembuatan = (!empty($arr['thn_pembuatan'])) ? $arr['thn_pembuatan'] : '';
        $no_rangka = (!empty($arr['no_rangka'])) ? $arr['no_rangka'] : '';
        $no_mesin = (!empty($arr['no_mesin'])) ? $arr['no_mesin'] : '';
        $user = (!empty($arr['user'])) ? $arr['user'] : '';
        ?>
        <tr>
            <td>&nbsp;<?= $nopol ?></td>
            <td>&nbsp;<?= $merk ?></td>
            <td>&nbsp;<?= $type?></td>
            <td>&nbsp;<?= $model ?></td>
            <td>&nbsp;<?= $warna ?></td>
            <td>&nbsp;<?= $thn_pembuatan ?></td>
            <td>&nbsp;<?= $no_rangka ?></td>
            <td>&nbsp;<?= $no_mesin ?></td>
            <td>&nbsp;<?= $user ?></td>

        </tr>
    <?php } ?>
</table>

