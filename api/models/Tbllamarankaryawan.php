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
            [['tgl', 'tanggal_lahir','telp'], 'safe'],
            [['no_lamaran', 'rt', 'rw','telp'], 'string', 'max' => 20],
            [['nama'], 'string', 'max' => 50],
            [['posisi', 'jurusan', 'informal'], 'string', 'max' => 200],
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
            'telp' => 'No. Telp',
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
        ];
    }
}
