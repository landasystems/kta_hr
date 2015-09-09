<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_agenda_umum".
 *
 * @property string $no_agenda
 * @property string $tgl
 * @property string $agenda
 */
class TblAgendaUmum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_agenda_umum';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl'], 'safe'],
            [['no_agenda'], 'string', 'max' => 20],
            [['agenda'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_agenda' => 'No Agenda',
            'tgl' => 'Tgl',
            'agenda' => 'Agenda',
        ];
    }
}
