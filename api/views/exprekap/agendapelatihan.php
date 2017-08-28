<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-agenda-pelatihan.xls");
}
//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>

<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th rowspan="2" colspan="4" style="vertical-align: middle;text-align: center;">
    <h3>AGENDA PELATIHAN</h3>
    <span>No. Dokumen : FR-HRD-007 Rev. 01</span>
</th>
<th rowspan="2" colspan="2"style="text-align: left;"><span>PERIODE :</span><br><center>Tahun <?= date('Y', strtotime($start)) ?></center></th>
<th>Dibuat Oleh</th>
<th>Diperiksa Oleh</th>
</tr>
<tr>
    <th style="height: 50px;"></th>
    <th></th>
</tr>
<tr>
    <th rowspan="2"style="text-align: center;vertical-align: middle;">NO </th>
    <th rowspan="2"style="text-align: center;vertical-align: middle;">Jenis Pelatihan</th>
    <th rowspan="2"style="text-align: center;vertical-align: middle;">Sumber Pelatihan</th>
    <th rowspan="2"style="text-align: center;vertical-align: middle;">Waktu</th>
    <th rowspan="2"style="text-align: center;vertical-align: middle;">Peserta</th>
    <th colspan="2" style="text-align: center;">Uraian Pelatihan</th>
    <th rowspan="2" style="text-align: center;vertical-align: middle;">Keterangan</th>
</tr>
<tr>
    <th style="text-align: center;vertical-align: center;">Bahasan </th>
    <th style="text-align: center;vertical-align: center;">Alat Peraga</th>
</tr>
</thead>
<tbody>
    <?php
    $no = 1;
    foreach ($models as $val) {
        echo '<tr>';
        echo '<td align="center">' . $no . '</td>';
        echo '<td align="center">' . $val['jns_pelatihan'] . '</td>';
        echo '<td align="center">' . $val['sumber_pelatihan'] . '</td>';
        echo '<td align="center">' . date("d M Y", strtotime($val['waktu'])) . '</td>';
        echo '<td align="center">Terlampir</td>';
        echo '<td align="center">' . $val['bahasan'] . '</td>';
        echo '<td align="center">' . $val['alat_peraga'] . '</td>';
        echo '<td align="center">' . $val['keterangan'] . '</td>';
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