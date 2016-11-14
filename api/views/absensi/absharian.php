<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekap-absensi-harian.xls");
}
?>
<table width="100%" style="border-collapse: collapse; " border="1" >
    <thead>
        <tr>
            <th rowspan="2"colspan="2">Rekap Absensi Harian<br>Tgl Pelaporan :  <?= date('d F Y'); ?> </th>
            <th>Dibuat Oleh</th>
            <th>Diperiksa Oleh</th>
        </tr>
        <tr>
            <th style="height:50px;"></th>
            <th></th>
        </tr>
        <tr>
            <th>NIK</th>
            <th>Nama Pegawai</th>
            <th style="text-align: center;">Masuk</th>
            <th style="text-align: center;">Keluar</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($models as $val) {
            ?>
            <tr>
                <td>&nbsp;<?= $val['nik'] ?></td>
                <td><?= $val['nama'] ?></td>
                <td style="text-align: center;"><?= !empty($val['masuk']) ? $val['masuk'] : "-" ?></td>
                <td style="text-align: center;"><?= !empty($val['keluar']) ? $val['keluar'] : "-" ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
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