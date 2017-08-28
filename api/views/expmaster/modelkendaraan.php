<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-model-kendaraan.xls");
?>
<h3>Data Master Model kendaraan</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Model</th>
        <th>Standard</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kd_model']?></td>
            <td><?=$arr['model']?></td>
            <td><?=($arr['standard'] == '1') ? 'Standard' : 'Tidak Standard'?></td>
            
        </tr>
    <?php } ?>
</table>

