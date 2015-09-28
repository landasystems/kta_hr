<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_monitoring_dservice".
 *
 * @property integer $id
 * @property string $no
 * @property string $ket_service
 * @property double $biaya
 *
 * @property TblMonitoringService $no0
 */
class TblMonitoringDservice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_monitoring_dservice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['biaya'], 'number'],
            [['no'], 'string', 'max' => 20],
            [['ket_service'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no' => 'No',
            'ket_service' => 'Ket Service',
            'biaya' => 'Biaya',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNo0()
    {
        return $this->hasOne(TblMonitoringService::className(), ['no_mservice' => 'no']);
    }
}
