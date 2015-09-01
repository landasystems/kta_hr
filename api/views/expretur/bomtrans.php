<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan-HPP.xls");
?>
<table border="1" width="100%">
    <thead>
        <tr valign="top">
            <td align="center"><h2>BILL OF <br>MATERIAL</h2></td>
            <td colspan="8">
                <table width="600">
                    <tr>
                        <td>Produk</td>
                        <td width="500">: <?php echo $model['merk'] ?></td>
                    </tr>
                    <tr>
                        <td>Model / Type</td>
                        <td width="500">: <?php echo $model['model'] . '/' . $model['tipe'] ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center"><b>NAMA MATERIAL</b></td>
            <td align="center"><b>SATUAN</b></td>
            <td align="center"><b>QTY</b></td>
            <td align="center" colspan="2" width="200"><b>HARGA SATUAN</b></td>
            <td align="center" colspan="2" width="200"><b>HARGA</b></td>
            <td align="center" colspan="2" width="250"><b>KETERANGAN</b></td>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($detail as $bag) {
            echo '<tr height="25">
                        <td colspan="9" style="background-color: rgb(226, 222, 222)">' . $bag['nama_jabatan'] . '</td>
                    </tr>';
            foreach ($bag['body'] as $det) {
                echo '<tr height="25">
                        <td>' . $det['nama_barang'] . '</td>
                        <td align="center">' . $det['satuan'] . '</td>
                        <td align="right">' . $det['jumlah'] . '</td>
                        <td colspan="2"></td>
                        <td colspan="2"></td>
                        <td colspan="2">' . $det['ket'] . '</td>
                    </tr>';
            }
        }
        ?>
    </tbody>
    <tfoo>
        <tr>
            <td>Note</td>
            <td colspan="3">PREPARED</td>
            <td colspan="3">CHECKED</td>
            <td colspan="2">APPROVED</td>
        </tr>
        <tr>
            <td height="100"></td>
            <td height="100" colspan="3"></td>
            <td height="100" colspan="3"></td>
            <td height="100" colspan="2"></td>
        </tr>
    </tfoo>
</table>