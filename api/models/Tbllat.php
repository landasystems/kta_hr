<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_lat".
 *
 * @property string $no_lat
 * @property string $biaya
 * @property string $kantor
 * @property string $no_telp
 * @property string $provider
 * @property string $user
 */
class Tbllat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_lat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_lat'], 'required'],
            [['no_lat'], 'string', 'max' => 20],
            [['biaya', 'no_telp', 'provider'], 'string', 'max' => 100],
            [['kantor', 'user'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_lat' => 'No Lat',
            'biaya' => 'Biaya',
            'kantor' => 'Kantor',
            'no_telp' => 'No Telp',
            'provider' => 'Provider',
            'user' => 'User',
        ];
    }
}
