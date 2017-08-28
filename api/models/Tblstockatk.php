<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_stock_atk".
 *
 * @property string $kode_brng
 * @property string $nama_brng
 * @property integer $jumlah_brng
 * @property integer $min_stock
 * @property integer $merk
 * @property integer $keterangan
 *
 * @property TblDpermohonanAtk[] $tblDpermohonanAtks
 */
class Tblstockatk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_stock_atk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kode_brng', 'nama_brng', 'merk'], 'required'],
            [['jumlah_brng', 'min_stock'], 'integer'],
            [['kode_brng'], 'string', 'max' => 20],
            [['nama_brng', 'merk'], 'string', 'max' => 200],
            [['keterangan'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kode_brng' => 'Kode Brng',
            'nama_brng' => 'Nama Brng',
            'jumlah_brng' => 'Jumlah Brng',
            'min_stock' => 'Min Stock',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblDpermohonanAtks()
    {
        return $this->hasMany(TblDpermohonanAtk::className(), ['kd_atk' => 'kode_brng']);
    }
}
