<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-subsetion.xls");
?>
<h3>Data Master Sub Section</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Sub Section</th>
        <th>Section</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kd_kerja']?></td>
            <td><?=$arr['kerja']?></td>
            <td><?=$arr['section']?></td>
            
        </tr>
    <?php } ?>
</table>

