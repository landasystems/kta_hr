<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_htrans_apd_keluar".
 *
 * @property string $no_transaksi
 * @property string $tgl
 *
 * @property TblDtransApdKeluar[] $tblDtransApdKeluars
 */
class TblHtransApdKeluar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_htrans_apd_keluar';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_transaksi'], 'required'],
            [['tgl'], 'safe'],
            [['no_transaksi','nik_karyawan'], 'string', 'max' => 20]
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
            'nik_karyawan' => 'NIK',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblDtransApdKeluars()
    {
        return $this->hasMany(TblDtransApdKeluar::className(), ['no_trans' => 'no_transaksi']);
    }
}
