<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_pemakian_lat".
 *
 * @property string $no_pemakian
 * @property string $tgl
 * @property string $biaya
 * @property string $kantor
 * @property string $no_telp
 * @property string $provider
 * @property string $user
 * @property string $no_reff
 * @property string $tgl_reff
 * @property double $jmlh
 */
class Tblpemakianlat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_pemakian_lat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_pemakian'], 'required'],
            [['tgl', 'tgl_reff'], 'safe'],
            [['jmlh'], 'number'],
            [['no_pemakian', 'no_reff'], 'string', 'max' => 20],
            [['biaya', 'kantor', 'provider', 'user'], 'string', 'max' => 200],
            [['no_telp'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_pemakian' => 'No Pemakian',
            'tgl' => 'Tgl',
            'biaya' => 'Biaya',
            'kantor' => 'Kantor',
            'no_telp' => 'No Telp',
            'provider' => 'Provider',
            'user' => 'User',
            'no_reff' => 'No Reff',
            'tgl_reff' => 'Tgl Reff',
            'jmlh' => 'Jmlh',
        ];
    }
}
