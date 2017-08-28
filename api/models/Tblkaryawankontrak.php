<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_karyawan_kontrak".
 *
 * @property string $no_kontrak
 * @property string $nik
 * @property string $nama
 * @property string $status_karyawan
 */
class Tblkaryawankontrak extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_karyawan_kontrak';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['no_kontrak'], 'required'],
            [['no_kontrak', 'nik', 'status_karyawan'], 'string', 'max' => 20],
            [['nama'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'no_kontrak' => 'No Kontrak',
            'nik' => 'Nik',
            'nama' => 'Nama',
            'status_karyawan' => 'Status Karyawan',
        ];
    }
    public function getFullinfo($nik){
        $query = new Query();
        $query->from('tbl_karyawan as kar')
                ->join('LEFT JOIN','tbl_section as sec','sec.id_section = kar.section')
                ->join('LEFT JOIN','pekerjaan as sub','sub.kd_kerja = kar.sub_section')
                ->join('LEFT JOIN','tbl_department as dep','dep.id_department = kar.department')
                ->join('LEFT JOIN','tbl_jabatan as jab','jab.id_jabatan= kar.jabatan')
                ->where('kar.nik="'.$nik.'" or kar.nama="'.$nik.'"')
                ->select('kar.nik, kar.nama,jab.jabatan,dep.department,sub.kerja as sub_section,sec.section');
        $command = $query->createCommand();
        $models = $command->queryOne();
        
        return $models;
        
    }

}
