<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-master-barang-atk.xls");
}
?>
<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
        <td rowspan="2" style="text-align: center;">
            LAPORAN MASTER BARANG ATK
        </td>
        <td rowspan="2" colspan="2" style="text-align: center"> Tgl Pelaporan :  <?= date('d F Y'); ?> </td>
        <td style="text-align: center;">Diketahui</td>
        <td style="text-align: center;">Diperiksa</td>
        <td style="text-align: center;">Dibuat</td>
    </tr>
    <tr>
        <td style="width: 100px;height: 80px;"></td>
        <td style="width: 100px;"></td>
        <td  style="width: 100px;" ></td>
    </tr>
    <tr>
        <th>No</th>
        <th>Kode ATK</th>
        <th>Nama Alat Tulis Kantor(ATK)</th>
        <th>Merk</th>
        <th>Jumlah</th>
        <th colspan="2">Keterangan</th>
    </tr>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $arr) {
            echo '<tr>';
            echo '<td style="text-align: center;">' . $no++ . '</td>';
            echo '<td>' . $arr['kode_brng'] . '</td>';
            echo '<td style="text-align: center;">' . $arr['nama_brng'] . '</td>';
            echo '<td>' . $arr['merk'] . '</td>';
            echo '<td style="text-align: center;">' . $arr['jumlah_brng'] . '</td>';
            echo '<td colspan="2">' . $arr['keterangan'] . '</td>';
            echo '</tr>';
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