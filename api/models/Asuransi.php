<?php

namespace app\models;

use Yii;

class Asuransi extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_asuransi';
    }

    public function rules()
    {
        return [
            [['id'], 'required'],
            [['nama'], 'string', 'max' => 30],
            [['telepon'], 'string', 'max' => 20],
            [['cp'], 'string', 'max' => 20]
        ];
    }

    public function attributeLabels()
    {
        return [
            'nama' => 'Nama',
            'telepon' => 'Telepon',
            'cp' => 'CP',
        ];
    }
}
