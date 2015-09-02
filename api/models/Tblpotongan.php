<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_potongan".
 *
 * @property string $kode_potongan
 * @property string $nm_potongan
 *
 * @property TblDtransPotongan[] $tblDtransPotongans
 */
class Tblpotongan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_potongan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kode_potongan'], 'required'],
            [['kode_potongan'], 'string', 'max' => 20],
            [['nm_potongan'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kode_potongan' => 'Kode Potongan',
            'nm_potongan' => 'Nm Potongan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblDtransPotongans()
    {
        return $this->hasMany(TblDtransPotongan::className(), ['kd_pot' => 'kode_potongan']);
    }
}
