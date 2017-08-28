<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_lembur".
 *
 * @property string $no_lembur
 * @property string $nik
 * @property string $nama
 * @property string $tanggal
 * @property integer $jam_lmbr
 * @property double $total_lmbr
 */
class Tbllembur extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_lembur';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_lembur'], 'required'],
            [['tanggal'], 'safe'],
            [['jam_lmbr'], 'integer'],
            [['total_lmbr'], 'number'],
            [['no_lembur', 'nik'], 'string', 'max' => 20],
            [['nama'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_lembur' => 'No Lembur',
            'nik' => 'Nik',
            'nama' => 'Nama',
            'tanggal' => 'Tanggal',
            'jam_lmbr' => 'Jam Lmbr',
            'total_lmbr' => 'Total Lmbr',
        ];
    }
}
