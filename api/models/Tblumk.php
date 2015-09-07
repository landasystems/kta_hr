<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_umk".
 *
 * @property string $no_umk
 * @property double $umk
 */
class Tblumk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_umk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_umk'], 'required'],
            [['umk'], 'number'],
            [['no_umk'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_umk' => 'No Umk',
            'umk' => 'Umk',
        ];
    }
}
