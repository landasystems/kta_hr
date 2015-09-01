<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jenis_brg".
 *
 * @property string $kd_jenis
 * @property string $jenis_brg
 * @property integer $kd
 */
class JenisBrg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jenis_brg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_jenis'], 'required'],
            [['kd'], 'integer'],
            [['kd_jenis'], 'string', 'max' => 10],
            [['jenis_brg'], 'string', 'max' => 50],
            [['kd_jenis'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_jenis' => 'Kd Jenis',
            'jenis_brg' => 'Jenis Brg',
            'kd' => 'Kd',
        ];
    }
}
