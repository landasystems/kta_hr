<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_karyawan_spd".
 *
 * @property string $no_spd
 * @property string $tgl
 * @property string $nik
 * @property string $nama
 * @property string $department
 * @property string $sub_section
 * @property string $jabatan
 * @property string $transportasi
 * @property string $level_transportasi
 * @property double $biaya_tiket
 * @property double $tarif_penginapan
 * @property double $uang_saku
 * @property double $uang_makan
 * @property string $tgl_berangkat
 * @property string $jam_berangkat
 * @property string $tgl_kembali
 * @property string $tujuan
 * @property string $keperluan
 * @property double $lain
 * @property double $bon_sementara
 * @property string $tbon_sementara
 * @property string $keterangan
 */
class Tblkaryawanspd extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_karyawan_spd';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_spd'], 'required'],
            [['tgl', 'tgl_berangkat', 'tgl_kembali'], 'safe'],
            [['biaya_tiket', 'tarif_penginapan', 'uang_saku', 'uang_makan', 'lain', 'bon_sementara'], 'number'],
            [['no_spd', 'nik', 'transportasi', 'level_transportasi', 'jam_berangkat'], 'string', 'max' => 20],
            [['nama', 'tujuan'], 'string', 'max' => 30],
            [['department', 'sub_section', 'jabatan', 'keperluan', 'keterangan'], 'string', 'max' => 200],
            [['tbon_sementara'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_spd' => 'No Spd',
            'tgl' => 'Tgl',
            'nik' => 'Nik',
            'nama' => 'Nama',
            'department' => 'Department',
            'sub_section' => 'Sub Section',
            'jabatan' => 'Jabatan',
            'transportasi' => 'Transportasi',
            'level_transportasi' => 'Level Transportasi',
            'biaya_tiket' => 'Biaya Tiket',
            'tarif_penginapan' => 'Tarif Penginapan',
            'uang_saku' => 'Uang Saku',
            'uang_makan' => 'Uang Makan',
            'tgl_berangkat' => 'Tgl Berangkat',
            'jam_berangkat' => 'Jam Berangkat',
            'tgl_kembali' => 'Tgl Kembali',
            'tujuan' => 'Tujuan',
            'keperluan' => 'Keperluan',
            'lain' => 'Lain',
            'bon_sementara' => 'Bon Sementara',
            'tbon_sementara' => 'Tbon Sementara',
            'keterangan' => 'Keterangan',
        ];
    }
}
