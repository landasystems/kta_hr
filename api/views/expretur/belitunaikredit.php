<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-laporan-pembelian-tunai-kredit.xls");
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

    $data[$val['bayar']]['title']['status_bayar'] = $val['bayar'];
    $data[$val['bayar']]['nota'][$val['nota']]['title'] = $val['nota'];

    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['tgl_terima'] = $val['tgl_terima'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['no_bbm'] = $val['no_bbm'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['kd_barang'] = $val['kd_barang'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['nm_barang'] = $val['nm_barang'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['satuan'] = $val['satuan'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['jml'] = $val['jml'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['harga'] = $val['harga'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['jenis_brg'] = $val['jenis_brg'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['surat_jalan'] = $val['surat_jalan'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['nota'] = $val['nota'];
    $data[$val['bayar']]['nota'][$val['nota']]['body'][$i]['nama_supplier'] = $val['nama_supplier'];
    $i++;
}
?>

<table border="1">
    <tr>
        <td rowspan="4" colspan="3">
            <br>
    <center><b>LAPORAN PEMBELIAN</b></center>
    <br><br>
    <center>No Dok : FR-PCH-019Rev.0</center>
    <br><br>

    </td>
    <td rowspan="4" colspan="5" valign="top">
        <table>
            <tr>
                <td>NOMOR</td>
                <td> : </td>
            </tr>
            <tr>
                <td>PERIODE</td>
                <?php
                if (!empty($filter['tanggal'])) {
                    $value = explode(' - ', $filter['tanggal']);
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
                <td> : <?php echo date('d/m/Y') ?></td>
            </tr>
        </table>
    </td>
    <td>DIBUAT</td>
    <td>DIPERIKSA</td>
    <td>DIKETAHUI</td>
</tr>
<tr>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
    <td rowspan="2"></td>
</tr>
<tr></tr>
<tr>
    <td>ADM PURC</td>
    <td>PURC HEAD</td>
    <td>FINANCE HEAD</td>
</tr>
</table>
<table border="1">
    <tr>
        <th valign="top">TANGGAL</th>
        <th valign="top">NO BBM</th>
        <th valign="top">KODE BARANG</th>
        <th>NAMA BARANG</th>
        <th>SAT</th>
        <th>JUMLAH</th>
        <th>HARGA SATUAN</th>
        <th>JENIS BARANG</th>
        <th>SURAT JALAN</th>
        <th>PO</th>
        <th>SUPPLIER</th>
    </tr>
    <?php
    foreach ($data as $key) {
        $status_bayar = ($key['title']['status_bayar'] == 0) ? 'Tunai' : 'Kredit';
        ?>
        <tr><td style="background-color: rgb(226, 222, 222)" colspan="11"><b><?= $status_bayar ?></b></td></tr>
                    <?php
                    foreach ($key['nota'] as $keys) {
                        ?>
            <tr><td colspan="11"><?= $keys['title'] ?></td></tr>
            <?php
            foreach ($keys['body'] as $val) {
                ?>
                <tr>
                    <td><?= $val['tgl_terima'] ?></td>
                    <td><?= $val['no_bbm'] ?></td>
                    <td><?= $val['kd_barang'] ?></td>
                    <td><?= $val['nm_barang'] ?></td>
                    <td><?= $val['satuan'] ?></td>
                    <td><?= $val['jml'] ?></td>
                    <td><?= $val['harga'] ?></td>
                    <td><?= $val['jenis_brg'] ?></td>
                    <td><?= $val['surat_jalan'] ?></td>
                    <td><?= $val['nota'] ?></td>
                    <td><?= $val['nama_supplier'] ?></td>
                </tr>
                <?php
            }
        }
    }
    ?>
</table>
