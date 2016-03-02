<?php
if (!isset($_GET['print'])) {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=excel-rekap-lamaran-krja.xls");
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
<div style="width:26cm">
    <?php
    if (!isset($_GET['print'])) {
        ?>
        <table border="1">
            <tr>
                <!--<td style="border-top: 1px solid black;" colspan="13"></td>-->
                <th height="30" colspan="13" rowspan="2">
            <h3>  LAMARAN MASUK</h3>
            </th>
            <th  colspan="3">Dibuat</th>
            <th colspan="2">Diketahui</th>
            </tr>
            <tr>
    <!--                <th height="30" colspan="13">
                    LAMARAN MASUK
                </th>-->
                <th   colspan="3" rowspan="2" ></th>
                <th   colspan="2" rowspan="2" ></th>
            </tr>
            <tr>
                <th  height="25" colspan="13"></th>
            </tr>
        </table>
        <table border="1">
            <?php
        } else {
            ?>
            <table>
                <?php
            }
            ?>
            <thead>

                <tr>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">NO</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">NO LAMARAN</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">TANGGAL</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">UNTUK POSISI</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">NAMA</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">PEND. AKHIR</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">JURUSAN</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">IN FORMAL</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" colspan="2">PENGALAMAN</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">TEMPAT LAHIR</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="2">TANGGAL LAHIR</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;" colspan="6">ALAMAT</th>
                </tr>
                <tr>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;">PERUSAHAAN</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;">BAGIAN</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;">JALAN</th>
                    <th width="100" style="font-size: 12px;text-align: center;vertical-align: middle;">RT</th>
                    <th width="100" style="font-size: 12px;text-align: center;vertical-align: middle;">RW</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;">KEL</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;">KEC</th>
                    <th style="font-size: 12px;text-align: center;vertical-align: middle;">KAB/KOTA</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($models as $value) {
                    $rowsp1 = !empty($value['pk_perusahaan1']) ? 1 : 0;
                    $rowsp2 = !empty($value['pk_perusahaan2']) ? 1 : 0;
                    $rowsp3 = !empty($value['pk_perusahaan3']) ? 1 : 0;

                    $rowsp = ($rowsp1 + $rowsp2 + $rowsp3);

                    $pengalaman1 = !empty($value['pk_perusahaan1']) ? "1. " . $value['pk_perusahaan1'] : '';
                    $pengalaman2 = !empty($value['pk_perusahaan2']) ? "2. " . $value['pk_perusahaan2'] : '';
                    $pengalaman3 = !empty($value['pk_perusahaan3']) ? "3. " . $value['pk_perusahaan3'] : '';
                    $bagian1 = !empty($value['pk_bagian1']) ? "1. " . $value['pk_bagian1'] : '';
                    $bagian2 = !empty($value['pk_bagian2']) ? "2. " . $value['pk_bagian2'] : '';
                    $bagian3 = !empty($value['pk_bagian3']) ? "3. " . $value['pk_bagian3'] : '';
                    ?>
                    <tr>
                        <td style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= $no ?></td>
                        <td style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= $value['no_lamaran'] ?></td>
                        <td style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= date("d-m-Y", strtotime($value['tgl'])) ?></td>
                        <td style="font-size: 12px;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= strtoupper($value['posisi']) ?></td>
                        <td style="font-size: 12px;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= strtoupper($value['nama']) ?></td>
                        <td style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= strtoupper($value['pendidikan']) ?></td>
                        <td style="font-size: 12px;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= strtoupper($value['jurusan']) ?></td>
                        <td style="font-size: 12px;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= strtoupper($value['informal']) ?></td>
                        <td style="font-size: 12px;vertical-align: middle;"><?= strtoupper($pengalaman1) ?></td>
                        <td style="font-size: 12px;vertical-align: middle;"><?= strtoupper($bagian1) ?></td>
                        <td style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= strtoupper($value['tempat_lahir']) ?></td>
                        <td style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= date('d-M-Y', strtotime($value['tanggal_lahir'])) ?></td>
                        <td style="font-size: 12px;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= strtoupper($value['alamat_jln']) ?></td>
                        <td style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= $value['rt'] ?></td>
                        <td style="font-size: 12px;text-align: center;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= $value['rw'] ?></td>
                        <td style="font-size: 12px;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= strtoupper($value['kelurahan']) ?></td>
                        <td style="font-size: 12px;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= strtoupper($value['kecamatan']) ?></td>
                        <td style="font-size: 12px;vertical-align: middle;" rowspan="<?= $rowsp ?>"><?= strtoupper($value['kabupaten']) ?></td>
                    </tr>
                    <?php
                    if ($rowsp >= 2) {
                        ?>
                        <tr>
                            <td style="font-size: 12px;vertical-align: middle;"><?= $pengalaman2 ?></td>
                            <td style="font-size: 12px;vertical-align: middle;"><?= $bagian2 ?></td>
                        </tr>
                        <?php
                    }
                    if ($rowsp == 3) {
                        ?>
                        <tr>
                            <td style="font-size: 12px;vertical-align: middle;"><?= $pengalaman3 ?></td>
                            <td style="font-size: 12px;vertical-align: middle;"><?= $bagian3 ?></td>
                        </tr>
                        <?php
                    }
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