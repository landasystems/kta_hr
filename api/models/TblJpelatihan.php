<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_jpelatihan".
 *
 * @property string $no_jpelatihan
 * @property string $tgl
 * @property string $jam
 * @property string $tmpt
 * @property string $kegiatan
 *
 * @property TblDjpelatihan[] $tblDjpelatihans
 */
class TblJpelatihan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_jpelatihan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_jpelatihan'], 'required'],
            [['tgl'], 'safe'],
            [['no_jpelatihan', 'jam'], 'string', 'max' => 20],
            [['tmpt'], 'string', 'max' => 50],
            [['kegiatan'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_jpelatihan' => 'No Jpelatihan',
            'tgl' => 'Tgl',
            'jam' => 'Jam',
            'tmpt' => 'Tmpt',
            'kegiatan' => 'Kegiatan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblDjpelatihans()
    {
        return $this->hasMany(TblDjpelatihan::className(), ['no' => 'no_jpelatihan']);
    }
}
