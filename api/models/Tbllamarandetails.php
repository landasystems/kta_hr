<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_lamaran_karyawan".
 *
 * @property string $id
 * @property string $no_lamaran
 * @property string $perusahaan
 * @property string $bagian
 * @property string $periode_awal
 * @property string $periode_akhir
 */
class Tbllamarandetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_lamaran_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_lamaran'], 'required'],
            [['perusahaan', 'bagian','periode_awal', 'periode_akhir', 'periode_awal', 'periode_akhir'], 'safe'],
            [['perusahaan', 'bagian'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no_lamaran' => 'No Lamaran',
            'perusahaan' => 'Perusahaan',
            'bagian' => 'Bagian',
            'periode_awal' => 'Periode Awal',
            'periode_akhir' => 'Periode Akhir',
        ];
    }
}
