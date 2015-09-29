<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sub_section".
 *
 * @property string $id_sub
 * @property string $sub_section
 */
class SubSection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pekerjaan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_kerja', 'kerja'], 'required'],
            [['id_section', 'kerja','kd_kerja'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_kerja' => 'Kd Kerja',
            'kerja' => 'Kerja',
            'id_section' => 'Section',
        ];
    }
}
