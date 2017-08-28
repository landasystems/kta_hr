<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_htrans_atk_keluar".
 *
 * @property string $no_transaksi
 * @property string $tgl
 */
class TblHtransAtkKeluar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_htrans_atk_keluar';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_transaksi'], 'required'],
            [['tgl','kd_karyawan'], 'safe'],
            [['no_transaksi','kd_karyawan'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_transaksi' => 'No Transaksi',
            'tgl' => 'Tgl',
        ];
    }
}
