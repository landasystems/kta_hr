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
                LAPORAN MASTER JABATAN
            </td>
            <td rowspan="2" colspan="2" style="text-align: center"> Tgl Pelaporan :  <?= date('d F Y'); ?> </td>
            <td style="text-align: center;">Diketahui</td>
            <td style="text-align: center;">Diperiksa</td>
            <td style="text-align: center;">Dibuat</td>
        </tr>
        <tr>
            <td style="width: 100px;height: 80px;"></td>
            <td style="width: 100px;"></td>
            <td style="width: 100px;" ></td>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: center;">NO</th>
            <th style="text-align: center;vertical-align: center;">TANGGAL</th>
            <th style="text-align: center;vertical-align: center;">JAM</th>
            <th style="text-align: center;vertical-align: center;">TEMPAT</th>
            <th style="text-align: center;vertical-align: center;">PESERTA</th>
            <th style="text-align: center;vertical-align: center;">KEGIATAN</th>
            <th style="text-align: center;vertical-align: center;">PESERTA AKTUAL</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>';
            echo '<td align="center">' . $no . '</td>';
            echo '<td align="center">' . date("d-m-Y", strtotime($val['tgl'])) . '</td>';
            echo '<td align="center">' . $val['jam'] . '</td>';
            echo '<td align="center">' . $val['tmpt'] . '</td>';
            echo '<td align="center"></td>';
            echo '<td align="center">' . $val['kegiatan'] . '</td>';
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