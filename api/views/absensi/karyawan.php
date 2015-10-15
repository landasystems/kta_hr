<link rel="stylesheet" href="../../../css/print.css" type="text/css" />
<div style="width:26cm">
   <?php
    if (isset($_GET['print'])) {
        ?>
        <table>
            <tr>
                <td width="80"><img src="../../../img/logo.png"></td>
                <td valign="top">
                    <b style="font-size: 18px; margin:0px; padding:0px;">PT KARYA TUGAS ANDA</b>
                    <p style="font-size: 13px; margin:0px; padding:0px;">Autobody Manufacturing- Transport Service</p>
                    <p style="font-size: 13px; margin:0px; padding:0px;">Mining Contractor - Trading Channel</p>
                </td>
                <td style='width:200px'>
                    <h2> LAPORAN ABSENSI KARYAWAN</h2>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <hr>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    Dicetak : <?=date('d/m/Y');?>
                </td>
            </tr>
        </table>
        <!--<hr>-->
        <?php
    }
    ?>
        <b>PERIODE : <?=$start?> S/D <?=$end?></b>
    <table style="border-collapse: collapse; border: 1px #000 solid; font-size: 12px; margin-top:-2px;">
    <thead>
        <tr>
            <th class='border-all back-grey' style="text-align: center;vertical-align: center;background-color:rgb(226, 222, 222);">No</th>
            <th class='border-all back-grey' style="text-align: center;vertical-align: center;width: 100px;background-color:rgb(226, 222, 222);">NIK</th>
            <th class='border-all back-grey' style="text-align: center;vertical-align: center;background-color:rgb(226, 222, 222);">Nama</th>
            <th class='border-all back-grey' style="text-align: center;vertical-align: center;background-color:rgb(226, 222, 222);">A</th>
            <th class='border-all back-grey' style="text-align: center;vertical-align: center;background-color:rgb(226, 222, 222);">I</th>
            <th class='border-all back-grey' style="text-align: center;vertical-align: center;background-color:rgb(226, 222, 222);">SD</th>
            <th class='border-all back-grey' style="text-align: center;vertical-align: center;background-color:rgb(226, 222, 222);">C</th>
            <th class='border-all back-grey' style="text-align: center;vertical-align: center;background-color:rgb(226, 222, 222);">S</th>
            <th class='border-all back-grey' style="text-align: center;vertical-align: center;background-color:rgb(226, 222, 222);">T.HADIR</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 0;
        foreach ($models as $r) {
            ?>
            <tr>
                <td class='border-left border-bottom' style="text-align: center">&nbsp;<?=$no ?></td>
                <td class='border-left border-bottom' style="text-align: center">&nbsp;<?=$r['nik'] ?></td>
                <td class='border-left border-bottom' style="text-align: left"><?=$r['nama'] ?></td>
                <td class='border-left border-bottom' style="text-align: center">&nbsp;<?=$r['Absen'] ?></td>
                <td class='border-left border-bottom' style="text-align: center">&nbsp;<?=$r['Izin'] ?></td>
                <td class='border-left border-bottom' style="text-align: center">&nbsp;<?=$r['Surat_Dokter'] ?></td>
                <td class='border-left border-bottom' style="text-align: center">&nbsp;<?=$r['Cuti'] ?></td>
                <td class='border-left border-bottom' style="text-align: center">&nbsp;<?=$r['Sakit'] ?></td>
                <td class='border-left border-bottom' style="text-align: center">&nbsp;<?=$r['Hadir'] ?></td>
            </tr>
            <?php
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
                setTimeout(function() {
                    window.close();
                }, 1);
    </script>
    <?php
}
?>
