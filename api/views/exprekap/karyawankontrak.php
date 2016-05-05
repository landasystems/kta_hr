<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-masa-kontrak.xls");
}

//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>
<style media="print">
    .table {
        width:28cm !important;
        border-collapse: collapse;
    }
    .table table {
        width:28cm !important;
        border-collapse: collapse;
    }
</style>
<div style="font-size: 10px;" class="table">
    <table>
        <tr>
            <td rowspan="3" style="width:10% !important;"><img ng-src="img/logo.png" align="left" style="margin-right: 8px"/></td>
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
    <?php
    $periode = '';

    if ($tipe == 'rentang') {
        $periode = date('d F Y', strtotime($start)) . ' s/d ' . date('d F Y', strtotime($end));
    } else {
        $periode = date('d F Y',  strtotime($tanggal));
    }
    ?>

    <br/>
    <br/>
    <hr/>
    <div style="text-align: right">Dicetak: <?= date('d F Y'); ?></div>
    <table border="1">
        <thead>
            <tr>
                <td colspan="6" rowspan="2" style="text-align: center;vertical-align: central;">
                    <h4><u>DATA KONTRAK KERJA</u></h4>
                </td>
                <td  rowspan="2" colspan="2" style="text-align: left">
                    <span>PERIODE: <?= $periode ?></span>
                    <br/><span>SEKSI : <?= $section; ?></span>
                </td>
                <td  style="text-align: center">Dibuat</td>
                <td  style="text-align: center">Diketahui</td>
            </tr>
            <tr>
                <td  style="height: 45px;"></td>
                <td ></td>
            </tr>
        </thead>
        <thead>
            <tr>
                <th  style="text-align: center;vertical-align: center;">No</th>
                <th  style="text-align: center;vertical-align: center;">NIK</th>
                <th  style="text-align: center;vertical-align: center;">NAMA</th>
                <th  style="text-align: center;vertical-align: center;">JABATAN</th>
                <th  style="text-align: center;vertical-align: center;width: 10%;">KONTRAK 1</th>
                <th  style="text-align: center;vertical-align: center;width: 10%;">KONTRAK 11</th>
                <th  style="text-align: center;vertical-align: center;width: 10%;">KONTRAK 2</th>
                <th  style="text-align: center;vertical-align: center;width: 10%;">KONTRAK 21</th>
                <th  style="text-align: center;vertical-align: center;">TGL PENILAIAN</th>
                <th  style="text-align: center;vertical-align: center;">STATUS PENILAIAN</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($models as $val) {
                $tglPenilaian = (!empty($val['tgl_penilaian'])) ? date('d-M-y', strtotime($val['tgl_penilaian'])) : '';
                $status_penilaian = (!empty($val['status_penilaian'])) ? $val['status_penilaian'] : '';
                $kontrak1 = (empty($val['Kontrak_1'])) ? '' : date('d-M-y', strtotime($val['Kontrak_1']));
                $kontrak11 = (empty($val['Kontrak_11'])) ? '' : date('d-M-y', strtotime($val['Kontrak_11']));
                $kontrak2 = (empty($val['Kontrak_2'])) ? '' : date('d-M-y', strtotime($val['Kontrak_2']));
                $kontrak21 = (empty($val['Kontrak_21'])) ? '' : date('d-M-y', strtotime($val['Kontrak_21']));

                echo '<tr>';
                echo '<td align="center">' . $no . '</td>';
                echo '<td align="center">' . $val['nik'] . '</td>';
                echo '<td align="left">' . $val['nama'] . '</td>';
                echo '<td align="left">' . $val['jabatan'] . '</td>';
                echo '<td align="center">' . $kontrak1 . '</td>';
                echo '<td align="center">' . $kontrak11 . '</td>';
                echo '<td align="center">' . $kontrak2 . '</td>';
                echo '<td align="center">' . $kontrak21 . '</td>';
                echo '<td align="center">' . $tglPenilaian . '</td>';
                echo '<td align="center">' . $status_penilaian . '</td>';
                echo '</tr>';
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>
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