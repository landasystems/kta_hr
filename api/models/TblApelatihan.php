<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_apelatihan".
 *
 * @property string $no_apelatihan
 * @property string $jns_pelatihan
 * @property string $sumber_pelatihan
 * @property string $waktu
 * @property string $peserta
 * @property string $bahasan
 * @property string $alat_peraga
 * @property string $keterangan
 */
class TblApelatihan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_apelatihan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_apelatihan'], 'required'],
            [['waktu'], 'safe'],
            [['no_apelatihan'], 'string', 'max' => 20],
            [['jns_pelatihan', 'sumber_pelatihan', 'peserta', 'bahasan', 'alat_peraga', 'keterangan'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_apelatihan' => 'No Apelatihan',
            'jns_pelatihan' => 'Jns Pelatihan',
            'sumber_pelatihan' => 'Sumber Pelatihan',
            'waktu' => 'Waktu',
            'peserta' => 'Peserta',
            'bahasan' => 'Bahasan',
            'alat_peraga' => 'Alat Peraga',
            'keterangan' => 'Keterangan',
        ];
    }
}
