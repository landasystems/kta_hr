<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-department.xls");
}
?>
<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
        <td rowspan="2" style="text-align: center;">
            LAPORAN MASTER DEPARTMENT
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
        <th>Kode</th>
        <th colspan="5">Department</th>

    </tr>
<?php
foreach ($models as $arr) {
    ?>
        <tr>
            <td>&nbsp;<?= $arr["id_department"]; ?></td>
            <td colspan="5" style="text-align: center"><?= $arr["department"]; ?></td>

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