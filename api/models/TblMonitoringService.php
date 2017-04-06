<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_monitoring_service".
 *
 * @property string $no_mservice
 * @property string $tgl
 * @property string $nopol
 * @property string $merk
 * @property string $tipe
 * @property string $model
 * @property string $warna
 * @property string $thn_pembuatan
 * @property string $no_rangka
 * @property string $no_mesin
 * @property string $user
 * @property double $total_biaya
 *
 * @property TblMonitoringDservice[] $tblMonitoringDservices
 */
class TblMonitoringService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_monitoring_service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_mservice'], 'required'],
            [['tgl','km'], 'safe'],
            [['total_biaya'], 'number'],
            [['no_mservice', 'nopol', 'merk', 'tipe', 'model', 'warna', 'thn_pembuatan'], 'string', 'max' => 20],
            [['no_rangka', 'no_mesin', 'user'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_mservice' => 'No Mservice',
            'tgl' => 'Tgl',
            'nopol' => 'Nopol',
            'merk' => 'Merk',
            'km' => 'KM',
            'tipe' => 'Tipe',
            'model' => 'Model',
            'warna' => 'Warna',
            'thn_pembuatan' => 'Thn Pembuatan',
            'no_rangka' => 'No Rangka',
            'no_mesin' => 'No Mesin',
            'user' => 'User',
            'total_biaya' => 'Total Biaya',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblMonitoringDservices()
    {
        return $this->hasMany(TblMonitoringDservice::className(), ['no' => 'no_mservice']);
    }
}
