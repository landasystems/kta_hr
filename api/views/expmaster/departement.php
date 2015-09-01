<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-department.xls");
?>
<h3>Data Master Department</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Department</th>
        
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr["id_department"];?></td>
            <td><?=$arr["department"];?></td>
            
        </tr>
    <?php } ?>
</table>

