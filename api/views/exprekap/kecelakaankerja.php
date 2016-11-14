<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekap-kecelakaan-kerja.xls");
}

//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>

<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
            <td rowspan="2" colspan="3" style="text-align: center;">
                <h3>LAPORAN KECELAKAAN KERJA</h3><br>
                 Tgl Pelaporan :  <?= date('d F Y'); ?> 
            </td>
            <!--<td rowspan="2" colspan="2" style="text-align: center"></td>-->
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
            <th style="text-align: center;vertical-align: center;">TANGGAL KEJADIAN</th>
            <th style="text-align: center;vertical-align: center;">NIK</th>
            <th style="text-align: center;vertical-align: center;">NAMA</th>
            <th style="text-align: center;vertical-align: center;">DEPARTMENT</th>
            <th style="text-align: center;vertical-align: center;">KETERANGAN</th>
            <th style="text-align: center;vertical-align: center;">BIAYA</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . date("d-m-Y",  strtotime($val['tgl_kejadian'])) . '</td>';
            echo '<td align="center">' . $val['nik'] . '</td>';
            echo '<td align="center">' . $val['nama'] . '</td>';
            echo '<td align="center">' . $val['bagian'] . '</td>';
            echo '<td align="center">' . $val['keterangan'] . '</td>';
            echo '<td align="center">' . Yii::$app->landa->rp($val['biaya']) . '</td>';
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