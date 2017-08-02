<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bagian".
 *
 * @property string $kd_bag
 * @property string $bagian
 * @property string $kd_kerja
 */
class DetSatuan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'det_satuan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','nama','konversi','kode_apd','kode_atk'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'nama' => 'Nama',
            'konversi' => 'Konversi',
            'apd_id' => 'Kode APD',
            'atk_id' => 'Kode ATK',
        ];
    }
}
