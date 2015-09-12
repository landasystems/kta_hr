<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_jpenilaian".
 *
 * @property string $no_jpenilaian
 * @property string $nik
 * @property string $nama
 * @property string $bagian
 * @property string $tgl_penilaian
 */
class TblJpenilaian extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_jpenilaian';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['no_jpenilaian'], 'required'],
            [['tgl_penilaian'], 'safe'],
            [['no_jpenilaian', 'nik', 'nik_penilai'], 'string', 'max' => 20],
            [['nama', 'penilai'], 'string', 'max' => 200],
            [['bagian', 'dep_penilai'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'no_jpenilaian' => 'No Jpenilaian',
            'nik' => 'Nik',
            'nama' => 'Nama',
            'bagian' => 'Bagian',
            'tgl_penilaian' => 'Tgl Penilaian',
        ];
    }

}
