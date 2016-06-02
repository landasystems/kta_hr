<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-siswa-prakerin.xls");
}

//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>
<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
        <td rowspan="2" style="text-align: center;">
            <h4>DATA SISWA PRAKERIN</h4><br/>
            <span>
                <?php
                if ($start == '') {
                    
                } else {
                    ?>
                    PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?>
                    <?php
                }
                ?>

            </span>
        </td>
        <td rowspan="2" style="text-align: center"> Tgl Pelaporan :  <?= date('d F Y'); ?> </td>
        <td style="text-align: center;">Diketahui</td>
        <td style="text-align: center;">Diperiksa</td>
        <td style="text-align: center;">Dibuat</td>
    </tr>
    <tr>
        <td style="width: 100px;height: 80px;"></td>
        <td style="width: 100px;"></td>
        <td style="width: 100px;" ></td>
    </tr>
</table>
<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th style="text-align: center;vertical-align: center;">NO</th>
            <th style="text-align: center;vertical-align: center;">NAMA</th>
            <th style="text-align: center;vertical-align: center;">BAGIAN</th>
            <th style="text-align: center;vertical-align: center;">TANGGAL MASUK</th>
            <th style="text-align: center;vertical-align: center;">TANGGAL KELUAR</th>
            <th style="text-align: center;vertical-align: center;">ASAL SEKOLAH</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td>' . $val['nama'] . '</td>';
            echo '<td align="center">' . $val['bagian'] . '</td>';
            echo '<td align="center">' . date('d-M-y', strtotime($val['tgl_mulai'])) . '</td>';
            echo '<td align="center">' . date('d-M-y', strtotime($val['tgl_selesai']))  . '</td>';
            echo '<td>' . $val['asal_sekolah'] . '</td>';
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