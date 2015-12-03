<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_dtrans_potongan".
 *
 * @property string $no
 * @property string $kd_pot
 * @property string $nm_pot
 * @property double $jmlh
 *
 * @property TblPotongan $kdPot
 * @property TblHtransPotongan $no0
 */
class TblDtransPotongan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_dtrans_potongan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jmlh','perbulan','parent_id'], 'number'],
            [['perbulan','parent_id'], 'safe'],
            [['no', 'kd_pot', 'nm_pot'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no' => 'No',
            'kd_pot' => 'Kd Pot',
            'nm_pot' => 'Nm Pot',
            'jmlh' => 'Jmlh',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKdPot()
    {
        return $this->hasOne(TblPotongan::className(), ['kode_potongan' => 'kd_pot']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNo0()
    {
        return $this->hasOne(TblHtransPotongan::className(), ['no_pot' => 'no']);
    }
}
