<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-jadwal-hse.xls");
}

//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>
<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
            <td rowspan="2" style="text-align: center;">
                <h3>LAPORAN HSE TALK</h3><br>Tgl Pelaporan :  <?= date('d F Y'); ?> </td>
            <td style="text-align: center;">Diketahui</td>
            <td style="text-align: center;">Dibuat</td>
        </tr>
        <tr>
            <td style="width: 100px;height: 80px;"></td>
            <td style="width: 100px;" ></td>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center;">NO</th>
            <th style="text-align: center;vertical-align: center;">TANGGAL HSE</th>
            <th style="text-align: center;vertical-align: center;">PEMBAWA MATERI</th>
            <th style="text-align: center;vertical-align: center;">JUDUL MATERI</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . date("d-m-Y", strtotime($val['tgl_hse'])) . '</td>';
            echo '<td align="center">' . $val['nama'] . '</td>';
            echo '<td align="center">' . $val['materi'] . '</td>';
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