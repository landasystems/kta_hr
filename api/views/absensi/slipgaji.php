<table >
    <thead>
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 30px">NO</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 100px">NIK</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Nama</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Gaji Pokok</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Potongan</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Penerimaan<br/>Kotor</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Potongan</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Penerimaan<br/> Bersih</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;vertical-align: middle;">Absensi</th>
            <th style="text-align: center;vertical-align: middle;">BPJS 2,5%</th>
            <th style="text-align: center;vertical-align: middle;">Pinjaman</th>
            <th style="text-align: center;vertical-align: middle;">Sepatu</th>
            <th style="text-align: center;vertical-align: middle;">Oksigen</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($models as $r) {
            echo '<tr>
                    <td style="text-align: center">'.$r['no'].'</td>
                    <td style="text-align: center">'.$r['nik'].'</td>
                    <td style="text-align: left">'.$r['nama'].'</td>
                    <td style="text-align: right">'.$r['gaji_pokok'].'</td>
                    <td style="text-align: center">'.$r['ijin'].'</td>
                    <td style="text-align: right">'.$r['ijin_rp'].'</td>
                    <td style="text-align: right">'.$r['bpjs'].'</td>
                    <td style="text-align: right">'.$r['kotor'].'</td>
                    <td style="text-align: right">'.$r['potongan_pinjaman_rp'].'</td>
                    <td style="text-align: right">'.$r['potongan_sepatu_rp'].'</td>
                    <td style="text-align: right">'.$r['potongan_oksigen_rp'].'</td>
                    <td style="text-align: right">'.$r['bersih'].'</td>
                </tr>';
        }
        ?>

    </tbody>
</table>