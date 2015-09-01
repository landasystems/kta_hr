<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-ujimutu.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>LAPORAN REKAP UJI MUTU</b></center>
<br><br>


<table border="1">
    <tr>
        <td rowspan="4" colspan="3">
            <br>
    <center><b>LAPORAN PENERIAMAAN UJI MUTU</b></center>
    <br><br>
    <center>No Dok : FR-SS-014</center>
    <br><br>

    </td>
    <td rowspan="4" colspan="5" valign="top">
        <table>
            <tr>
                <td>PERIODE</td>
                <?php
                if (!empty($filter['tgl_periode'])) {
                    $value = explode(' - ', $filter['tgl_periode']);
                    $start = date("d/m/Y", strtotime($value[0]));
                    $end = date("d/m/Y", strtotime($value[1]));
                } else {
                    $start = '';
                    $end = '';
                }
                ?>
                <td> : <?php echo $start . ' - ' . $end ?></td>
            </tr>
            <tr>
                <td>CETAK</td>
                <td> : <?php echo date('d M Y') ?></td>
            </tr>
        </table>
    </td>
    <td>DIBUAT</td>
    <td>DIPERIKSA</td>
</tr>
<tr>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
</tr>
<tr></tr>
<tr>
    <td>Tgl :</td>
    <td></td>
</tr>
</table>
<table border="1">
    <tr>
        <th>WO</th>
        <th>Pembuatan RB</th>
        <th>Pengajuan UM</th>
        <th>No. Register UM</th>
        <th>Merk/Type</th>
        <th>Chassis</th>
        <th>Customer</th>
    </tr>
    <?php
    foreach ($models as $key) {
        ?>
        <tr>
        <td valign="top">&nbsp;<?=$key['no_wo'];?></td>
        <td valign="top"><?=date('d m Y', strtotime($key['tanggal_rubah']))?></td>
        <td valign="top"><?=date('d m Y', strtotime($key['tgl']))?></td>
        <td valign="top">&nbsp;<?=$key['kd_uji'];?></td>
        <td valign="top">&nbsp;<?=$key['merk'];?>/<?=$key['tipe'];?></td>
        <td valign="top">&nbsp;<?=$key['no_chassis'];?></td>
        <td valign="top">&nbsp;<?=$key['nm_customer'];?></td>
        </tr>
        <?php
    }
    ?>
</table>
