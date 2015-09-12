<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_dtrans_apd_masuk".
 *
 * @property string $no_trans
 * @property string $kd_apd
 * @property string $nm_apd
 * @property integer $jmlh_apd
 *
 * @property TblHtransApdMasuk $noTrans
 */
class TblDtransApdMasuk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_dtrans_apd_masuk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jmlh_apd'], 'integer'],
            [['no_trans', 'kd_apd', 'nm_apd'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_trans' => 'No Trans',
            'kd_apd' => 'Kd Apd',
            'nm_apd' => 'Nm Apd',
            'jmlh_apd' => 'Jmlh Apd',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNoTrans()
    {
        return $this->hasOne(TblHtransApdMasuk::className(), ['no_transaksi' => 'no_trans']);
    }
}
