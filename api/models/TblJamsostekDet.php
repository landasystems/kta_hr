<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_jamsostek_det".
 *
 * @property integer $id
 * @property string $nik
 * @property integer $kpj
 * @property string $periode_kepesertaan
 * @property integer $upah_tk
 * @property integer $jht
 * @property integer $jkm
 * @property integer $jkk
 * @property integer $pensiun
 * @property integer $iuran
 * @property string $keterangan
 */
class TblJamsostekDet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_jamsostek_det';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nik', 'periode_kepesertaan', 'upah_tk', 'jht', 'jkm', 'jkk', 'pensiun', 'iuran',], 'required'],
            [['upah_tk', 'jht', 'jkm', 'jkk', 'pensiun', 'iuran'], 'integer'],
            [['periode_kepesertaan','nn','kpj','hub_keluarga'], 'safe'],
            [['keterangan','nn','kpj','hub_keluarga'], 'string'],
            [['nik'], 'string', 'max' => 11]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nik' => 'Nik',
            'nn' => 'NN',
            'kpj' => 'Kpj',
            'periode_kepesertaan' => 'Periode Kepesertaan',
            'upah_tk' => 'Upah Tk',
            'jht' => 'Jht',
            'jkm' => 'Jkm',
            'jkk' => 'Jkk',
            'pensiun' => 'Pensiun',
            'iuran' => 'Iuran',
            'hub_keluarga' => 'Hub Keluarga',
            'keterangan' => 'Keterangan',
        ];
    }
}
