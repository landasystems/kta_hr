<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jenis_komplain".
 *
 * @property string $kd_jns
 * @property string $stat
 * @property string $bag
 * @property string $jns_komplain
 */
class JenisKomplain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jenis_komplain';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_jns'], 'required'],
            [['kd_jns', 'stat'], 'string', 'max' => 10],
            [['bag'], 'string', 'max' => 50],
            [['jns_komplain'], 'string', 'max' => 200],
            [['kd_jns'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_jns' => 'Kd Jns',
            'stat' => 'Stat',
            'bag' => 'Bag',
            'jns_komplain' => 'Jns Komplain',
        ];
    }
}
