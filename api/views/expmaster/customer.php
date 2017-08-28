<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-customer.xls");
?>
<center><h3>Data Master Customer</h3>
Tanggal Cetak : <?=Yii::$app->landa->date2Ind(date('d M Y'))?>
</center>

<br>
<br>
<table border="1">
    <tr>
        <th>Kode Customer</th>
        <th>Nama Customer</th>
        <th>Market</th>
        <th>Alamat 1</th>
        <th>Telpon</th>
        <th>Hp</th>
        <th>Email</th>
        <th>Contact Person</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kd_cust']?></td>
            <td><?=$arr['nm_customer']?></td>
            <td><?=$arr['market']?></td>
            <td><?=$arr['alamat1']?></td>
            <td>&nbsp;<?=$arr['telp']?></td>
            <td>&nbsp;<?=$arr['hp']?></td>
            <td><?=$arr['email']?></td>
            <td>&nbsp;<?=$arr['cp']?></td>
            
        </tr>
    <?php } ?>
</table>

