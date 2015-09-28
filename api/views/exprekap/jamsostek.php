<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-retur-Pergerakan-Barang.xls");
}
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
<div style="text-align: right">Dicetak: <?= date('d F Y'); ?></div>
<br>
<center>LAPORAN DAFTAR JAMSOSTEK</center>
<br>
<span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span>

<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th>NO</th>
            <th>NAMA</th>
            <th>SUB SECTION</th>
            <th>JABATAN</th>
            <th>NN</th>
            <th>KPJ</th>
            <th>NO JAMSOSTEK</th>
            <th>PERIODE KEPESERTAAN</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            $noJamsostek = (empty($val['no_jamsostek']))? '' : $val['no_jamsostek']; 
            $jabatan = (empty($val['jabatan']))? '' : $val['jabatan']; 
            $subSection = (empty($val['sub_section']))? '' : $val['sub_section']; 
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . $val['nama'] . '</td>';
            echo '<td align="center">' . $subSection . '</td>';
            echo '<td align="center">' . $jabatan. '</td>';
            echo '<td align="center">' . $val['nn'] . '</td>';
            echo '<td align="center">' . $val['kpj'] . '</td>';
            echo '<td align="center">' . $noJamsostek . '</td>';
            echo '<td align="center">' . $val['p_kepesertaan'] . '</td>';
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