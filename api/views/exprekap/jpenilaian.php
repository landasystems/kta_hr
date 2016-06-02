<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-master-jpenilaian.xls");
}
?>

<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
        <td rowspan="2" style="text-align: center;">
            LAPORAN MASTER JADWAL PENILAIAN
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

    <tr >
        <th>Kode</th>
        <th >Tanggal Penilaian</th>
        <th >Penilai</th>
        <th >Dept. Penilai</th>
        <th >Yang Dinilai</th>
        <th >Dept. Dinilai</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        ?>
        <tr>
            <td >&nbsp;<?= $arr['no_jpenilaian'] ?></td>
            <td style="text-align: center"><?= date('d-M-y', strtotime($arr['tgl_penilaian']))  ?></td>
            <td ><?= $arr['penilai'] ?></td>
            <td ><?= $arr['dep_penilai'] ?></td>
            <td ><?= $arr['nama'] ?></td>
            <td ><?= $arr['bagian'] ?></td>

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