<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_proposal".
 *
 * @property string $no_proposal
 * @property string $no_surat
 * @property string $tgl
 * @property string $dari
 * @property string $untuk
 * @property string $perihal
 * @property string $tindak_lanjut
 */
class Tblproposal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_proposal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_proposal'], 'required'],
            [['tgl'], 'safe'],
            [['no_proposal'], 'string', 'max' => 20],
            [['no_surat'], 'string', 'max' => 100],
            [['dari', 'untuk'], 'string', 'max' => 200],
            [['perihal', 'tindak_lanjut'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_proposal' => 'No Proposal',
            'no_surat' => 'No Surat',
            'tgl' => 'Tgl',
            'dari' => 'Dari',
            'untuk' => 'Untuk',
            'perihal' => 'Perihal',
            'tindak_lanjut' => 'Tindak Lanjut',
        ];
    }
}
