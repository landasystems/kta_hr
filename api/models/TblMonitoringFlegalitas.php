<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_monitoring_flegalitas".
 *
 * @property string $no_mflegalitas
 * @property string $tgl_mflegalitas
 * @property string $no_file
 * @property string $nm_file
 * @property string $instansi
 * @property string $atas_nm
 * @property string $jns_legalitas
 * @property string $tgl_pengesahan
 * @property string $masa_berlaku
 * @property string $ket
 */
class Tblmonitoringflegalitas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_monitoring_flegalitas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_mflegalitas'], 'required'],
            [['tgl_mflegalitas', 'tgl_pengesahan', 'masa_berlaku'], 'safe'],
            [['no_mflegalitas', 'no_file'], 'string', 'max' => 20],
            [['nm_file'], 'string', 'max' => 200],
            [['instansi', 'atas_nm', 'jns_legalitas'], 'string', 'max' => 100],
            [['ket'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_mflegalitas' => 'No Mflegalitas',
            'tgl_mflegalitas' => 'Tgl Mflegalitas',
            'no_file' => 'No File',
            'nm_file' => 'Nm File',
            'instansi' => 'Instansi',
            'atas_nm' => 'Atas Nm',
            'jns_legalitas' => 'Jns Legalitas',
            'tgl_pengesahan' => 'Tgl Pengesahan',
            'masa_berlaku' => 'Masa Berlaku',
            'ket' => 'Ket',
        ];
    }
}
