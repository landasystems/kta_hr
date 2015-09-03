<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_ijazah".
 *
 * @property string $no
 * @property string $tgl_masuk
 * @property string $no_ijazah
 * @property string $atas_nama
 * @property string $nama_sekolah
 * @property string $tgl_keluar
 * @property string $status
 */
class Tblijazah extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_ijazah';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no'], 'required'],
            [['tgl_masuk', 'tgl_keluar'], 'safe'],
            [['no', 'no_ijazah', 'status'], 'string', 'max' => 20],
            [['atas_nama', 'nama_sekolah'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no' => 'No',
            'tgl_masuk' => 'Tgl Masuk',
            'no_ijazah' => 'No Ijazah',
            'atas_nama' => 'Atas Nama',
            'nama_sekolah' => 'Nama Sekolah',
            'tgl_keluar' => 'Tgl Keluar',
            'status' => 'Status',
        ];
    }
}
