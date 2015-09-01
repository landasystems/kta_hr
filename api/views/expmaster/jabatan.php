<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-jabatan.xls");
?>
<h3>Data Master Jabatan</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Jabatan</th>
        <th>Sub Section</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['id_jabatan']?></td>
            <td><?=$arr['jabatan']?></td>
            <td><?=$arr['kerja']?></td>
            
        </tr>
    <?php } ?>
</table>

