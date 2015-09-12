<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_htrans_apd_masuk".
 *
 * @property string $no_transaksi
 * @property string $tgl
 *
 * @property TblDtransApdMasuk[] $tblDtransApdMasuks
 */
class TblHtransApdMasuk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_htrans_apd_masuk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_transaksi'], 'required'],
            [['tgl'], 'safe'],
            [['no_transaksi'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_transaksi' => 'No Transaksi',
            'tgl' => 'Tgl',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblDtransApdMasuks()
    {
        return $this->hasMany(TblDtransApdMasuk::className(), ['no_trans' => 'no_transaksi']);
    }
}
