<?php

namespace app\barangmodels;

use Yii;

/**
 * This is the model class for table "model".
 *
 * @property string $kd_model
 * @property string $model
 * @property integer $standard
 */
class BarangModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'model';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_model'], 'required'],
            [['standard'], 'integer'],
            [['kd_model'], 'string', 'max' => 5],
            [['model'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_model' => 'Kd Model',
            'model' => 'Model',
            'standard' => 'Standard',
        ];
    }
}
