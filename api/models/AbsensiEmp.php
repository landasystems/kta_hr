<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "emp".
 *
 * @property integer $emp_id_auto
 * @property string $nik
 * @property integer $cab_id_auto
 * @property integer $dept_id_auto
 * @property integer $func_id_auto
 * @property string $first_name
 * @property string $last_name
 * @property integer $gender
 * @property string $birth_date
 * @property string $religion
 * @property string $address
 * @property string $phone
 * @property string $ssn
 * @property integer $married
 * @property integer $is_spouse
 * @property string $education
 * @property integer $dependent
 * @property string $pin
 * @property string $alias
 * @property string $photo_path
 * @property string $begin_date
 * @property string $resign_date
 * @property string $lastupdate_date
 * @property string $lastupdate_user
 * @property integer $emp_status
 * @property integer $priv
 * @property string $rfid
 * @property string $pwd
 * @property integer $status_kerja
 * @property integer $bulan_kontrak
 * @property string $tgl_habis_kontrak
 * @property string $tgl_mulai_training
 * @property string $tgl_mulai_kontrak
 * @property string $tgl_mulai_tetap
 * @property integer $status_syn_fcs
 * @property string $stamp_sync
 */
class AbsensiEmp extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'emp';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbabsensi');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cab_id_auto', 'dept_id_auto', 'func_id_auto', 'gender', 'married', 'is_spouse', 'dependent', 'emp_status', 'priv', 'status_kerja', 'bulan_kontrak', 'status_syn_fcs'], 'integer'],
            [['birth_date', 'begin_date', 'resign_date', 'lastupdate_date', 'tgl_habis_kontrak', 'tgl_mulai_training', 'tgl_mulai_kontrak', 'tgl_mulai_tetap'], 'safe'],
            [['pin', 'lastupdate_date'], 'required'],
            [['nik', 'pin'], 'string', 'max' => 32],
            [['first_name', 'last_name'], 'string', 'max' => 100],
            [['religion', 'ssn', 'education', 'alias'], 'string', 'max' => 30],
            [['address', 'photo_path'], 'string', 'max' => 255],
            [['phone', 'lastupdate_user'], 'string', 'max' => 50],
            [['rfid'], 'string', 'max' => 20],
            [['pwd'], 'string', 'max' => 8],
            [['stamp_sync'], 'string', 'max' => 6],
            [['pin'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'emp_id_auto' => 'Emp Id Auto',
            'nik' => 'Nik',
            'cab_id_auto' => 'Cab Id Auto',
            'dept_id_auto' => 'Dept Id Auto',
            'func_id_auto' => 'Func Id Auto',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'gender' => 'Gender',
            'birth_date' => 'Birth Date',
            'religion' => 'Religion',
            'address' => 'Address',
            'phone' => 'Phone',
            'ssn' => 'Ssn',
            'married' => 'Married',
            'is_spouse' => 'Is Spouse',
            'education' => 'Education',
            'dependent' => 'Dependent',
            'pin' => 'Pin',
            'alias' => 'Alias',
            'photo_path' => 'Photo Path',
            'begin_date' => 'Begin Date',
            'resign_date' => 'Resign Date',
            'lastupdate_date' => 'Lastupdate Date',
            'lastupdate_user' => 'Lastupdate User',
            'emp_status' => 'Emp Status',
            'priv' => 'Priv',
            'rfid' => 'Rfid',
            'pwd' => 'Pwd',
            'status_kerja' => 'Status Kerja',
            'bulan_kontrak' => 'Bulan Kontrak',
            'tgl_habis_kontrak' => 'Tgl Habis Kontrak',
            'tgl_mulai_training' => 'Tgl Mulai Training',
            'tgl_mulai_kontrak' => 'Tgl Mulai Kontrak',
            'tgl_mulai_tetap' => 'Tgl Mulai Tetap',
            'status_syn_fcs' => 'Status Syn Fcs',
            'stamp_sync' => 'Stamp Sync',
        ];
    }

}
