<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-retur-rubah-bentuk.xls");
?>
<div style="margin-top: 30px; margin-left: 30px;">
    <table width="100%" border="1">
        <tr>
            <td colspan="4" rowspan="5" align="center">
                <h3>LAPORAN PEMBUATAN RUBAH BENTUK</h3>
                No. Dokumen : FR-SS-013
            </td>
            <td colspan="2">
                <table width="100%">
                    <tr height="30">
                        <td width="100">PERIODE</td>
                        <td width="1" width="1">:</td>
                        <td><?php echo $periode ?></td>
                    </tr>
                </table>
            </td>
            <td>DIBUAT</td>
            <td>DIPERIKSA</td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tr height="30">
                        <td width="100">CETAK</td>
                        <td width="1" width="1">:</td>
                        <td><?php echo date("d-m-y")?></td>
                    </tr>
                </table>
            </td>
            <td rowspan="3"></td>
            <td rowspan="3"></td>
        </tr>
        <tr height="30">
            <td colspan="2"></td>
        </tr>
        <tr height="30">
            <td colspan="2"></td>
        </tr>
        <tr height="30">
            <td colspan="2"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td align="center">PEMBUATAN WO</td>
            <td align="center">WO</td>
            <td align="center">PEMBUATAN</td>
            <td align="center">NO. REGISTER</td>
            <td align="center">MERK TYPE</td>
            <td align="center">BENTUK BARU</td>
            <td align="center">CHASSIS</td>
            <td align="center">CUSTOMER</td>
        </tr>
        <?php
        $jml = 0;
        foreach ($models as $val) {
            $jml += $val['jml_unit'];
            echo '<tr>';
            echo '<td></td>';
            echo '<td align="center">' . $val['no_wo'] . '</td>';
            echo '<td align="right">' . date("d-M-Y", strtotime($val['tgl'])) . '&emsp;</td>';
            echo '<td align="left">' . $val['kd_rubah'] . '</td>';
            echo '<td align="left">' . $val['merk'] . ' ' . $val['tipe'] . '</td>';
            echo '<td align="left">' . $val['bentuk_baru'] . '</td>';
            echo '<td align="center">' . $val['no_chassis'] . '</td>';
            echo '<td align="left">' . $val['nm_customer'] . '</td>';
            echo '</tr>';
        }
        ?>
        <tr height="30">
            <td>Total Unit</td>
            <td colspan="7"><?php echo $jml ?></td>
        </tr>
    </table>
</div>