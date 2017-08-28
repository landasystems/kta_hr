<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_section".
 *
 * @property string $id_section
 * @property string $section
 * @property string $dept
 *
 * @property Pekerjaan[] $pekerjaans
 * @property TblDepartment $dept0
 */
class Section extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_section';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_section'], 'required'],
            [['id_section', 'dept'], 'string', 'max' => 20],
            [['section'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_section' => 'Id Section',
            'section' => 'Section',
            'dept' => 'Dept',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPekerjaans()
    {
        return $this->hasMany(Pekerjaan::className(), ['id_section' => 'id_section']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDept0()
    {
        return $this->hasOne(TblDepartment::className(), ['id_department' => 'dept']);
    }
}
