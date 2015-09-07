<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_karyawan_kontrak".
 *
 * @property string $no_kontrak
 * @property string $nik
 * @property string $nama
 * @property string $status_karyawan
 */
class Tblkaryawankontrak extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_karyawan_kontrak';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_kontrak'], 'required'],
            [['no_kontrak', 'nik', 'status_karyawan'], 'string', 'max' => 20],
            [['nama'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_kontrak' => 'No Kontrak',
            'nik' => 'Nik',
            'nama' => 'Nama',
            'status_karyawan' => 'Status Karyawan',
        ];
    }
}
