<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-PO.xls");
?>
<h3>PT. KARYA TUGAS ANDA</h3>
Jl. raya Sukorejo No. 1 Sukorejo 67161 Pasuruan, Jawa Timur
<br>
Telp: +62 343 611161 Fax: +62 343 612688 Email: kta@tugasanda.com
<hr>
<br>
<center><b>REKAP PURCHASE ORDER</b></center>
<br><br>
<?php
$data = array();
$i = 0;

foreach ($models as $key => $val) {
    $data[$val['nota']]['body']['jml'] = isset($data[$val['nota']]['body']['jml']) ? $data[$val['nota']]['body']['jml'] . $val['jml'] . '<br>' : $val['jml'] . '<br>';
    $data[$val['nota']]['body']['bayar'] = isset($data[$val['nota']]['body']['bayar']) ? $data[$val['nota']]['body']['bayar'] . ($val['bayar'] == '0') ? 'Tunai' : 'Kredit' . '<br>' : ($val['bayar'] == '0') ? 'Tunai' : 'Kredit' . '<br>';
    $data[$val['nota']]['body']['total'] = isset($data[$val['nota']]['body']['total']) ? $data[$val['nota']]['body']['total'] . $val['jml'] * $val['harga'] . '<br>' : $val['jml'] * $val['harga'].'<br>';
    $data[$val['nota']]['body']['ket'] = isset($data[$val['nota']]['body']['ket']) ? $data[$val['nota']]['body']['ket'] . $val['ket'] . '<br>' : $val['ket'] . '<br>';
    $data[$val['nota']]['body']['tgl_pengiriman'] = isset($data[$val['nota']]['body']['tgl_pengiriman']) ? $data[$val['nota']]['body']['tgl_pengiriman'] . $val['tgl_pengiriman'] . '<br>' : $val['tgl_pengiriman'] . '<br>';
    $data[$val['nota']]['body']['harga'] = isset($data[$val['nota']]['body']['harga']) ? $data[$val['nota']]['body']['harga'] . $val['harga'] . '<br>' : $val['harga'] . '<br>';
    $data[$val['nota']]['body']['nama_supplier'] = isset($data[$val['nota']]['body']['nama_supplier']) ? $data[$val['nota']]['body']['nama_supplier'] . $val['nama_supplier'] . '<br>' : $val['nama_supplier'] . '<br>';
    $data[$val['nota']]['body']['no_bbm'] = isset($data[$val['nota']]['body']['no_bbm']) ? $data[$val['nota']]['body']['no_bbm'] . $val['no_bbm'] . '<br>' : $val['no_bbm'] . '<br>';
    $data[$val['nota']]['body']['kd_barang'] = isset($data[$val['nota']]['body']['kd_barang']) ? $data[$val['nota']]['body']['kd_barang'] . $val['kd_barang'] . '<br>' : $val['kd_barang'] . '<br>';
    $data[$val['nota']]['body']['nm_barang'] = isset($data[$val['nota']]['body']['nm_barang']) ? $data[$val['nota']]['body']['nm_barang'] . $val['nm_barang'] . '<br>' : $val['nm_barang'] . '<br>';
    $data[$val['nota']]['title']['nota'] = $val['nota'];
    $data[$val['nota']]['title']['suplier'] = $val['nama_supplier'];
    $data[$val['nota']]['title']['no_bbm'] = $val['no_bbm'];
    $i++;
}
?>
<table border="1">
    <tr>
        <th valign="top">No PO</th>
        <th valign="top">Supplier</th>
        <th valign="top">NO BBM</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Qty</th>
        <th>Harga</th>
        <th>Total</th>
        <th>Tgl Kirim</th>
        <th>Jenis Bayar</th>
        <th>Keterangan</th>
    </tr>
    <?php
    foreach ($data as $key) {
        ?>
        <tr>
            <td valign="top"><?php echo $key['title']['nota'] ?></td>
            <td valign="top"><?php echo $key['title']['suplier'] ?></td>
            <td valign="top"><?php echo $key['title']['no_bbm'] ?></td>
            <td style="text-align: center">&nbsp;<?php echo $key['body']['kd_barang'] ?></td>
            <td><?php echo $key['body']['nm_barang'] ?></td>
            <td style="text-align: right">&nbsp;<?php echo $key['body']['jml'] ?></td>
            <td style="text-align: right">&nbsp;<?php echo $key['body']['harga'] ?></td>
            <td style="text-align: right">&nbsp;<?php echo $key['body']['total'] ?></td>
            <td><?php echo $key['body']['tgl_pengiriman'] ?></td>
            <td><?php echo $key['body']['bayar'] ?></td>
            <td><?php echo $key['body']['ket'] ?></td>
        </tr>
        <?php
    }
    ?>
</table>
