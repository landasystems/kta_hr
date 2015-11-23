<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_work_plan_det".
 *
 * @property integer $id
 * @property integer $work_plan_id
 * @property string $activity
 * @property string $due_date
 * @property string $actual_date
 */
class TblWorkPlanDet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_work_plan_det';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['work_plan_id', 'activity', 'due_date', 'actual_date'], 'required'],
            [['work_plan_id'], 'integer'],
            [['due_date', 'actual_date'], 'safe'],
            [['activity'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'work_plan_id' => 'Work Plan ID',
            'activity' => 'Activity',
            'due_date' => 'Due Date',
            'actual_date' => 'Actual Date',
        ];
    }
}
