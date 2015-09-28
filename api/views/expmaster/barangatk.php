<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-barang.xls");
?>
<h3>Laporan Stok ATK</h3>
<br><br>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode ATK</th>
            <th>Nama Alat Tulis Kantor(ATK)</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $arr) {
            echo '<tr>';
            echo '<td>'.$no++.'</td>';
            echo '<td>'.$arr['kode_brng'].'</td>';
            echo '<td>'.$arr['nama_brng'].'</td>';
            echo '<td>'.$arr['jumlah_brng'].'</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

