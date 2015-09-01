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
class Bagian extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bagian';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_bag'], 'required'],
            [['kd_bag'], 'string', 'max' => 6],
            [['bagian'], 'string', 'max' => 50],
            [['kd_kerja'], 'string', 'max' => 10],
            [['kd_bag'], 'unique'],
            [['kd_bag'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_bag' => 'Kd Bag',
            'bagian' => 'Bagian',
            'kd_kerja' => 'Kd Kerja',
        ];
    }
}
