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
    <center><b>LAPORAN RETUR BARANG MASUK</b></center>
    <br><br>
    <center>(BBM)</center>

</td>
<td colspan="4" style="border: 1px solid #000000">
    <table>
        <tr>
            <td>Nomer</td>
            <td> : </td>
        </tr>
        <tr>
            <td>Periode</td>
            <?php
            if (!empty($filter['tanggal'])) {
                $value = explode(' - ', $filter['tanggal']);
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
<td colspan="2" style="border: 1px solid #000000" valign="top">
<center><b>Diperiksa oleh</b></center>

</td>

</tr>
</table>

<table border="1">
    <tr>
        <th rowspan='2' st-sort="tgl_nota">#</th>
        <th rowspan='2' st-sort="tgl_nota">Tanggal</th>
        <th rowspan='2' st-sort="spp">KODE BARANG</th>
        <th rowspan='2' st-sort="spp">GOLONGAN</th>
        <th rowspan='2' st-sort="spp">NAMA BARANG</th>
        <th rowspan='2' st-sort="spp">SAT</th>
        <th  colspan='3' st-sort="spp">NOMER</th>
        <th rowspan='2' st-sort="spp">KET</th>
    </tr>
    <tr>
        <th st-sort="tgl_nota">BBM</th>
        <th st-sort="spp">RETUR</th>
        <th st-sort="spp">SURAT JALAN</th>
    </tr>
    <?php
    $no = 0;
    foreach ($models as $key) {
        $no++;
        ?>
        <tr>
            <td valign="top"><?php echo $no ?></td>
            <td valign="top"><?php echo date('d m Y', strtotime($key['tanggal'])) ?></td>
            <td valign="top"><?php echo $key['kd_barang'] ?></td>
            <td valign="top"><?php echo $key['jenis_brg'] ?></td>
            <td valign="top"><?php echo $key['nm_barang'] ?></td>
            <td valign="top"><?php echo $key['satuan'] ?></td>
            <td valign="top"><?php echo $key['no_bbm'] ?></td>
            <td valign="top"><?php echo $key['no_retur_bbm'] ?></td>
            <td valign="top"><?php echo $key['surat_jalan'] ?></td>
            <td valign="top"><?php echo $key['ket'] ?></td>
        </tr>
        <?php
    }
    ?>
</table>
