<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_jamsostek".
 *
 * @property string $kpj
 * @property string $nik
 * @property string $nn
 * @property string $p_kepesertaan
 */
class Tbljamsostek extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_jamsostek';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['kpj'], 'required'],
            [['p_kepesertaan','kpj'], 'safe'],
            [['kpj'], 'string', 'max' => 50],
            [['nik', 'nn'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kpj' => 'Kpj',
            'nik' => 'Nik',
            'nn' => 'Nn',
            'p_kepesertaan' => 'P Kepesertaan',
        ];
    }
}
