<?php
//header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=excel-retur-Pergerakan-Barang.xls");
?>
<table width="100%" border="1">
    <thead>
        <tr>
            <td colspan="3" rowspan="2" align="center">
                <h2>LAPORAN STOK BAHAN MINGGUAN</h2>
                <p>No. Dokumen : FR-WHS-001.REEV.00</p>
            </td>
            <td rowspan="2" colspan="6"valign="top" width="50%">
                <table>
                    <tr valign="top">
                        <td width="75">Nomor</td>
                        <td>: </td>
                    </tr>
                    <tr valign="top">
                        <td>Periode</td>
                        <td>: <?php echo isset($periode) ? $periode : '-' ?></td>
                    </tr>
                    <tr valign="top">
                        <td>Cetak</td>
                        <td>: <?php echo date("d/m/Y"); ?></td>
                    </tr>
                </table>
            </td>
            <td align="center" valign="top" height="15" colspan="2">Dibuat Oleh</td>
            <td align="center" valign="top" colspan="2">Diperiksa Oleh</td>
            <td align="center" valign="top" colspan="2">Disetujui Oleh</td>
        </tr>
        <tr height="75">
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <th rowspan="3">NO</th>
            <th rowspan="3">KODE BARANG</th>
            <th rowspan="3">NAMA BARANG</th>
            <th rowspan="3">SAT</th>
            <th rowspan="3">STOK MINIM</th>
            <th rowspan="3">STOK AWAL</th>
            <th colspan="7">MUTASI</th>
            <th rowspan="3">SALDO AKHIR</th>
            <th rowspan="3">OPNAME</th>
        </tr>
        <tr>
            <th rowspan="2">MASUK</th>
            <th colspan="6">KELUAR</th>
        </tr>
        <tr>
            <?php
            foreach ($tgl as $tanggal) {
                echo '<th>' . date("d/m/y", strtotime($tanggal)) . '</th>';
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . $val['kd_barang'] . '</td>';
            echo '<td align="left">' . $val['barang'] . '</td>';
            echo '<td align="center">' . $val['satuan'] . '</td>';
            echo '<td align="center">' . $val['stok_minim'] . '</td>';
            echo '<td align="center">' . $val['saldo_awal'] . '</td>';
            echo '<td align="center">' . $val['stok_masuk'] . '</td>';
            foreach ($tgl as $tanggal) {
                if (isset($val[$tanggal])) {
                    echo '<td align="center">' . $val[$tanggal]['jml'] . '</td>';
                } else {
                    echo '<td align="center">0</td>';
                }
            }
            $no++;
            echo '<td>' . $val['saldo_akhir'] . '</td>';
            echo '<td></td>';
        }
        ?>
    </tbody>
</table>
