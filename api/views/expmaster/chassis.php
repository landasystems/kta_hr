<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-chassis.xls");
?>
<h3>Data Master Chassis</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode Chassis</th>
        <th>Merk</th>
        <th>Type</th>
        <th>Jenis</th>
        <th>WhellBase</th>
        <th>Model Chassis</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kd_chassis']?></td>
            <td><?=$arr['merk']?></td>
            <td><?=$arr['tipe']?></td>
            <td><?=$arr['jenis']?></td>
            <td><?=$arr['wheelbase']?></td>
            <td><?=$arr['model_chassis']?></td>
            
        </tr>
    <?php } ?>
</table>

