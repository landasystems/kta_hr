<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_kendaraan".
 *
 * @property string $nopol
 * @property string $merk
 * @property string $tipe
 * @property string $model
 * @property string $warna
 * @property string $thn_pembuatan
 * @property string $no_rangka
 * @property string $no_mesin
 * @property string $user
 */
class TblKendaraan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_kendaraan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nopol'], 'required'],
            [['nopol', 'merk', 'tipe', 'model', 'warna', 'thn_pembuatan'], 'string', 'max' => 20],
            [['no_rangka', 'no_mesin', 'user'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nopol' => 'Nopol',
            'merk' => 'Merk',
            'tipe' => 'Tipe',
            'model' => 'Model',
            'warna' => 'Warna',
            'thn_pembuatan' => 'Thn Pembuatan',
            'no_rangka' => 'No Rangka',
            'no_mesin' => 'No Mesin',
            'user' => 'User',
        ];
    }
}
