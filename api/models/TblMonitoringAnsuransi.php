<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_monitoring_ansuransi".
 *
 * @property string $no_mansuransi
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
 * @property string $masa_berlaku_mulai
 * @property string $masa_berlaku_sampai
 * @property string $nm_ansuransi
 * @property string $no_polis
 * @property double $harga_pertanggungan
 * @property double $harga_premi
 */
class TblMonitoringAnsuransi extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_monitoring_ansuransi';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_mansuransi'], 'required'],
            [['tgl', 'masa_berlaku_mulai', 'masa_berlaku_sampai'], 'safe'],
            [['harga_pertanggungan', 'harga_premi'], 'number'],
            [['no_mansuransi', 'nopol', 'merk', 'tipe', 'model', 'warna', 'thn_pembuatan'], 'string', 'max' => 20],
            [['no_rangka', 'no_mesin', 'user'], 'string', 'max' => 50],
            [['nm_ansuransi'], 'string', 'max' => 200],
            [['no_polis'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_mansuransi' => 'No Mansuransi',
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
            'masa_berlaku_mulai' => 'Masa Berlaku Mulai',
            'masa_berlaku_sampai' => 'Masa Berlaku Sampai',
            'nm_ansuransi' => 'Nm Ansuransi',
            'no_polis' => 'No Polis',
            'harga_pertanggungan' => 'Harga Pertanggungan',
            'harga_premi' => 'Harga Premi',
        ];
    }
}
