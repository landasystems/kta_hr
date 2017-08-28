<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename='excel-master-jenis-barang.xls'");
?>
<h3>Data Master Jenis Barang</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode Jenis</th>
        <th>Jenis Barang</th>
        
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kd_jenis']?></td>
            <td><?=$arr['jenis_brg']?></td>
            
        </tr>
    <?php } ?>
</table>

