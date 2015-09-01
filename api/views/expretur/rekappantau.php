<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-pemantauan-penerimaan-barang.xls");
?>



<table border="1">
    <tr>
        <td rowspan="4" colspan="3">
            <br>
    <center><b>LAPORAN PEMANTAUAN PENERIMAAN BARANG</b></center>
    <br><br>
    <center>No Dokumen FR-PCH-002REV.02</center>
    <br><br>

    </td>
    <td rowspan="4" colspan="5" valign="top">
        <table>
            <tr>
                <td>DEPARTEMENT</td>
                <td> : PURCHASSING</td>
            </tr>
            <tr>

                <td>PERIODE</td>
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
    <td>DIBUAT</td>
    <td>DIKETAHUI</td>
</tr>
<tr>
    <td rowspan="3"></td>
    <td rowspan="3"></td>
</tr>
<tr></tr>
<tr>
</tr>
</table>
<table border="1">
    <tr>
        <th rowspan="2">No</th>
        <th rowspan="2">No PO</th>
        <th rowspan="2">Kode Barang</th>
        <th rowspan="2">Nama Barang</th>
        <th rowspan="2">Sat</th>
        <th rowspan="2">QTY</th>
        <th rowspan="2">Deadline</th>
        <th colspan="2">Tanggal Kirim</th>
        <th rowspan="2">Status</th>
    </tr>
    <tr>
        <th>P</th>
        <th>A</th>
    </tr>
    <?php
    $no = 1;
    foreach ($models as $key) {
        if ($key['p'] == '0000-00-00') {
            $key['p'] = '';
        }
        if($key['a'] == '0000-00-00'){
            $key['a'] = '';
        }
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= $key['nota'] ?></td>
            <td><?= $key['kd_barang'] ?></td>
            <td><?= $key['nm_barang'] ?></td>
            <td><?= $key['satuan'] ?></td>
            <td><?= $key['jml'] ?></td>
            <td><?= $key['jatuh_tempo'] ?></td>
            <td><?= $key['p'] ?></td>
            <td><?= $key['a'] ?></td>
            <td></td>
        </tr>
        <?php
        $no++;
    }
    ?>
</table>
