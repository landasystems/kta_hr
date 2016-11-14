<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-master-filelegalitas.xls");
}
?>

<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
        <td rowspan="2" style="text-align: center;">
            LAPORAN MASTER FILE LEGALITAS
        </td>
        <td rowspan="2" style="text-align: center"> Tgl Pelaporan :  <?= date('d F Y'); ?> </td>
        <td style="text-align: center;">Diketahui</td>
        <td colspan="2" style="text-align: center;">Diperiksa</td>
        <td colspan="2" style="text-align: center;">Dibuat</td>
    </tr>
    <tr>
        <td style="width: 100px;height: 80px;"></td>
        <td colspan="2" style="width: 100px;"></td>
        <td colspan="2" style="width: 100px;"></td>
    </tr>

    <tr >
        <th>Kode</th>
        <th>No Fie</th>
        <th>Nama Legalitas</th>
        <th>Instansi</th>
        <th>Atas Nama</th>
        <th>Jenis Legalitas</th>
        <th>Periode Akhir</th>
        <th>Keterangan</th>
    </tr>
    <?php
    $no = 1;
    foreach ($models as $arr) {
        ?>
        <tr>
            <td >&nbsp;<?= $no ?></td>
            <td >&nbsp;<?= $arr['no_file'] ?></td>
            <td style="text-align: center; text-transform: uppercase;">&nbsp;<?= $arr['nm_file'] ?></td>
            <td style="text-transform: uppercase;">&nbsp;<?= $arr['instansi'] ?></td>
            <td style="text-transform: uppercase;">&nbsp;<?= $arr['atas_nm'] ?></td>
            <td style="text-transform: uppercase;">&nbsp;<?= $arr['jns_legalitas'] ?></td>
            <td style="text-transform: uppercase;min-width: 100px;text-align: center;"><?= $arr['periode_akhir'] ?></td>
            <td style="text-transform: uppercase;">&nbsp;<?= $arr['keterangan'] ?></td>

        </tr>
        <?php
        $no++;
    }
    ?>
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