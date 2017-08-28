<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_htrans_potongan".
 *
 * @property string $no_pot
 * @property string $no_gaji
 * @property string $tgl
 * @property string $nik
 * @property double $total
 *
 * @property TblDtransPotongan[] $tblDtransPotongans
 * @property TblGajiProduksi $noGaji
 */
class TblHtransPotongan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_htrans_potongan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_pot','nik'], 'required'],
            [['tgl','cicilan'], 'safe'],
            [['total','cicilan'], 'number'],
            [['no_pot', 'no_gaji', 'nik'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_pot' => 'No Pot',
            'no_gaji' => 'No Gaji',
            'tgl' => 'Tgl',
            'nik' => 'Nik',
            'total' => 'Total',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblDtransPotongans()
    {
        return $this->hasMany(TblDtransPotongan::className(), ['no' => 'no_pot']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNoGaji()
    {
        return $this->hasOne(TblGajiProduksi::className(), ['no_gaji' => 'no_gaji']);
    }
}
