<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-rekap-PO.xls");
?>
<br>
<table width="100%" border="1">
    <tr>
        <td colspan="4" rowspan="2" align="center" valign="top"><h2>REKAP SPK MASUK</h2></td>
        <td colspan="4" rowspan="2" valign="top">
            <table>
                <tr>
                    <td width="100" valign="top">Dept/Bagian</td>
                    <td width="1" valign="top">:</td>
                    <td valign="top">Sales Support</td>
                </tr>
                <tr>
                    <td width="100" valign="top">Periode</td>
                    <td width="1" valign="top">:</td>
                    <td valign="top">Sales Support</td>
                </tr>
            </table>
        </td>
        <td colspan="2" valign="top">Dibuat</td>
        <td colspan="2" valign="top">Diketahui</td>
    </tr>
    <tr height="70">
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td valign="top" align="center">NO</td>
        <td valign="top" align="center">SALES</td>
        <td valign="top" align="center">TGL CHASSIS</td>
        <td colspan="2" valign="top" align="center">CUSTOMER</td>
        <td valign="top" align="center">KODE TITIPAN</td>
        <td valign="top" align="center">NO WO</td>
        <td colspan="2" valign="top" align="center">MERK/TYPE</td>
        <td colspan="2" valign="top" align="center">MODEL</td>
        <td valign="top" align="center">NO SPK</td>
    </tr>
    <?php
    $no = 1;
    foreach ($models as $val) {
        echo '<tr>';
        echo '<td>' . $no . '</td>';
        echo '<td>' . $val['sales'] . '</td>';
        echo '<td>' . date("d M Y", strtotime($val['tgl_chassis'])) . '</td>';
        echo '<td colspan="2">' . $val['nm_customer'] . '</td>';
        echo '<td>' . $val['kd_titipan'] . '</td>';
        echo '<td>' . $val['no_wo'] . '</td>';
        echo '<td colspan="2">' . $val['merk'] . '/' . $val['tipe'] . '</td>';
        echo '<td colspan="2">' . $val['model'] . '</td>';
        echo '<td>' . $val['no_spk'] . '</td>';
        echo '</tr>';
        $no++;
    }
    ?>
</table>