<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-retur-Barang_Masuk.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com



<table>
    <tr>
        <td colspan="2" style="border: 1px solid #000000">
            <br><br>
    <center><b>LAPORAN REKAP DELIVERY UNIT</b></center>


</td>
<td colspan="4" style="border: 1px solid #000000">
    <table>

        <tr>
            <td>Periode</td>
            <?php
            if (!empty($filter['tgl_delivery'])) {
                $value = explode(' - ', $filter['tgl_delivery']);
                $start = date("d-m-Y", strtotime($value[0]));
                $end = date("d-m-Y", strtotime($value[1]));
            } else {
                $start = '';
                $end = '';
            }
            ?>
            <td> : <?php echo $start . ' - ' . $end ?></td>
        </tr>
        <tr>
            <td>Cetak</td>
            <td> : <?php echo date('d-m-Y') ?></td>
        </tr>
        <tr>
            <td colspan='2'></td>
        </tr>
    </table>
</td>
<td colspan="1" style="border: 1px solid #000000" valign="top">
<center><b>DIBUAT</b></center>

</td>
<td colspan="1" style="border: 1px solid #000000" valign="top">
<center><b>DIKETAHUI</b></center>

</td>

</tr>
</table>

<table border="1">
    <tr>
        <th   st-sort="tgl_nota">#</th>
        <th   st-sort="tgl_nota">TGL DELV</th>
        <th   st-sort="spp">UNIT</th>
        <th   st-sort="spp">NO DELV</th>
        <th   st-sort="spp">NO WO</th>
        <th  st-sort="spp">CUSTOMER</th>
        <th  st-sort="spp">MERK / TYPE</th>
        <th st-sort="spp">MODEL</th>
    </tr>

    <?php
    $no = 0;
    $total = 0;
    foreach ($models as $key) {
        $no++;
        $total += $key['jml_unit'];
        ?>
        <tr>
            <td valign="top"><?php echo $no ?></td>
            <td valign="top"><?php echo date('d - m - Y', strtotime($key['tgl_delivery'])) ?></td>
            <td valign="top" align='center'><?php echo $key['jml_unit'] ?></td>
            <td valign="top"><?php echo $key['no_delivery'] ?></td>
            <td valign="top"><?php echo $key['no_wo'] ?></td>
            <td valign="top"><?php echo $key['nm_customer'] ?></td>
            <td valign="top"><?php echo $key['merk'] . ' - ' . $key['tipe'] ?></td>
            <td valign="top"><?php echo $key['model'] ?></td>

        </tr>
        <?php
    }
    ?>
        <tr>
            <th></th>
            <th>TOTAL</th>
            <th><?php echo $total; ?></th>
            <th colspan='5'></th>
        </tr>
        <tr>
            <td colspan='8' rowspan='4'></td>
        </tr>
</table>
