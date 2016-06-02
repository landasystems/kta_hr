<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-master-kendaraan.xls");
}
?>
<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
        <td rowspan="2" style="text-align: center;">
            LAPORAN MASTER KENDARAAN
        </td>
        <td rowspan="2" style="text-align: center"> Tgl Pelaporan :  <?= date('d F Y'); ?> </td>
        <td colspan="2" style="text-align: center;">Diketahui</td>
        <td colspan="2" style="text-align: center;">Diperiksa</td>
        <td colspan="2" style="text-align: center;">Dibuat</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 100px;height: 80px;"></td>
        <td colspan="2" style="width: 100px;"></td>
        <td colspan="2" style="width: 100px;" ></td>
    </tr>
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
            <td style="min-width: 90px;">&nbsp;<?= $nopol ?></td>
            <td style="text-align: center;">&nbsp;<?= $merk ?></td>
            <td>&nbsp;<?= $type ?></td>
            <td>&nbsp;<?= $model ?></td>
            <td>&nbsp;<?= $warna ?></td>
            <td>&nbsp;<?= $thn_pembuatan ?></td>
            <td>&nbsp;<?= $no_rangka ?></td>
            <td>&nbsp;<?= $no_mesin ?></td>
            <td>&nbsp;<?= $user ?></td>

        </tr>
    <?php } ?>
</table>

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