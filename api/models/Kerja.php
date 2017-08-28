<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "kerja".
 *
 * @property string $kd_ker
 * @property string $nm_kerja
 * @property string $kd_jab
 * @property string $jenis
 * @property integer $no
 */
class Kerja extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kerja';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_ker', 'jenis'], 'string', 'max' => 10],
            [['nm_kerja'], 'string', 'max' => 100],
            [['kd_jab'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_ker' => 'Kd Ker',
            'nm_kerja' => 'Nm Kerja',
            'kd_jab' => 'Kd Jab',
            'jenis' => 'Jenis',
            'no' => 'No',
        ];
    }
}
