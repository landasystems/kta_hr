<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-stock-atk.xls");
}

$start = $params['tanggal']['startDate'];
$end = $params['tanggal']['endDate'];
?>


<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
          <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
            <td colspan="2" rowspan="2" style="width:60%;text-align: center">
                <h4>LAPORAN STOCK ATK</h4><br/>
                FR-HRD-038 REV.00
            </td>
            <td rowspan="2" colspan="2">
                <span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span><br/>
                Dicetak: <?= date('d F Y'); ?>
            </td>
            <td colspan="2"style="text-align: center">Dibuat</td>
            <td colspan="2" style="text-align: center">Diperiksa</td>
        </tr>
        <tr>
            <td colspan="2"style="height: 50px;"></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center;">NO</th>
            <th style="text-align: center;vertical-align: center;">KODE BARANG</th>
            <th style="text-align: center;vertical-align: center;">NAMA BARANG</th>
            <th style="text-align: center;vertical-align: center;">HARGA</th>
            <th style="text-align: center;vertical-align: center;">SATUAN</th>
            <th style="text-align: center;vertical-align: center;">SALDO AWAL</th>
            <th style="text-align: center;vertical-align: center;">MASUK</th>
            <th style="text-align: center;vertical-align: center;">KELUAR</th>
            <th style="text-align: center;vertical-align: center;">SALDO AKHIR</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td style="text-align: center">' . $no . '</td>
                            <td align="center">' . $val['kode_brng'] . '</td>
                            <td align="center">' . $val['nama_brng'] . '</td>
                            <td align="center"></td>
                            <td align="center"></td>
                            <td align="center">' . $val['saldo_awal'] . '</td>
                            <td align="center">' . $val['masuk'] . '</td>
                            <td align="center">' . $val['keluar'] . '</td>
                            <td align="center">' . $val['saldo_akhir'] . '</td>';
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