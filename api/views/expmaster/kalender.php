<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-kalender.xls");
?>
<h3>Data Master Kalender</h3>
<br><br>
<table border="1">
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Kategori</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['no']?></td>
            <td>&nbsp;<?=date('d-m-Y',  strtotime($arr['tgl']))?></td>
            <td><?=$arr['ket']?></td>
            
        </tr>
    <?php } ?>
</table>

