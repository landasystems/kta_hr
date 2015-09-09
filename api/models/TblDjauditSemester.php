<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_djaudit_semester".
 *
 * @property string $no
 * @property string $dept_auditee
 * @property string $auditee
 * @property string $dept_auditor
 * @property string $auditor
 *
 * @property TblHjauditSemester $no0
 */
class TblDjauditSemester extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_djaudit_semester';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no', 'dept_auditee', 'auditee', 'dept_auditor', 'auditor'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no' => 'No',
            'dept_auditee' => 'Dept Auditee',
            'auditee' => 'Auditee',
            'dept_auditor' => 'Dept Auditor',
            'auditor' => 'Auditor',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNo0()
    {
        return $this->hasOne(TblHjauditSemester::className(), ['no_audit' => 'no']);
    }
}
