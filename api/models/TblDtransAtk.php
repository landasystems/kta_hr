<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_dtrans_atk".
 *
 * @property string $no_trans
 * @property string $kd_brng
 * @property string $nm_brng
 * @property integer $jmlh_brng
 */
class TblDtransAtk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_dtrans_atk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jmlh_brng'], 'integer'],
            [['no_trans', 'kd_brng', 'nm_brng'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_trans' => 'No Trans',
            'kd_brng' => 'Kd Brng',
            'nm_brng' => 'Nm Brng',
            'jmlh_brng' => 'Jmlh Brng',
        ];
    }
}
