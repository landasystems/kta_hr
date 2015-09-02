<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_penilaian_karyawan".
 *
 * @property string $no_penilaian
 * @property string $nik
 * @property string $nama
 * @property string $department
 * @property string $sub_section
 * @property string $periode
 * @property string $i1
 * @property string $i2
 * @property string $i3
 * @property string $i4
 * @property string $i5
 * @property string $i6
 * @property string $i7
 * @property string $i8
 * @property string $ii1
 * @property string $ii2
 * @property string $ii3
 * @property string $iii1
 * @property string $iii2
 * @property string $keterampilan
 * @property string $managerial
 * @property string $kepegawaian
 * @property string $nilai_final
 */
class Tblpenilaiankaryawan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_penilaian_karyawan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_penilaian'], 'required'],
            [['periode'], 'safe'],
            [['no_penilaian', 'nik', 'i1', 'i2', 'i3', 'i4', 'i5', 'i6', 'i7', 'i8', 'ii1', 'ii2', 'ii3', 'iii1', 'iii2', 'keterampilan', 'managerial', 'kepegawaian', 'nilai_final'], 'string', 'max' => 20],
            [['nama', 'department', 'sub_section'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_penilaian' => 'No Penilaian',
            'nik' => 'Nik',
            'nama' => 'Nama',
            'department' => 'Department',
            'sub_section' => 'Sub Section',
            'periode' => 'Periode',
            'i1' => 'I1',
            'i2' => 'I2',
            'i3' => 'I3',
            'i4' => 'I4',
            'i5' => 'I5',
            'i6' => 'I6',
            'i7' => 'I7',
            'i8' => 'I8',
            'ii1' => 'Ii1',
            'ii2' => 'Ii2',
            'ii3' => 'Ii3',
            'iii1' => 'Iii1',
            'iii2' => 'Iii2',
            'keterampilan' => 'Keterampilan',
            'managerial' => 'Managerial',
            'kepegawaian' => 'Kepegawaian',
            'nilai_final' => 'Nilai Final',
        ];
    }
}
