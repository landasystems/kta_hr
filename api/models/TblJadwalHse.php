<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_jadwal_hse".
 *
 * @property string $no
 * @property string $tgl_hse
 * @property string $nama
 * @property string $materi
 */
class TblJadwalHse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_jadwal_hse';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no'], 'required'],
            [['tgl_hse'], 'safe'],
            [['no'], 'string', 'max' => 20],
            [['nik_karyawan'], 'string', 'max' => 50],
            [['materi'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no' => 'No',
            'tgl_hse' => 'Tgl Hse',
            'nik_karyawan' => 'Nama',
            'materi' => 'Materi',
        ];
    }
}
