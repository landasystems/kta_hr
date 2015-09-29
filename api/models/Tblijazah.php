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
class Tblijazah extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_ijazah';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['no', 'nik'], 'required'],
            [['tgl_masuk', 'tgl_keluar', 'tgl_lahir', 'tempat_lahir', 'tgl_ijazah'], 'safe'],
            [['no', 'nik', 'no_ijazah', 'status', 'tempat_lahir'], 'string', 'max' => 20],
            [['atas_nama', 'nama_sekolah'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'no' => 'No',
            'tgl_masuk' => 'Tgl Masuk',
            'no_ijazah' => 'No Ijazah',
            'atas_nama' => 'Atas Nama',
            'nik' => 'NIK',
            'nama_sekolah' => 'Nama Sekolah',
            'tgl_keluar' => 'Tgl Keluar',
            'status' => 'Status',
            'tempat_lahir' => 'Tempat Lahir',
            'tgl_ijazah' => 'Tanggal Ijazah',
            'tgl_lahir' => 'Tanggal Lahir',
        ];
    }

}
