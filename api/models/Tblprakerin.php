<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_prakerin".
 *
 * @property string $no_prakerin
 * @property string $tgl
 * @property string $nama
 * @property string $no_surat_rekomendasi
 * @property string $asal_sekolah
 * @property string $tgl_mulai
 * @property string $tgl_selesai
 * @property string $bagian
 * @property string $foto
 */
class Tblprakerin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_prakerin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_prakerin'], 'required'],
            [['tgl', 'tgl_mulai', 'tgl_selesai'], 'safe'],
            [['no_prakerin', 'nama', 'no_surat_rekomendasi'], 'string', 'max' => 20],
            [['asal_sekolah'], 'string', 'max' => 50],
            [['bagian'], 'string', 'max' => 30],
            [['foto'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_prakerin' => 'No Prakerin',
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
