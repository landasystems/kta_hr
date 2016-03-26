<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-karyawan-iso.xls");
}

//$start = $params['tanggal']['startDate'];
//$end = $params['tanggal']['endDate'];
?>
<style media="print">
    .table {
        width:28cm !important;
        border-collapse: collapse;
    }
    .table table {
        width:28cm !important;
        border-collapse: collapse;
    }
</style>
<div style="font-size: 10px;" class="table">
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


    <br>
    <br>
    <hr>
    <div style="text-align: right">Dicetak: <?= date('d F Y'); ?></div>
    <table border="1">
        <thead>
            <tr>
                <td colspan="8" rowspan="2" style="text-align: center;vertical-align: central;">
                    <h4><u>DATA KARYAWAN ISO</u></h4>
                </td>
                <td colspan="3" rowspan="2" style="text-align: left">
                    <span>PERIODE : <?= date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)); ?></span>
                    <br/><span>SEKSI : <?= $section;?></span>
                </td>
                <td colspan="3" style="text-align: center">Dibuat</td>
                <td colspan="3" style="text-align: center">Diketahui</td>
            </tr>
            <tr>
                <td colspan="3" style="height: 45px;"></td>
                <td colspan="3"></td>
            </tr>
        </thead>
        <thead>
            <tr>
                <th  style="text-align: center;vertical-align: center;">No</th>
                <th  style="text-align: center;vertical-align: center;">NIK</th>
                <th  style="text-align: center;vertical-align: center;">NAMA LENGKAP</th>
                <th  style="text-align: center;vertical-align: center;">PENDIDIKAN TERAKHIR</th>
                <th  style="text-align: center;vertical-align: center;">TEMPAT</th>
                <th  style="text-align: center;vertical-align: center;">TANGGAL LAHIR</th>
                <th  style="text-align: center;vertical-align: center;">USIA</th>
                <th  style="text-align: center;vertical-align: center;">ALAMAT</th>
                <th  style="text-align: center;vertical-align: center;">DESA</th>
                <th  style="text-align: center;vertical-align: center;">KECAMATAN</th>
                <th  style="text-align: center;vertical-align: center;">KOTA/ KABUPATEN</th>
                <th  style="text-align: center;vertical-align: center;">NO KTP</th>
                <th  style="text-align: center;vertical-align: center;">AGAMA</th>
                <th  style="text-align: center;vertical-align: center;">STATUS</th>
                <th  style="text-align: center;vertical-align: center; width: 170px;">TGL MASUK KERJA</th>
                <th  style="text-align: center;vertical-align: center;">LAMA BEKERJA (TH)</th>
                <th  style="text-align: center;vertical-align: center;">LAMA BEKERJA (BLN)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($models as $val) {
                echo '<tr>';
                echo '<td align="center">' . $no . '</td>';
                echo '<td align="center">' . $val['nik'] . '</td>';
                echo '<td align="center">' . $val['nama'] . '</td>';
                echo '<td align="center">' . $val['pendidikan'] . '</td>';
                echo '<td align="center">' . $val['tmpt_lahir'] . '</td>';
                echo '<td align="center">' . date('m/d/Y', strtotime($val['tgl_lahir'])) . '</td>';
                echo '<td align="center">' . $val['usia'] . '</td>';
                echo '<td align="center">' . $val['alamat_jln'] . '</td>';
                echo '<td align="center">' . $val['desa'] . '</td>';
                echo '<td align="center">' . $val['kecamatan'] . '</td>';
                echo '<td align="center">' . $val['kabupaten'] . '</td>';
                echo '<td align="center">&nbsp;' . $val['no_ktp'] . '</td>';
                echo '<td align="center">' . $val['agama'] . '</td>';
                echo '<td align="center">' . $val['status_pernikahan'] . '</td>';
                echo '<td align="center">' . date('m/d/Y', strtotime($val['tgl_masuk_kerja'])) . '</td>';
                echo '<td align="center">' . $val['tahun'] . '</td>';
                echo '<td align="center">' . $val['bulan'] . '</td>';
                echo '</tr>';
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>
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