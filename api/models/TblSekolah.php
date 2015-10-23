<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_sekolah".
 *
 * @property integer $id
 * @property string $nama_sekolah
 * @property string $alamat
 * @property string $telepon
 * @property string $contact_person
 * @property string $jurusan
 */
class TblSekolah extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_sekolah';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_sekolah', 'contact_person','jenjang'], 'string', 'max' => 30],
            [['alamat', 'jurusan'], 'string', 'max' => 500],
            [['telepon','kode'], 'string', 'max' => 14]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kode' => 'Kode Sekolah',
            'nama_sekolah' => 'Nama Sekolah',
            'alamat' => 'Alamat',
            'telepon' => 'Telepon',
            'contact_person' => 'Contact Person',
            'jurusan' => 'Jurusan',
        ];
    }
}
