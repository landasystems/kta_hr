<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-retur-Pergerakan-Barang.xls");
}
?>
<div style="font-size: 10px;">
    <table>
        <tr>
            <td rowspan="3" style="width:10% !important;"><img src="../../../img/logo.png" align="left" style="margin-right: 8px"/></td>
            <td style="width:40% !important;font-size: 14px !important;">PT. KARYA TUGAS ANDA</td>
            <td></td>
        </tr>
        <tr>
            <td style="font-size: 10px;">Autobody, Manufacturing - Transport Services</td>
            <td></td>
        </tr>
        <tr>
            <td style="font-size: 10px;">Minning Contractor - Trading Channel</td>
            <td></td>
        </tr>
    </table>
</div>

<br>
<br>
<div style="text-align: right">Dicetak: <?= date('d F Y'); ?></div>

<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <td colspan="5" rowspan="2" style="width:60%;text-align: center">
                <h4>JADWAL AUDIT</h4><br/>
                FR.MR-007 REV: 01<br/>
                <span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span>
            </td>
            <td style="text-align: center">Disiapkan</td>
            <td style="text-align: center">Disetujui</td>
        </tr>
        <tr>
            <td style="height: 45px;"></td>
            <td></td>
        </tr>
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: center;">NO</th>
            <th rowspan="2" style="text-align: center;vertical-align: center;">TANGGAL</th>
            <th rowspan="2" style="text-align: center;vertical-align: center;">JAM</th>
            <th colspan="2" style="text-align: center;vertical-align: center;">AUDITEE</th>
            <th colspan="2" style="text-align: center;vertical-align: center;">AUDITOR</th>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center;">DEPARTMENT</th>
            <th style="text-align: center;vertical-align: center;">PIC</th>
            <th style="text-align: center;vertical-align: center;">DEPARTMENT</th>
            <th style="text-align: center;vertical-align: center;">PIC</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $tgl = '';
        $jam = '';
        foreach ($models as $key => $val) {
            $val1 = (!empty($models[$key - 1])) ? $models[$key - 1]['no_audit'] : '';
            $val2 = (!empty($models[$key])) ? $models[$key]['no_audit'] : '';

            if ($val1 == $val2) {
                $tgl = '';
                $jam = '';
            } else {
                $tgl = date('d/m/Y', strtotime($val['tgl']));
                $jam = $val['jam'];
            }
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . $tgl . '</td>';
            echo '<td align="center">' . $jam . '</td>';
            echo '<td align="center">' . $val['dept_auditee'] . '</td>';
            echo '<td align="center">' . $val['auditee'] . '</td>';
            echo '<td align="center">' . $val['dept_auditor'] . '</td>';
            echo '<td align="center">' . $val['auditor'] . '</td>';
            echo '</tr>';
            $no++;
        }
        ?>
    </tbody>
</table>
<?php
if (isset($_GET['print'])) {
    ?>
    <script type="text/javascript">
        window.print();
        setTimeout(function () {
            window.close();
        }, 1);
    </script>
    <?php
}
?>