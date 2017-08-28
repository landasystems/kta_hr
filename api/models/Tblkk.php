<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_kk".
 *
 * @property string $no
 * @property string $no_kk
 * @property string $kepala_kk
 *
 * @property TblDataBpjs[] $tblDataBpjs
 */
class Tblkk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_kk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no'], 'required'],
            [['no', 'no_kk'], 'string', 'max' => 20],
            [['kepala_kk'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no' => 'No',
            'no_kk' => 'No Kk',
            'kepala_kk' => 'Kepala Kk',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblDataBpjs()
    {
        return $this->hasMany(TblDataBpjs::className(), ['no_fk' => 'no']);
    }
}
