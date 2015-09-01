<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-retur-Barang_Masuk.xls");
?>





<table>
    <tr>
        <td colspan="12" style="border-bottom: 1px solid #000000">
            <h3>PT. KARYA TUGAS ANDA</h3>
            Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
            <br>
            Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
        </td>

    </tr>
    <tr>
        <td colspan="12" >
    <center>LAPORAN BUKTI BARANG MASUK  </center>
        </td>

    </tr>
</table>
<table border="1">
    <tr>
        <th st-sort="tgl_nota">#</th>
        <th st-sort="tgl_nota">No SPP</th>
        <th st-sort="spp">No BBM</th>
        <th st-sort="spp">No PO</th>
        <th st-sort="spp">SURAT JALAN</th>
        <th st-sort="spp">SUPPLIER</th>
        <th st-sort="spp">TGL TERIMA</th>
        <th st-sort="spp">KODE BARANG</th>
        <th st-sort="spp">NAMA BARANG</th>
        <th st-sort="spp">SAT</th>
        <th st-sort="spp">QTY</th>
        <th st-sort="spp">KETERNGAN</th>
    </tr>
    <?php
    $no = 0;
    foreach ($models as $key) {
        $no++;
        ?>
        <tr>
            <td valign="top"><?php echo $no ?></td>
            <td valign="top"><?php echo $key['no_spp'] ?></td>
            <td valign="top"><?php echo $key['no_bbm'] ?></td>
            <td valign="top"><?php echo $key['nota'] ?></td>
            <td valign="top"><?php echo $key['surat_jalan'] ?></td>
            <td valign="top"><?php echo $key['nama_supplier'] ?></td>
            <td valign="top"><?php echo date('d M Y', strtotime($key['tanggal_nota'])) ?></td>
            <td valign="top"><?php echo $key['kd_barang'] ?></td>
            <td valign="top"><?php echo $key['nm_barang'] ?></td>
            <td valign="top"><?php echo $key['satuan'] ?></td>
            <td valign="top"><?php echo $key['jumlah'] ?></td>
            <td valign="top"><?php echo $key['keterangan'] ?></td>
        </tr>
        <?php
    }
    ?>
</table>
