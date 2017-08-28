<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hjaudit_semester".
 *
 * @property string $no_audit
 * @property string $tgl
 * @property string $hari
 * @property string $jam
 * @property integer $audit_ke
 *
 * @property TblDjauditSemester[] $tblDjauditSemesters
 */
class TblHjauditSemester extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hjaudit_semester';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_audit'], 'required'],
            [['tgl'], 'safe'],
            [['audit_ke'], 'integer'],
            [['no_audit', 'hari', 'jam'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_audit' => 'No Audit',
            'tgl' => 'Tgl',
            'hari' => 'Hari',
            'jam' => 'Jam',
            'audit_ke' => 'Audit Ke',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblDjauditSemesters()
    {
        return $this->hasMany(TblDjauditSemester::className(), ['no' => 'no_audit']);
    }
}
