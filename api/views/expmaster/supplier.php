<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-supplier.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>

<center><h3>Laporan Data Supplier</h3></center>
<br><br>
<table border="1">
    <tr>
        <th>Kode Supplier</th>
        <th>Nama Supplier</th>
        <th>Alamat</th>
        <th>Telp</th>
        <th>Contact Person</th>
        <th>Email</th>
        <th>Produk</th>
        
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kd_supplier']?></td>
            <td><?=$arr['nama_supplier']?></td>
            <td><?=$arr['alamat']?></td>
            <td>&nbsp;<?=$arr['telp']?></td>
            <td>&nbsp;<?=$arr['cp']?></td>
            <td><?=$arr['email']?></td>
            <td><?=$arr['ket']?></td>
            
        </tr>
    <?php } ?>
</table>

