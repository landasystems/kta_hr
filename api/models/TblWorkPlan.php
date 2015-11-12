<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_work_plan".
 *
 * @property integer $id
 * @property string $periode
 * @property string $department_id
 * @property string $section_id
 * @property string $prepared_by
 * @property string $checked_by
 * @property string $acknowledge
 */
class TblWorkPlan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_work_plan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['periode', 'department_id', 'section_id', 'prepared_by', 'checked_by', 'acknowledge'], 'required'],
            [['periode'], 'safe'],
            [['department_id', 'section_id', 'prepared_by', 'checked_by', 'acknowledge'], 'string', 'max' => 11]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'periode' => 'Periode',
            'department_id' => 'Department ID',
            'section_id' => 'Section ID',
            'prepared_by' => 'Prepared By',
            'checked_by' => 'Checked By',
            'acknowledge' => 'Acknowledge',
        ];
    }
}
