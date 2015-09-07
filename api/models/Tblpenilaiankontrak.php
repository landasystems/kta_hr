<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_penilaian_kontrak".
 *
 * @property string $no_kntrk
 * @property string $tgl
 * @property string $nm_kontrak
 * @property string $penilaian
 * @property string $keterangan
 */
class Tblpenilaiankontrak extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_penilaian_kontrak';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl'], 'safe'],
            [['no_kntrk', 'nm_kontrak', 'penilaian'], 'string', 'max' => 20],
            [['keterangan'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_kntrk' => 'No Kntrk',
            'tgl' => 'Tgl',
            'nm_kontrak' => 'Nm Kontrak',
            'penilaian' => 'Penilaian',
            'keterangan' => 'Keterangan',
        ];
    }
}
