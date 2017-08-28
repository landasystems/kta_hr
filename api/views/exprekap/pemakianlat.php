<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-jadwal-pelatihan.xls");
}

//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>
<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
            <td rowspan="2" style="text-align: center;">
                <h3>LAPORAN PEMAKIAN LISTRIK, AIR DAN TELEPON</h3><br>
                 <span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span> 
            </td>
            <td style="text-align: center;">Diketahui</td>
            <td style="text-align: center;">Diperiksa</td>
            <td style="text-align: center;">Dibuat</td>
        </tr>
        <tr>
            <td style="height: 80px;"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center;">NO</th>
            <th style="text-align: center;vertical-align: center;">NO TRANSAKSI</th>
            <th style="text-align: center;vertical-align: center;">TGL. TRANSAKSI</th>
            <th style="text-align: center;vertical-align: center;">PEMAKIAN</th>
            <th style="text-align: center;vertical-align: center;">JUMLAH</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . $val['no_pemakian'] . '</td>';
            echo '<td align="center">' . date("d-m-Y",strtotime($val['tgl'])) . '</td>';
            echo '<td align="center">' . $val['biaya'] . '</td>';
            echo '<td align="center">' . $val['jmlh'] . '</td>';
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