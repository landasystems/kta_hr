<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-Barang_Keluar.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com



<table>
    <tr>
        <td colspan="3" style="border: 1px solid #000000">
    <center><b>REKAP BARANG KELUAR</b></center>
    <br>
    <center>BBK</center>
    <br>
    No Dok : FR-WHS-015-REV.00
</td>
<td colspan="4" style="border: 1px solid #000000">
    <table>
        <tr>
            <td>Nomer</td>
            <td> : </td>
        </tr>
        <tr>
            <td>Kategori</td>
            <td> : </td>
        </tr>
        <tr>
            <td>Periode</td>
            <?php
                if (!empty($filter['tgl_periode'])) {
                    $value = explode(' - ', $filter['tgl_periode']);
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
            <td> : <?php echo date('d/m/Y') ?></td>
        </tr>
    </table>
</td>
   <td colspan="2" style="border: 1px solid #000000" valign="top">
    <center><b>Dibuat oleh</b></center>
    
</td>
   <td colspan="0" style="border: 1px solid #000000" valign="top">
    <center><b>Diperiksa oleh</b></center>
    
</td>
   <td colspan="0" style="border: 1px solid #000000" valign="top">
    <center><b>Diketahui oleh</b></center>
    
</td>
</tr>
</table>

<table border="1">
    <tr>
        <th>#</th>
        <th>TANGGAL</th>
        <th>NO WO</th>
        <th>NO BBK</th>
        <th>BAGIAN</th>
        <th>PLK kERJA</th>
        <th>KODE BARANG</th>
        <th>NAMA BARANG</th>
        <th>SAT</th>
        <th>JML</th>
        <th>KET</th>
    </tr>
    <?php
    $no = 0;
    foreach ($models as $key) {
        $no++;
        ?>
        <tr>
            <td valign="top">&nbsp;<?php echo $no ?></td>
            <td valign="top"><?php echo date('d/m/y', strtotime($key['tanggal'])) ?></td>
            <td valign="top">&nbsp;<?php echo $key['no_wo'] ?></td>
            <td valign="top">&nbsp;<?php echo $key['no_bbk'] ?></td>
            <td valign="top"><?php echo $key['jabatan'] ?></td>
            <td valign="top"><?php echo $key['nama'] ?></td>
            <td valign="top">&nbsp;<?php echo $key['kd_barang'] ?></td>
            <td valign="top"><?php echo $key['nm_barang'] ?></td>
            <td valign="top"><?php echo $key['satuan'] ?></td>
            <td valign="top">&nbsp;<?php echo $key['jml'] ?></td>
            <td valign="top"><?php echo $key['ket'] ?></td>
        </tr>
        <?php
    }
    ?>
</table>
