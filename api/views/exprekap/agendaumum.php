<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-agenda-umum.xls");
}
//function daftar_file($dir) { if(is_dir($dir)) { if($handle = opendir($dir)) { //tampilkan semua file dalam folder kecuali 
//    while(($file = readdir($handle)) !== false) { echo '<a target="_blank" href="'.$dir.$file.'">'.$file.'</a><br>'."\n"; } closedir($handle); } } }
//daftar_file("../../img")
//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>
<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <td rowspan="2" style="width:10% !important;"><center><img src="../../../img/logo.png" align="center" /></center></td>
            <td rowspan="2" colspan="2"style="text-align: center;padding: 10px;text-decoration: underline;width:30% !important;font-size: 20px !important;font-weight: bold">LAPORAN AGENDA UMUM</td>
            <td rowspan="2"   style="width:30%!important;padding: 10px;height: 10px;">Periode : <?= date('d M Y', strtotime($start)) . ' S/D ' . date('d M Y', strtotime($end)); ?><br>
                Tgl Pelaporan : <?=date("d F Y")?>
            </td>
            <td style="text-align: center;"> Diketahui</td>
            <td style="text-align: center;" > Diperiksa</td>
            <td style="text-align: center;"> Dibuat</td>
        </tr>
        <tr>
            <!--<td style="font-size: 10px;">Autobody, Manufacturing - Transport Services</td>-->
            <td style="height: 50px;"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center;">NO</th>
            <th style="text-align: center;vertical-align: center;">NO AGENDA</th>
            <th style="text-align: center;vertical-align: center;">TANGGAL</th>
            <th style="text-align: center;vertical-align: center;">AGENDA</th>
            <th colspan="3" style="text-align: center;vertical-align: center;">KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . $val['no_agenda'] . '</td>';
            echo '<td align="center">' . date('d-m-Y',strtotime($val['tgl'])) . '</td>';
            echo '<td align="center">' . $val['agenda'] . '</td>';
            echo '<td align="left" colspan="3">' . $val['keterangan'] . '</td>';
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