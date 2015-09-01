<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_magang".
 *
 * @property string $no_magang
 * @property string $tgl
 * @property string $nama
 * @property string $no_surat_rekomendasi
 * @property string $asal_sekolah
 * @property string $tgl_mulai
 * @property string $tgl_selesai
 * @property string $bagian
 * @property string $foto
 */
class Tblmagang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_magang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_magang'], 'required'],
            [['tgl', 'tgl_mulai', 'tgl_selesai'], 'safe'],
            [['no_magang', 'nama', 'no_surat_rekomendasi', 'bagian'], 'string', 'max' => 20],
            [['asal_sekolah'], 'string', 'max' => 50],
            [['foto'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_magang' => 'No Magang',
            'tgl' => 'Tgl',
            'nama' => 'Nama',
            'no_surat_rekomendasi' => 'No Surat Rekomendasi',
            'asal_sekolah' => 'Asal Sekolah',
            'tgl_mulai' => 'Tgl Mulai',
            'tgl_selesai' => 'Tgl Selesai',
            'bagian' => 'Bagian',
            'foto' => 'Foto',
        ];
    }
}
