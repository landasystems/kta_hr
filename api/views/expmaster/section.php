<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-section.xls");
?>
<h3>Data Master Section</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Section</th>
        <th>Departement</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['id_section']?></td>
            <td><?=$arr['section']?></td>
            <td><?=$arr['department']?></td>
        </tr>
    <?php } ?>
</table>

