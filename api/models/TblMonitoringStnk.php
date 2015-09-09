<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_monitoring_stnk".
 *
 * @property string $no_mstnk
 * @property string $tgl
 * @property string $nopol
 * @property string $merk
 * @property string $tipe
 * @property string $model
 * @property string $warna
 * @property string $thn_pembuatan
 * @property string $no_rangka
 * @property string $no_mesin
 * @property string $user
 * @property string $masa_berlaku
 * @property double $b_perpanjangan
 */
class TblMonitoringStnk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_monitoring_stnk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_mstnk'], 'required'],
            [['tgl', 'masa_berlaku'], 'safe'],
            [['b_perpanjangan'], 'number'],
            [['no_mstnk', 'nopol', 'merk', 'tipe', 'model', 'warna', 'thn_pembuatan'], 'string', 'max' => 20],
            [['no_rangka', 'no_mesin', 'user'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_mstnk' => 'No Mstnk',
            'tgl' => 'Tgl',
            'nopol' => 'Nopol',
            'merk' => 'Merk',
            'tipe' => 'Tipe',
            'model' => 'Model',
            'warna' => 'Warna',
            'thn_pembuatan' => 'Thn Pembuatan',
            'no_rangka' => 'No Rangka',
            'no_mesin' => 'No Mesin',
            'user' => 'User',
            'masa_berlaku' => 'Masa Berlaku',
            'b_perpanjangan' => 'B Perpanjangan',
        ];
    }
}
