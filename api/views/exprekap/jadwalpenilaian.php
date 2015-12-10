<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-jadwal-pelatihan.xls");
}

//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>
<div style="font-size: 10px;">
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
</div>

<br>
<br>
<hr>
<table border="1" style="border-collapse: collapse;width:100%">
    <tr>
        <td colspan="3" rowspan="2" style="width:60%;text-align: center">
            <h4>LAPORAN JADWAL PENILAIAN</h4><br/>

            <span>PERIODE : Tahun <?= $params['tanggal']; ?> (Semester <?= $semester ?>)</span>
        </td>
        <td style="text-align: center;width:20%">Dibuat</td>
        <td style="text-align: center;width:20%">Diperiksa</td>
    </tr>
    <tr>
        <td style="height: 45px;"></td>
        <td></td>
    </tr>
</table>
<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th style="text-align: center;vertical-align: center;" rowspan="2">NO</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">NAMA</th>
            <th style="text-align: center;vertical-align: center;" rowspan="2">BAGIAN</th>
            <th style="text-align: center;vertical-align: center;" colspan="6">BULAN</th>
        </tr>
        <tr>
            <?php
            if ($semester == 'I') {
                ?>
                <th>Januari</th>
                <th>Februari</th>
                <th>Maret</th>
                <th>April</th>
                <th>Mei</th>
                <th>Juni</th>
                <?php
            } else {
                ?>
                <th>Juli</th>
                <th>Agustus</th>
                <th>September</th>
                <th>Oktober</th>
                <th>November</th>
                <th>Desember</th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            $satu = ($val['month'] == '01' || $val['month'] == '07') ? $val['day'] : '';
            $dua = ($val['month'] == '02' || $val['month'] == '08') ? $val['day'] : '';
            $tiga = ($val['month'] == '03' || $val['month'] == '09') ? $val['day'] : '';
            $empat = ($val['month'] == '04' || $val['month'] == '10') ? $val['day'] : '';
            $lima = ($val['month'] == '05' || $val['month'] == '11') ? $val['day'] : '';
            $enam = ($val['month'] == '06' || $val['month'] == '12') ? $val['day'] : '';

            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . $val['nama'] . '</td>';
            echo '<td align="center">' . $val['bagian'] . '</td>';
            echo '<td align="center">' . $satu . '</td>';
            echo '<td align="center">' . $dua . '</td>';
            echo '<td align="center">' . $tiga . '</td>';
            echo '<td align="center">' . $empat . '</td>';
            echo '<td align="center">' . $lima . '</td>';
            echo '<td align="center">' . $enam . '</td>';


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