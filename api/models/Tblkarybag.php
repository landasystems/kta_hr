<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_kary_bag".
 *
 * @property string $nik
 * @property string $kd_bagian
 */
class Tblkarybag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_kary_bag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nik', 'kd_bagian'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nik' => 'Nik',
            'kd_bagian' => 'Kd Bagian',
        ];
    }
}
