<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekap-penggajian.xls");
}
?>
<table width="100%" border="1" style="border-collapse: collapse;">
    <thead>
        <tr>
            <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
            <td colspan="3" rowspan="2" style="text-align: center;">
                LAPORAN REKAP GAJI
            </td>
            <td rowspan="2" colspan="7" style="text-align: center"> Tgl Pelaporan :  <?= date('d F Y'); ?> </td>
            <td colspan="2" style="text-align: center;">Diketahui</td>
            <td colspan="3" style="text-align: center;">Diperiksa</td>
            <td  style="text-align: center;">Dibuat</td>
        </tr>
        <tr>
            <td colspan="2" style="width: 100px;height: 80px;"></td>
            <td colspan="3" style="width: 100px;"></td>
            <td  style="width: 100px;" ></td>
        </tr>
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 30px">NO</th>
            <th style="text-align: center;vertical-align: middle;">Nama</th>
            <th  style="text-align: center;vertical-align: middle;">THP</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;">KOMPENSASI</th>
            <th colspan="6" style="text-align: center;vertical-align: middle;">POTONGAN</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">PENERIMAAN NETTO</th>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;">KARYAWAN</th>
            <th style="text-align: center;vertical-align: middle;"><?= $tahun ?></th>
            <th style="text-align: center;vertical-align: middle;">GP</th>
            <th style="text-align: center;vertical-align: middle;">T.JABATAN</th>
            <th style="text-align: center;vertical-align: middle;">MGM</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">INCENTIVE KEHADIRAN</th>
            <th style="text-align: center;vertical-align: middle;">JUMLAH</th>
            <th style="text-align: center;vertical-align: middle;">KETENAGAKERJAAN 3%</th>
            <th style="text-align: center;vertical-align: middle;">KESEHATAN (1%)</th>
            <th style="text-align: center;vertical-align: middle;">PINJAMAN</th>
            <th colspan="2" style="text-align: center;vertical-align: middle;">ABSEN</th>
            <th style="text-align: center;vertical-align: middle;">JUMLAH</th>
        </tr>
    </thead>
    <tbody>

        <?php
        foreach ($models as $val) {
            ?>
            <tr>
                <td style="text-align: center">&nbsp;<?= $val['no'] ?></td>
                <td style="text-align: left"><?= $val['nama'] ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['thp']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['gaji_pokok']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['t_fungsional']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['mgm']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['incentive']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['jml_inc']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['ttl_incentive']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['jumlah_kopensasi']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['ketenagakerjaan']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['kesehatan']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['pinjaman']) ?></td>
                <td style="text-align: right"><?= $val['jml_absen'] ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['absen']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['jml_potongan']) ?></td>
                <td style="text-align: right"><?= Yii::$app->landa->rp($val['netto']) ?></td>
            </tr>
            <?php
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