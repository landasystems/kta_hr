<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekap-lamaran-krja.xls");
    $judul = '9';
    $col2 = '2';
} else {
    $judul = '5';
    $col2 = '1';
}
?>
<style type="text/css">
    * {
        font-size: 12px;
    }
    table {
        width: 100%;
        border-collapse: collapse;

    }
    .border-left {
        border-left: 1px #000 solid;
        padding: 2px;
    }

    .border-top {
        border-top: 1px #000 solid;
        padding: 2px;
    }

    .border-right {
        border-right: 1px #000 solid;
        padding: 2px;
    }

    .border-bottom {
        border-bottom: 1px #000 solid;
        padding: 2px;
    }


    .border-all {
        border-bottom: 1px #000 solid;
        border-left: 1px #000 solid;
        border-top: 1px #000 solid;
        border-right: 1px #000 solid;
        padding: 2px;
    }

    thead , tbody {
        border: 1px solid black;
    }

    thead > tr > th{
        border: 1px solid black;
    }

    tbody > tr > td {
        border: 1px solid black;
    }

    tbody > tr { 
        line-height: 20px; 
    }
    .border-none {
        border:none;
    }
</style>
<table width="100%" style="border-collapse: collapse;" border="1">
    <tr>
        <td rowspan="2" style="text-align: center;"><img class="img-responsive" src="../../../img/logo.png"></td>
        <td colspan="<?= $judul ?>" rowspan="2" style="text-align: center;">
            LAPORAN MASTER LAMARAN MASUK
        </td>
        <td colspan="<?= $col2 ?>" rowspan="2" style="text-align: center"> Tgl Pelaporan :  <?= date('d F Y'); ?> </td>
        <td colspan="<?= $col2 ?>" style="text-align: center;">Diketahui</td>
        <td colspan="<?= $col2 ?>" style="text-align: center;">Diperiksa</td>
        <td colspan="<?= $col2 ?>" style="text-align: center;">Dibuat</td>
    </tr>
    <tr>
        <td colspan="<?= $col2 ?>" style="width: 100px;height: 80px;"></td>
        <td colspan="<?= $col2 ?>" style="width: 100px;"></td>
        <td colspan="<?= $col2 ?>" style="width: 100px;" ></td>
    </tr>
    <tr>
        <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">NO</th>
        <th style="font-size: 12px;text-align: center;" rowspan="2">NO LAMARAN</th>
        <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">TANGGAL</th>
        <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">UNTUK POSISI</th>
        <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">NAMA</th>
        <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">PEND. AKHIR</th>
        <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">JURUSAN</th>
        <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">IN FORMAL</th>
        <th style="font-size: 12px;text-align: center;vertical-align: middle;" colspan="2">PENGALAMAN</th>
        <?php
        if (!isset($_GET['print'])) {
            ?>
            <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">TEMPAT LAHIR</th>
            <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">TANGGAL LAHIR</th>
            <th style="font-size: 12px;text-align: center;vertical-align: middle;" colspan="6">ALAMAT</th>
            <?php
        } else {
            
        }
        ?>
    </tr>
    <tr>
        <th style="font-size: 12px;text-align: center;vertical-align: middle;">PERUSAHAAN</th>
        <th style="font-size: 12px;text-align: center;vertical-align: middle;">BAGIAN</th>
        <?php
        if (!isset($_GET['print'])) {
            ?>
            <th style="font-size: 12px;text-align: center;vertical-align: middle;">JALAN</th>
            <th width="100" style="font-size: 12px;text-align: center;vertical-align: middle;">RT</th>
            <th width="100" style="font-size: 12px;text-align: center;vertical-align: middle;">RW</th>
            <th style="font-size: 12px;text-align: center;vertical-align: middle;">KEL</th>
            <th style="font-size: 12px;text-align: center;vertical-align: middle;">KEC</th>
            <th style="font-size: 12px;text-align: center;vertical-align: middle;">KAB/KOTA</th>
            <?php
        } else {
            
        }
        ?>
    </tr>


    <?php
    $no = 1;
    foreach ($models as $value) {
        ?>
        <tr>
            <td style="font-size: 12px;text-align: center;" ><?= $no ?></td>
            <td style="font-size: 12px;text-align: center;vertical-align: middle;" ><?= $value['no_lamaran'] ?></td>
            <td style="font-size: 12px;text-align: center;vertical-align: middle;" ><?= date("d-m-Y", strtotime($value['tgl'])) ?></td>
            <td style="font-size: 12px;vertical-align: middle;" ><?= strtoupper($value['posisi']) ?></td>
            <td style="font-size: 12px;vertical-align: middle;" ><?= strtoupper($value['nama']) ?></td>
            <td style="font-size: 12px;text-align: center;vertical-align: middle;" ><?= strtoupper($value['pendidikan']) ?></td>
            <td style="font-size: 12px;vertical-align: middle;" ><?= strtoupper($value['jurusan']) ?></td>
            <td style="font-size: 12px;vertical-align: middle;" ><?= strtoupper($value['informal']) ?></td>
            <td style="font-size: 12px;vertical-align: middle;">
                <?php
                $n= 0;
                foreach ($value['perusahaan'] as $per) {
                    $n++;
                    echo "$n. ".strtoupper($per)."<br/>";
                }
                ?>
            </td>
            <td style="font-size: 12px;vertical-align: middle;">
                <?php
                $o= 0;
                foreach ($value['bagian'] as $bag) {
                    $o++;
                    echo "$o. ".strtoupper($bag)."<br/>";
                }
                ?>
                
            </td>
            <?php
            if (!isset($_GET['print'])) {
                ?>
                <td style="font-size: 12px;text-align: center;vertical-align: middle;" ><?= strtoupper($value['tempat_lahir']) ?></td>
                <td style="font-size: 12px;text-align: center;vertical-align: middle;" ><?= date('d-M-Y', strtotime($value['tanggal_lahir'])) ?></td>
                <td style="font-size: 12px;vertical-align: middle;" ><?= strtoupper($value['alamat_jln']) ?></td>
                <td style="font-size: 12px;text-align: center;vertical-align: middle;" ><?= $value['rt'] ?></td>
                <td style="font-size: 12px;text-align: center;vertical-align: middle;" ><?= $value['rw'] ?></td>
                <td style="font-size: 12px;vertical-align: middle;" ><?= strtoupper($value['kelurahan']) ?></td>
                <td style="font-size: 12px;vertical-align: middle;" ><?= strtoupper($value['kecamatan']) ?></td>
                <td style="font-size: 12px;vertical-align: middle;" ><?= strtoupper($value['kabupaten']) ?></td>
                <?php
            } else {
                
            }
            ?>
        </tr>

       
    <?php
    $no++;
}
?>

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