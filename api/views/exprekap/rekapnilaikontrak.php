<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-retur-Pergerakan-Barang.xls");
}

?>
<div style="font-size: 10px;">
    <table>
        <tr>
            <td rowspan="3" style="width:10% !important;"><img src="../../../img/logo.png" align="left" style="margin-right: 8px"/></td>
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
<div style="text-align: right">Dicetak: <?= date('d/F/Y'); ?></div>
<table width="100%" border="1" style="border-collapse: collapse">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Kontrak</th>
            <th>Mutu Kerja</th>
            <th>Pengetahuan Teknis</th>
            <th>Tg Jawab Pekerjaan</th>
            <th>Kerjasama / Komunikasi</th>
            <th>Sikap Kerja</th>
            <th>Inisiatif</th>
            <th>Rs Turut Memiliki</th>
            <th>Disiplinitas</th>
            <th>Kepemimpinan</th>
            <th>Pelaksanaan Managerial</th>
            <th>Problem Solving</th>
            <th>Kehadiran</th>
            <th>Administratif</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($models as $val) {
            echo '<tr>
                            <td>' . $no . '</td>
                            <td>' . $val['tgl'] . '</td>
                            <td>' . $val['nm_kontrak'] . '</td>
                            <td style="text-align: center">' . $penilaian($val['mutu_kerja']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['pengetahuan_teknis']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['tgjawab_pekerjaan']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['kerjasama_komunikasi']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['sikap_kerja']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['inisiatif']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['rasa_turut_memiliki']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['disiplinitas']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['kepemimpinan']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['pelaksanaan_managerial']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['problem_solving']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['kehadiran']) . '</td>
                            <td style="text-align: center">' . $penilaian($val['administratif']) . '</td>
                            <td>' . $val['keterangan'] . '</td>
                        </tr>';
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