<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_jabatan".
 *
 * @property string $id_jabatan
 * @property string $jabatan
 * @property string $krj
 *
 * @property Pekerjaan $krj0
 */
class Jabatan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_jabatan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_jabatan'], 'required'],
            [['id_jabatan', 'krj'], 'string', 'max' => 20],
            [['jabatan'], 'string', 'max' => 50],
            [['id_jabatan'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_jabatan' => 'Id Jabatan',
            'jabatan' => 'Jabatan',
            'krj' => 'Krj',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKrj0()
    {
        return $this->hasOne(Pekerjaan::className(), ['kd_kerja' => 'krj']);
    }
}
