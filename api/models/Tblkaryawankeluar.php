<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_karyawan_keluar".
 *
 * @property string $nik
 * @property string $no_ktp
 * @property string $nama
 * @property resource $foto
 * @property string $alamat
 * @property string $jenis_kelamin
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $agama
 * @property string $status_nikah
 * @property string $tlp
 * @property string $no_hp
 * @property string $alamat_ktp
 * @property string $tanggal_masuk
 * @property string $tanggal_keluar
 * @property string $email
 * @property string $department
 * @property string $id_section
 * @property string $id_sub
 * @property string $jabatan
 * @property string $kd_level
 * @property string $golongan
 * @property string $nama_pasangan
 * @property string $agama_pasangan
 * @property string $tmp_lhr_pasangan
 * @property string $tgl_lhr_pasangan
 * @property string $jml_anak
 * @property string $nama_anak1
 * @property string $tmp_lhr_anak1
 * @property integer $tgl_lhr_anak1
 * @property string $nama_anak2
 * @property string $tmp_lhr_anak2
 * @property string $tgl_lhr_anak2
 * @property string $nama_anak3
 * @property string $tmp_lhr_anak3
 * @property string $tgl_lhr_anak3
 * @property string $keterangan
 * @property string $status
 * @property string $bagian
 * @property string $tgl_sampai
 * @property string $jam_masuk
 * @property string $jam_keluar
 * @property string $kode_shift
 */
class Tblkaryawankeluar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_karyawan_keluar';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nik'], 'required'],
            [['foto'], 'string'],
            [['tgl_lhr_anak1'], 'integer'],
            [['nik', 'no_ktp', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'status_nikah', 'tlp', 'no_hp', 'alamat_ktp', 'tanggal_masuk', 'tanggal_keluar', 'department', 'id_section', 'id_sub', 'jabatan', 'kd_level', 'golongan', 'nama_pasangan', 'agama_pasangan', 'tmp_lhr_pasangan', 'tgl_lhr_pasangan', 'jml_anak', 'nama_anak1', 'tmp_lhr_anak1', 'nama_anak2', 'tmp_lhr_anak2', 'tgl_lhr_anak2', 'nama_anak3', 'tmp_lhr_anak3', 'tgl_lhr_anak3', 'status', 'bagian', 'tgl_sampai', 'jam_masuk', 'jam_keluar', 'kode_shift'], 'string', 'max' => 20],
            [['alamat', 'keterangan'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nik' => 'Nik',
            'no_ktp' => 'No Ktp',
            'nama' => 'Nama',
            'foto' => 'Foto',
            'alamat' => 'Alamat',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'agama' => 'Agama',
            'status_nikah' => 'Status Nikah',
            'tlp' => 'Tlp',
            'no_hp' => 'No Hp',
            'alamat_ktp' => 'Alamat Ktp',
            'tanggal_masuk' => 'Tanggal Masuk',
            'tanggal_keluar' => 'Tanggal Keluar',
            'email' => 'Email',
            'department' => 'Department',
            'id_section' => 'Id Section',
            'id_sub' => 'Id Sub',
            'jabatan' => 'Jabatan',
            'kd_level' => 'Kd Level',
            'golongan' => 'Golongan',
            'nama_pasangan' => 'Nama Pasangan',
            'agama_pasangan' => 'Agama Pasangan',
            'tmp_lhr_pasangan' => 'Tmp Lhr Pasangan',
            'tgl_lhr_pasangan' => 'Tgl Lhr Pasangan',
            'jml_anak' => 'Jml Anak',
            'nama_anak1' => 'Nama Anak1',
            'tmp_lhr_anak1' => 'Tmp Lhr Anak1',
            'tgl_lhr_anak1' => 'Tgl Lhr Anak1',
            'nama_anak2' => 'Nama Anak2',
            'tmp_lhr_anak2' => 'Tmp Lhr Anak2',
            'tgl_lhr_anak2' => 'Tgl Lhr Anak2',
            'nama_anak3' => 'Nama Anak3',
            'tmp_lhr_anak3' => 'Tmp Lhr Anak3',
            'tgl_lhr_anak3' => 'Tgl Lhr Anak3',
            'keterangan' => 'Keterangan',
            'status' => 'Status',
            'bagian' => 'Bagian',
            'tgl_sampai' => 'Tgl Sampai',
            'jam_masuk' => 'Jam Masuk',
            'jam_keluar' => 'Jam Keluar',
            'kode_shift' => 'Kode Shift',
        ];
    }
}
