<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_karyawan".
 *
 * @property string $nik
 * @property string $nama
 * @property string $Kontrak_1
 * @property string $Kontrak_11
 * @property string $Kontrak_2
 * @property string $Kontrak_21
 * @property string $initial
 * @property string $foto
 * @property string $status_kepemilikan
 * @property string $status_karyawan
 * @property string $department
 * @property string $section
 * @property string $sub_section
 * @property string $jabatan
 * @property string $lokasi_kntr
 * @property string $tmt_kerja
 * @property string $pendidikan
 * @property string $sekolah
 * @property string $jurusan
 * @property string $no_ijazah
 * @property string $tmpt_lahir
 * @property string $tgl_lahir
 * @property string $bulan_lahir
 * @property string $alamat_jln
 * @property string $rt
 * @property string $rw
 * @property string $desa
 * @property string $kecamatan
 * @property string $kabupaten
 * @property string $kode_pos
 * @property string $no_ktp
 * @property string $agama
 * @property string $status_pernikahan
 * @property string $kewarganegaraan
 * @property string $tgl_masuk_kerja
 * @property string $kode_bank
 * @property string $nama_bank
 * @property double $gaji_pokok
 * @property double $t_fungsional
 * @property double $t_kehadiran
 * @property double $thp
 * @property double $upah_tetap
 * @property double $pesangon
 * @property double $t_masa_kerja
 * @property double $penggantian_hak
 * @property double $normatif
 * @property string $jk
 * @property string $tgl_keluar_kerja
 * @property string $alasan_keluar
 * @property string $status
 * @property string $ket
 * @property string $no_polis
 * @property string $nm_asuransi
 * @property string $no_npwp
 * @property string $no_pasport
 */
class TblKaryawan extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_karyawan';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['nik'], 'required'],
            [['Kontrak_1', 'Kontrak_11', 'Kontrak_2', 'Kontrak_21', 'tgl_lahir', 'tgl_masuk_kerja', 'tgl_keluar_kerja'], 'safe'],
            [['gaji_pokok', 'mgm', 't_fungsional', 't_kehadiran', 'thp', 'upah_tetap', 'pesangon', 't_masa_kerja', 'penggantian_hak', 'normatif'], 'number'],
            [['nik', 'initial', 'status_kepemilikan', 'status_karyawan', 'department', 'section', 'sub_section', 'lokasi_kntr', 'tmt_kerja', 'pendidikan', 'tmpt_lahir', 'bulan_lahir', 'rt', 'rw', 'desa', 'kecamatan', 'kabupaten', 'kode_pos', 'no_ktp', 'agama', 'status_pernikahan', 'kewarganegaraan', 'kode_bank', 'nama_bank', 'jk', 'status', 'ket', 'no_npwp'], 'string', 'max' => 20],
            [['nama', 'sekolah', 'jurusan', 'no_ijazah', 'alamat_jln', 'no_polis', 'no_pasport'], 'string', 'max' => 50],
            [['foto'], 'string', 'max' => 500],
            [['jabatan', 'alasan_keluar'], 'string', 'max' => 100],
            [['nm_asuransi'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'nik' => 'Nik',
            'nama' => 'Nama',
            'Kontrak_1' => 'Kontrak 1',
            'Kontrak_11' => 'Kontrak 11',
            'Kontrak_2' => 'Kontrak 2',
            'Kontrak_21' => 'Kontrak 21',
            'initial' => 'Initial',
            'foto' => 'Foto',
            'status_kepemilikan' => 'Status Kepemilikan',
            'status_karyawan' => 'Status Karyawan',
            'department' => 'Department',
            'mgm' => 'Management',
            'section' => 'Section',
            'sub_section' => 'Sub Section',
            'jabatan' => 'Jabatan',
            'lokasi_kntr' => 'Lokasi Kntr',
            'tmt_kerja' => 'Tmt Kerja',
            'pendidikan' => 'Pendidikan',
            'sekolah' => 'Sekolah',
            'jurusan' => 'Jurusan',
            'no_ijazah' => 'No Ijazah',
            'tmpt_lahir' => 'Tmpt Lahir',
            'tgl_lahir' => 'Tgl Lahir',
            'bulan_lahir' => 'Bulan Lahir',
            'alamat_jln' => 'Alamat Jln',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'desa' => 'Desa',
            'kecamatan' => 'Kecamatan',
            'kabupaten' => 'Kabupaten',
            'kode_pos' => 'Kode Pos',
            'no_ktp' => 'No Ktp',
            'agama' => 'Agama',
            'status_pernikahan' => 'Status Pernikahan',
            'kewarganegaraan' => 'Kewarganegaraan',
            'tgl_masuk_kerja' => 'Tgl Masuk Kerja',
            'kode_bank' => 'Kode Bank',
            'nama_bank' => 'Nama Bank',
            'gaji_pokok' => 'Gaji Pokok',
            't_fungsional' => 'T Fungsional',
            't_kehadiran' => 'T Kehadiran',
            'thp' => 'Thp',
            'upah_tetap' => 'Upah Tetap',
            'pesangon' => 'Pesangon',
            't_masa_kerja' => 'T Masa Kerja',
            'penggantian_hak' => 'Penggantian Hak',
            'normatif' => 'Normatif',
            'jk' => 'Jk',
            'tgl_keluar_kerja' => 'Tgl Keluar Kerja',
            'alasan_keluar' => 'Alasan Keluar',
            'status' => 'Status',
            'ket' => 'Ket',
            'no_polis' => 'No Polis',
            'nm_asuransi' => 'Nm Asuransi',
            'no_npwp' => 'No Npwp',
            'no_pasport' => 'No Pasport',
        ];
    }

    public function getSect() {
        return $this->hasOne(Section::className(), ['id_section' => 'section']);
    }

    public static function aktif($niknama = '', $section = '', $lokasi_kntr = 'SUKOREJO') {
        $query = TblKaryawan::find()->where('status="Kerja" AND lokasi_kntr="' . $lokasi_kntr . '"')->orderBy('nik')->indexBy('nik');
        if (!empty($niknama)) {
            $query->andWhere('(nik LIKE "%' . $niknama . '%" OR nama LIKE "%' . $niknama . '%")');
        }
        
        if (!empty($section)) {
            if (is_array($section)) {
//                Yii::error($section);
                    $query->andWhere(['in','section',$section]);
            } else {
                $query->andWhere("section = '" . $section . "'");
            }
        }
        return $query->all();
    }

}
