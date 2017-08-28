<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "kpb".
 *
 * @property string $no_wo
 * @property string $kd_jab
 * @property integer $status
 */
class Kpb extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kpb';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['no_wo', 'kd_jab'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_wo' => 'No Wo',
            'kd_jab' => 'Kd Jab',
            'status' => 'Status',
        ];
    }
}
