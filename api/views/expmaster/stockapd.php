<?php
if (!isset($_GET['print'])) {
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-stok-apd.xls");
}
?>
<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
        <td rowspan="2" style="text-align: center;">
             LAPORAN MASTER JABATAN
        </td>
        <td rowspan="2" style="text-align: center"> Tgl Pelaporan :  <?= date('d F Y'); ?> </td>
        <td style="text-align: center;">Diketahui</td>
        <td style="text-align: center;">Diperiksa</td>
        <td style="text-align: center;">Dibuat</td>
    </tr>
    <tr>
        <td style="width: 100px;height: 80px;"></td>
        <td style="width: 100px;"></td>
        <td style="width: 100px;" ></td>
    </tr>
        <tr>
            <th>No</th>
            <th>Kode APD</th>
            <th colspan="2">Nama Alat Pelindung Diri(APD)</th>
            <th colspan="2">Jumlah</th>
        </tr>
    
        <?php
        $no = 1;
        foreach ($models as $arr) {
            ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $arr['kode_apd'] ?></td>
            <td style="text-align: center;" colspan="2"><?= $arr['nama_apd'] ?></td>
            <td style="text-align: center;" colspan="2"><?= $arr['jumlah_apd'] ?></td>
        </tr>
        <?php
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