<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gaji_produksi".
 *
 * @property string $no_gaji
 * @property string $periode
 * @property string $nik
 * @property string $nama
 * @property integer $hadir
 * @property integer $dinas_luar
 * @property integer $setengah_hari
 * @property integer $absent
 * @property integer $izin
 * @property integer $surat_dokter
 * @property integer $cuti
 * @property integer $sakit
 * @property integer $jmlh_hadir
 * @property integer $jam_lembur
 * @property double $total_lembur
 * @property double $gaji_pokok
 * @property double $kompensasi
 * @property double $potongan
 * @property double $gaji_bersih
 *
 * @property TblHtransPotongan[] $tblHtransPotongans
 */
class TblGajiProduksi extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gaji_produksi';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_gaji'], 'required'],
            [['hadir', 'dinas_luar', 'setengah_hari', 'absent', 'izin', 'surat_dokter', 'cuti', 'sakit', 'jmlh_hadir', 'jam_lembur'], 'integer'],
            [['total_lembur', 'gaji_pokok', 'kompensasi', 'potongan', 'gaji_bersih'], 'number'],
            [['no_gaji'], 'string', 'max' => 50],
            [['periode'], 'string', 'max' => 100],
            [['nik'], 'string', 'max' => 20],
            [['nama'], 'string', 'max' => 70]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_gaji' => 'No Gaji',
            'periode' => 'Periode',
            'nik' => 'Nik',
            'nama' => 'Nama',
            'hadir' => 'Hadir',
            'dinas_luar' => 'Dinas Luar',
            'setengah_hari' => 'Setengah Hari',
            'absent' => 'Absent',
            'izin' => 'Izin',
            'surat_dokter' => 'Surat Dokter',
            'cuti' => 'Cuti',
            'sakit' => 'Sakit',
            'jmlh_hadir' => 'Jmlh Hadir',
            'jam_lembur' => 'Jam Lembur',
            'total_lembur' => 'Total Lembur',
            'gaji_pokok' => 'Gaji Pokok',
            'kompensasi' => 'Kompensasi',
            'potongan' => 'Potongan',
            'gaji_bersih' => 'Gaji Bersih',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblHtransPotongans()
    {
        return $this->hasMany(TblHtransPotongan::className(), ['no_gaji' => 'no_gaji']);
    }
}
