<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-monitoring-stnk.xls");
}
?>
<style>
    *{
        font-size: 14px;
    }
    .back-grey{
        background-color:rgb(226, 222, 222) !important;;
        background-image: url("../../../img/print.png");
        background-repeat: repeat;
        -webkit-print-color-adjust:exact;
    }
</style>

<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th rowspan="2" colspan="9"> <h3>DATA KENDARAAN<BR>PT. KARYA TUGAS ANDA</h3><BR>FR-HRD-032 Rev. 01<br><br></th>
    <th rowspan="2" colspan="7" style="vertical-align:top;text-align: left;"><br>Periode : Tahun <?=$tahun?></th>
    <th colspan="3">Prepared</th>
    <th colspan="3">Approved</th>
</tr>
<tr>
    <th style="height:90px" colspan="3"></th>
    <th colspan="3"></th>
</tr>
<tr>
    <th rowspan='2' class="back-grey" style="text-align: center;vertical-align: middle;">NO</th>
    <th rowspan='2' class="back-grey" style="text-align: center;vertical-align: middle;">NO<br>POLISI</th>
    <th rowspan='2' class="back-grey" style="text-align: center;vertical-align: middle;">MERK</th>
    <th rowspan='2' class="back-grey" style="text-align: center;vertical-align: middle;">TYPE</th>
    <th rowspan='2' class="back-grey" style="text-align: center;vertical-align: middle;">NO RANGKA</th>
    <th rowspan='2' class="back-grey" style="text-align: center;vertical-align: middle;">NO MESIN</th>
    <th rowspan='2' class="back-grey" style="text-align: center;vertical-align: middle;">TAHUN</th>
    <th rowspan='2' class="back-grey" style="text-align: center;vertical-align: middle;">USER</th>
    <th rowspan='2' class="back-grey" style="text-align: center;vertical-align: middle;">ATAS NAMA</th>
    <th colspan="12" class="back-grey" style="text-align: center;">BULAN</th>
    <th rowspan='2' class="back-grey" style="vertical-align: middle;">KET</th>
</tr>
<tr>
    <th class="back-grey">JAN</th>
    <th class="back-grey">FEB</th>
    <th class="back-grey">MAR</th>
    <th class="back-grey">APR</th>
    <th class="back-grey">MEI</th>
    <th class="back-grey">JUN</th>
    <th class="back-grey">JUL</th>
    <th class="back-grey">AGS</th>
    <th class="back-grey">SEP</th>
    <th class="back-grey">OKT</th>
    <th class="back-grey">NOV</th>
    <th class="back-grey">DES</th>
</tr>
</thead>
<tbody>
    <?php
    $no = 1;
    foreach ($models as $val) {
        echo '<tr>';
        echo '<td align="center">' . $no . '</td>';
        echo '<td align="left">' . $val['nopol'] . '</td>';
        echo '<td align="left">' . $val['merk'] . '</td>';
        echo '<td align="left">' . $val['tipe'] . '</td>';
        echo '<td align="left">' . $val['no_rangka'] . '</td>';
        echo '<td align="left">' . $val['no_mesin'] . '</td>';
        echo '<td align="center">' . $val['thn_pembuatan'] . '</td>';
        echo '<td align="left">' . $val['user'] . '</td>';
        echo '<td align="left"></td>';
        echo '<td align="center" class="' . $val['Jan']['style'] . '">' . $val['Jan']['day'] . '</td>';
        echo '<td align="center" class="' . $val['Feb']['style'] . '">' . $val['Feb']['day'] . '</td>';
        echo '<td align="center" class="' . $val['Mar']['style'] . '">' . $val['Mar']['day'] . '</td>';
        echo '<td align="center" class="' . $val['Apr']['style'] . '">' . $val['Apr']['day'] . '</td>';
        echo '<td align="center" class="' . $val['May']['style'] . '">' . $val['May']['day'] . '</td>';
        echo '<td align="center" class="' . $val['Jun']['style'] . '">' . $val['Jun']['day'] . '</td>';
        echo '<td align="center" class="' . $val['Jul']['style'] . '">' . $val['Jul']['day'] . '</td>';
        echo '<td align="center" class="' . $val['Aug']['style'] . '">' . $val['Aug']['day'] . '</td>';
        echo '<td align="center" class="' . $val['Sep']['style'] . '">' . $val['Sep']['day'] . '</td>';
        echo '<td align="center" class="' . $val['Oct']['style'] . '">' . $val['Oct']['day'] . '</td>';
        echo '<td align="center" class="' . $val['Nov']['style'] . '">' . $val['Nov']['day'] . '</td>';
        echo '<td align="center" class="' . $val['Dec']['style'] . '">' . $val['Dec']['day'] . '</td>';
        echo '<td align="center"></td>';
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