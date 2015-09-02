<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_spermohonan_cuti".
 *
 * @property string $no_cuti
 * @property string $nik
 * @property string $nama
 * @property string $jabatan
 * @property string $mkerja
 * @property string $tgl_cuti
 * @property string $tgl_selesai_cuti
 * @property integer $lama_cuti
 * @property string $lama_cuti_huruf
 * @property string $keperluan
 */
class Tblspermohonancuti extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_spermohonan_cuti';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_cuti'], 'required'],
            [['tgl_cuti', 'tgl_selesai_cuti'], 'safe'],
            [['lama_cuti'], 'integer'],
            [['no_cuti', 'nik', 'nama', 'jabatan', 'mkerja', 'lama_cuti_huruf'], 'string', 'max' => 20],
            [['keperluan'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_cuti' => 'No Cuti',
            'nik' => 'Nik',
            'nama' => 'Nama',
            'jabatan' => 'Jabatan',
            'mkerja' => 'Mkerja',
            'tgl_cuti' => 'Tgl Cuti',
            'tgl_selesai_cuti' => 'Tgl Selesai Cuti',
            'lama_cuti' => 'Lama Cuti',
            'lama_cuti_huruf' => 'Lama Cuti Huruf',
            'keperluan' => 'Keperluan',
        ];
    }
}
