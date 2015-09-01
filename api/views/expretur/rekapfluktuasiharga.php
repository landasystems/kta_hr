<?php
header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-rekap-fluktuasi-harga.xls");
?>



<table border="1">
    <tr>
        <td rowspan="4" colspan="3">
            <br>
    <center><b>FLUKTUASI HARGA</b></center>
    <br><br>
    <center>NO DOKUMEN FR-PCH-004 Rev.03</center>
    <br><br>

    </td>
    <td rowspan="4" colspan="5" valign="top">
        <table>
            <tr>
                <td>Departemen Purchassing</td>

            </tr>
            <tr>
                <td>Periode</td>
                <?php
                if (!empty($filter['tanggal'])) {
                    $value = explode(' - ', $filter['tanggal']);
                    $start = date("d/m/Y", strtotime($value[0]));
                    $end = date("d/m/Y", strtotime($value[1]));
                } else {
                    $start = '';
                    $end = '';
                }
                ?>
                <td> : <?php echo $start . ' - ' . $end ?></td>
            </tr>

        </table>
    </td>
    <td>Dibuat</td>
    <td>Diketahui</td>
</tr>
<tr>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
</tr>
<tr></tr>
<tr>
    <td></td>
    <td></td>
</tr>
</table>
<table border="1">
    <tr>
        <th>No</th>
        <th>Supplier</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Sat</th>
        <th>Tanggal</th>
        <th>Harga Barang</th>
    </tr>
    <?php
    $no=1;
    foreach ($models as $key) {
        ?>
        <tr>
            <td valign="top">&nbsp;<?= $no; ?></td>
            <td valign="top"><?=$key['nama_supplier']?></td>
            <td valign="top">&nbsp;<?=$key['kd_barang']?></td>
            <td valign="top"><?= $key['nm_barang']; ?></td>
            <td valign="top"><?= $key['satuan'];?></td>
            <td valign="top"><?= $key['tgl_pengiriman']; ?></td>
            <td valign="top" style="text-align: right;">&nbsp;<?= $key['hrg_barang']; ?></td>
        </tr>
        <?php
        $no++;
    }
    ?>
</table>
