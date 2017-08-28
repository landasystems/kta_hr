<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-master-karyawan.xls");
}
?>
<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
        <td rowspan="2" style="text-align: center;">
            LAPORAN MASTER KARYAWAN
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
</table >
<table width="100%" style="border-collapse: collapse;" border="1">
    <tr >
        <th>Kode</th>
        <th>Nama</th>
        <th>Status Karyawan</th>
        <th>Jabatan</th>
        <th>Lokasi Kantor</th>
        <th>Status</th>
    </tr>
    <?php
    foreach ($data as $arr) {
//echo $arr['kerja'];    
        ?>
        <tr>
            <td><?= $arr['nik'] ?></td>
            <td style="text-align: center;"><?= $arr['nama'] ?></td>
            <td style="text-transform: uppercase;"><?= $arr['status_karyawan'] ?></td>
            <td><?= $arr['jabatan'] ?></td>
            <td><?= $arr['lokasi_kntr'] ?></td>
            <td style="text-transform: uppercase;"><?= $arr['status'] ?></td>
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