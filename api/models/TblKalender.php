<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_kalender".
 *
 * @property string $no
 * @property string $tgl
 * @property string $ket
 */
class TblKalender extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_kalender';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no'], 'required'],
            [['tgl'], 'safe'],
            [['no'], 'string', 'max' => 20],
            [['ket'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no' => 'No',
            'tgl' => 'Tgl',
            'ket' => 'Ket',
        ];
    }
    
    
    
}
