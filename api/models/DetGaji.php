<?php

namespace app\models;

use Yii;

class DetGaji extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_det_gaji';
    }

    public function rules()
    {
        return [
            [["id", "nik", "gaji", "status"], 'integer'],
            [['tgl'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nik' => 'Kd Jab',
            "gaji" => "Gaji",
            'status' => 'Status',
            "tgl" => "Tanggal",
        ];
    }
}
