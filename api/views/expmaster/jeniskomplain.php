<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-jenis-komplain.xls");
?>
<h3>Data Master Jenis Komplain</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode Jenis Komplain</th>
        <th>Nama</th>
        <th>Kategori</th>
        <th>Satuan</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kd_jns']?></td>
            <td><?=$arr['stat']?></td>
            <td><?=$arr['bag']?></td>
            <td><?=$arr['jns_komplain']?></td>
            
        </tr>
    <?php } ?>
</table>

