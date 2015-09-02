<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_lamaran_karyawan".
 *
 * @property string $no_lamaran
 * @property string $tgl
 * @property string $nama
 * @property string $posisi
 * @property string $alamat_jln
 * @property string $rt
 * @property string $rw
 * @property string $kelurahan
 * @property string $kecamatan
 * @property string $kabupaten
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $pendidikan
 * @property string $jurusan
 * @property string $informal
 * @property string $foto
 * @property string $pk_perusahaan1
 * @property string $pk_bagian1
 * @property string $pk_perusahaan2
 * @property string $pk_bagian2
 * @property string $pk_perusahaan3
 * @property string $pk_bagian3
 */
class Tbllamarankaryawan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_lamaran_karyawan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_lamaran'], 'required'],
            [['tgl', 'tanggal_lahir'], 'safe'],
            [['no_lamaran', 'rt', 'rw'], 'string', 'max' => 20],
            [['nama'], 'string', 'max' => 50],
            [['posisi', 'jurusan', 'informal', 'pk_perusahaan1', 'pk_bagian1', 'pk_perusahaan2', 'pk_bagian2', 'pk_perusahaan3', 'pk_bagian3'], 'string', 'max' => 200],
            [['alamat_jln'], 'string', 'max' => 250],
            [['kelurahan', 'kecamatan', 'kabupaten', 'tempat_lahir', 'pendidikan'], 'string', 'max' => 100],
            [['foto'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_lamaran' => 'No Lamaran',
            'tgl' => 'Tgl',
            'nama' => 'Nama',
            'posisi' => 'Posisi',
            'alamat_jln' => 'Alamat Jln',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'kelurahan' => 'Kelurahan',
            'kecamatan' => 'Kecamatan',
            'kabupaten' => 'Kabupaten',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'pendidikan' => 'Pendidikan',
            'jurusan' => 'Jurusan',
            'informal' => 'Informal',
            'foto' => 'Foto',
            'pk_perusahaan1' => 'Pk Perusahaan1',
            'pk_bagian1' => 'Pk Bagian1',
            'pk_perusahaan2' => 'Pk Perusahaan2',
            'pk_bagian2' => 'Pk Bagian2',
            'pk_perusahaan3' => 'Pk Perusahaan3',
            'pk_bagian3' => 'Pk Bagian3',
        ];
    }
}
