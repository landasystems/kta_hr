<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_bagian".
 *
 * @property string $kd_bagian
 * @property string $bagian
 * @property integer $urutan
 */
class TblBagian extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_bagian';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_bagian'], 'required'],
            [['urutan'], 'integer'],
            [['kd_bagian'], 'string', 'max' => 20],
            [['bagian'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_bagian' => 'Kd Bagian',
            'bagian' => 'Bagian',
            'urutan' => 'Urutan',
        ];
    }
}
