<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_stock_apd".
 *
 * @property string $kode_apd
 * @property string $nama_apd
 * @property integer $jumlah_apd
 * @property integer $min_stock
 *
 * @property TblDpermohonanApd[] $tblDpermohonanApds
 */
class Tblstockapd extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_stock_apd';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kode_apd'], 'required'],
            [['jumlah_apd', 'min_stock'], 'integer'],
            [['kode_apd', 'nama_apd'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kode_apd' => 'Kode Apd',
            'nama_apd' => 'Nama Apd',
            'jumlah_apd' => 'Jumlah Apd',
            'min_stock' => 'Min Stock',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblDpermohonanApds()
    {
        return $this->hasMany(TblDpermohonanApd::className(), ['kd_apd' => 'kode_apd']);
    }
}
