<?php

namespace app\models;

use Yii;
use app\models\TblKaryawan;
/**
 * This is the model class for table "att_log".
 *
 * @property integer $att_id
 * @property string $sn
 * @property string $scan_date
 * @property string $pin
 * @property integer $verify_mode
 * @property integer $io_mode
 * @property integer $work_code
 * @property integer $ex_id
 * @property integer $flag
 * @property string $rowguid
 * @property integer $io_mode_update
 */
class AbsensiEttLog extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'att_log';
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
            [['sn', 'scan_date', 'pin', 'verify_mode', 'work_code', 'rowguid'], 'required'],
            [['scan_date'], 'safe'],
            [['verify_mode', 'io_mode', 'work_code', 'ex_id', 'flag', 'io_mode_update'], 'integer'],
            [['sn'], 'string', 'max' => 30],
            [['pin', 'rowguid'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'att_id' => 'Att ID',
            'sn' => 'Sn',
            'scan_date' => 'Scan Date',
            'pin' => 'Pin',
            'verify_mode' => 'Verify Mode',
            'io_mode' => 'Io Mode',
            'work_code' => 'Work Code',
            'ex_id' => 'Ex ID',
            'flag' => 'Flag',
            'rowguid' => 'Rowguid',
            'io_mode_update' => 'Io Mode Update',
        ];
    }

    public function getKaryawan() {
        return $this->hasOne(AbsensiEmp::className(), ['pin' => 'pin']);
    }
    
    public function absen($date_start='', $date_end='', $niknama=''){
//        $query = new Query;
//        $query->from('ftm.att_log AS abs')
//                ->join('RIGHT JOIN', 'ftm.emp', 'emp.emp_id_auto = abs.pin AND date(abs.scan_date)="'.date('Y-m-d',strtotime($params['tanggal'])).'"')
//                ->select('emp.nik, date(abs.scan_date) AS tanggal, min(abs.scan_date) AS masuk, max(abs.scan_date) AS keluar')
//                ->groupBy('tanggal, nik');
//        
//        $query->andWhere('date(abs.scan_date)>="2015-07-26" AND date(abs.scan_date)<="2015-09-26"');
//        if (isset($params['niknama'])) {
//            $query->andWhere('(kry.nik LIKE "%'.$params['niknama'].'%" OR kry.nama LIKE "%'.$params['niknama'].'%")');
//        }        
//        
        
//        SELECT emp.nik, date(abs.scan_date) AS tanggal, min(abs.scan_date) AS masuk, max(abs.scan_date) AS keluar 
//FROM `ftm`.`att_log` `abs` RIGHT JOIN `ftm`.`emp` ON emp.emp_id_auto = abs.pin  
//WHERE date(abs.scan_date)>="2015-07-26" AND date(abs.scan_date)<="2015-09-26"

        $kry = Tblkaryawan::aktif();
        $result = [];
        foreach($kry as $r){
            $result[] = ['nik'=>$r->nik,'nama'=>$r->nama];
        }
        return $result;
        
    }

}
