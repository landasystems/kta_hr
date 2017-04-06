<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_kecelakaan_kerja".
 *
 * @property string $no
 * @property string $tgl_kejadian
 * @property string $nik
 * @property string $nama
 * @property string $sub_section
 * @property string $keterangan
 * @property double $biaya
 */
class TblKecelakaanKerja extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_kecelakaan_kerja';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no'], 'required'],
            [['tgl_kejadian'], 'safe'],
            [['biaya'], 'number'],
            [['no', 'nik'], 'string', 'max' => 20],
            [['nama'], 'string', 'max' => 30],
//            [['sub_section'], 'string', 'max' => 50],
            [['keterangan'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no' => 'No',
            'tgl_kejadian' => 'Tgl Kejadian',
            'nik' => 'Nik',
            'nama' => 'Nama',
            'sub_section' => 'Sub Section',
            'keterangan' => 'Keterangan',
            'biaya' => 'Biaya',
        ];
    }
}
