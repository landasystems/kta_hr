<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_data_flegalitas".
 *
 * @property string $no
 * @property string $no_file
 * @property string $nm_file
 * @property string $instansi
 * @property string $atas_nm
 * @property string $periode
 * @property string $periode_mulai
 * @property string $periode_akhir
 * @property string $keterangan
 * @property string $jns_legalitas
 * @property string $tgl_pengesahan
 */
class TblDataFlegalitas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_data_flegalitas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no'], 'required'],
            [['tgl_pengesahan', 'keterangan', 'periode_mulai', 'periode_akhir'], 'safe'],
            [['no', 'no_file'], 'string', 'max' => 20],
            [['nm_file', 'periode'], 'string', 'max' => 300],
            [['instansi', 'atas_nm'], 'string', 'max' => 400],
            [['jns_legalitas'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no' => 'No',
            'no_file' => 'No File',
            'nm_file' => 'Nm File',
            'instansi' =>'Instansi',
            'atas_nm' => 'Atas Nm',
            'periode' => 'Periode',
            'periode_mulai' => 'Periode Mulai',
            'periode_akhir' => 'Periode Akhir',
            'keterangan' => 'Keterangan',
            'jns_legalitas' => 'Jns Legalitas',
            'tgl_pengesahan' => 'Tgl Pengesahan',
        ];
    }
}
