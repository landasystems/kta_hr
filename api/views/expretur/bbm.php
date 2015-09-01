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
    <center><b>REKAP BARANG MASUK</b></center>
    <br><br>
    <center>(BBM)</center>
    
    No Dok : FR-WHS-012-REV.00
</td>
<td colspan="3" style="border: 1px solid #000000">
    <table>
        <tr>
            <td>Nomer Kategori</td>
            <td> : </td>
        </tr>
        <tr>
                <td>Periode</td>
                <?php
                if (!empty($filter['tgl_nota'])) {
                    $value = explode(' - ', $filter['tgl_nota']);
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
   <td colspan="2" style="border: 1px solid #000000" valign="top">
    <center><b>Diketahui oleh</b></center>
    
</td>
</tr>
</table>

<table border="1">
    <tr>
        <th st-sort="tgl_nota">#</th>
        <th st-sort="tgl_nota">Tanggal</th>
        <th st-sort="spp">No BBM</th>
        <th st-sort="spp">KODE BRG</th>
        <th st-sort="spp">NAMA BARANG</th>
        <th st-sort="spp">SAT</th>
        <th st-sort="spp">JML</th>
        <th st-sort="spp">SURAT JALAN</th>
        <th st-sort="spp">PO</th>
        <th st-sort="spp">SUPPLIER</th>
        <th st-sort="spp">KET</th>
    </tr>
    <?php
    $no = 0;
    foreach ($models as $key) {
        $no++;
        ?>
        <tr>
            <td valign="top"><?php echo $no ?></td>
            <td valign="top"><?php echo date('d M Y', strtotime($key['tanggal_nota'])) ?></td>
            <td valign="top"><?php echo $key['no_bbm'] ?></td>
            <td valign="top"><?php echo $key['kd_barang'] ?></td>
            <td valign="top"><?php echo $key['nm_barang'] ?></td>
            <td valign="top"><?php echo $key['satuan'] ?></td>
            <td valign="top"><?php echo $key['jumlah'] ?></td>
            <td valign="top"><?php echo $key['surat_jalan'] ?></td>
            <td valign="top"><?php echo $key['no_po'] ?></td>
            <td valign="top"><?php echo $key['nama_supplier'] ?></td>
            <td valign="top"><?php echo $key['keterangan'] ?></td>
        </tr>
        <?php
    }
    ?>
</table>
