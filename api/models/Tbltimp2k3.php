<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tim_p2k3".
 *
 * @property string $no_tim
 * @property string $tgl
 * @property string $nama
 * @property string $bagian
 */
class Tbltimp2k3 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tim_p2k3';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_tim'], 'required'],
            [['tgl'], 'safe'],
            [['no_tim', 'bagian'], 'string', 'max' => 20],
            [['nama'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_tim' => 'No Tim',
            'tgl' => 'Tgl',
            'nama' => 'Nama',
            'bagian' => 'Bagian',
        ];
    }
}
