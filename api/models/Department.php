<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_department".
 *
 * @property string $id_department
 * @property string $department
 *
 * @property TblSection[] $tblSections
 */
class Department extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_department';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_department'], 'required'],
            [['id_department', 'department'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_department' => 'Id Department',
            'department' => 'Department',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblSections()
    {
        return $this->hasMany(TblSection::className(), ['dept' => 'id_department']);
    }
}
