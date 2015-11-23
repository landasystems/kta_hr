<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_absent".
 *
 * @property string $no_absent
 * @property string $nik
 * @property string $nama
 * @property string $tanggal
 * @property string $jmasuk
 * @property string $jkeluar
 * @property string $ket
 */
class TblAbsent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_absent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_absent'], 'required'],
            [['tanggal','tgl_pembuatan','tanggal_kembali'], 'safe'],
            [['no_absent', 'nik', 'jmasuk', 'jkeluar'], 'string', 'max' => 20],
            [['nama'], 'string', 'max' => 50],
            [['ket'], 'string', 'max' => 30],
            [['ket_uraian'], 'string', 'max' => 600]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_absent' => 'No Absent',
            'nik' => 'Nik',
            'nama' => 'Nama',
            'tanggal' => 'Tanggal',
            'tanggal_kembali' => 'Tanggal Kembali',
            'jmasuk' => 'Jmasuk',
            'jkeluar' => 'Jkeluar',
            'ket' => 'Ket',
            'tgl_pembuatan' => 'Tanggal Pembuatan',
        ];
    }
}
