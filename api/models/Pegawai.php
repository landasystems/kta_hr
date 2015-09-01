<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pegawai".
 *
 * @property integer $id
 * @property string $nip
 * @property string $nip_lama
 * @property string $nama
 * @property string $gelar_depan
 * @property string $gelar_belakang
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $jenis_kelamin
 * @property string $agama
 * @property string $ket_agama
 * @property integer $pendidikan_id
 * @property integer $kedudukan_id
 * @property string $keterangan
 * @property string $tmt_keterangan_kedudukan
 * @property string $status_pernikahan
 * @property string $alamat
 * @property integer $city_id
 * @property string $kode_pos
 * @property string $hp
 * @property string $email
 * @property string $golongan_darah
 * @property string $bpjs
 * @property string $kpe
 * @property string $npwp
 * @property string $no_taspen
 * @property integer $karpeg
 * @property string $foto
 * @property string $tmt_cpns
 * @property string $ket_tmt_cpns
 * @property string $no_sk_cpns
 * @property string $tanggal_sk_cpns
 * @property string $tmt_pns
 * @property string $no_sk_pns
 * @property string $tanggal_sk_pns
 * @property integer $riwayat_pangkat_id
 * @property integer $riwayat_gaji_id
 * @property string $tmt_golongan
 * @property integer $riwayat_jabatan_id
 * @property string $tipe_jabatan
 * @property integer $jabatan_struktural_id
 * @property integer $jabatan_fu_id
 * @property integer $jabatan_ft_id
 * @property string $tmt_pensiun
 * @property string $perubahan_masa_kerja
 * @property string $nomor_kesehatan
 * @property string $tanggal_kesehatan
 * @property string $created
 * @property integer $created_user_id
 * @property string $modified
 * @property integer $modified_user_id
 * @property string $bup
 */
class Pegawai extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'pegawai';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['nip', 'nama', 'jenis_kelamin', 'ket_agama', 'pendidikan_id', 'kedudukan_id', 'keterangan', 'status_pernikahan', 'city_id', 'hp', 'golongan_darah', 'bpjs', 'kpe', 'karpeg', 'riwayat_pangkat_id', 'riwayat_gaji_id', 'tmt_golongan', 'riwayat_jabatan_id', 'jabatan_struktural_id', 'jabatan_fu_id', 'jabatan_ft_id', 'nomor_kesehatan', 'tanggal_kesehatan', 'created_user_id', 'modified_user_id'], 'required'],
            [['tanggal_lahir', 'tmt_keterangan_kedudukan', 'tmt_cpns', 'tanggal_sk_cpns', 'tmt_pns', 'tanggal_sk_pns', 'tmt_golongan', 'tmt_pensiun', 'tanggal_kesehatan', 'created', 'modified'], 'safe'],
            [['jenis_kelamin', 'status_pernikahan', 'alamat', 'ket_tmt_cpns', 'tipe_jabatan', 'perubahan_masa_kerja'], 'string'],
            [['pendidikan_id', 'kedudukan_id', 'city_id', 'karpeg', 'riwayat_pangkat_id', 'riwayat_gaji_id', 'riwayat_jabatan_id', 'jabatan_struktural_id', 'jabatan_fu_id', 'jabatan_ft_id', 'created_user_id', 'modified_user_id'], 'integer'],
            [['nip', 'gelar_depan', 'gelar_belakang', 'email', 'bpjs', 'kpe', 'npwp', 'no_taspen'], 'string', 'max' => 50],
            [['nip_lama', 'hp'], 'string', 'max' => 25],
            [['nama', 'tempat_lahir', 'ket_agama', 'no_sk_cpns', 'no_sk_pns', 'nomor_kesehatan'], 'string', 'max' => 100],
            [['agama'], 'string', 'max' => 30],
            [['keterangan', 'foto'], 'string', 'max' => 225],
            [['kode_pos'], 'string', 'max' => 10],
            [['golongan_darah'], 'string', 'max' => 5],
            [['bup'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nip' => 'Nip',
            'nip_lama' => 'Nip Lama',
            'nama' => 'Nama',
            'gelar_depan' => 'Gelar Depan',
            'gelar_belakang' => 'Gelar Belakang',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'agama' => 'Agama',
            'ket_agama' => 'Ket Agama',
            'pendidikan_id' => 'Pendidikan ID',
            'kedudukan_id' => 'Kedudukan ID',
            'keterangan' => 'Keterangan',
            'tmt_keterangan_kedudukan' => 'Tmt Keterangan Kedudukan',
            'status_pernikahan' => 'Status Pernikahan',
            'alamat' => 'Alamat',
            'city_id' => 'City ID',
            'kode_pos' => 'Kode Pos',
            'hp' => 'Hp',
            'email' => 'Email',
            'golongan_darah' => 'Golongan Darah',
            'bpjs' => 'Bpjs',
            'kpe' => 'Kpe',
            'npwp' => 'Npwp',
            'no_taspen' => 'No Taspen',
            'karpeg' => 'Karpeg',
            'foto' => 'Foto',
            'tmt_cpns' => 'Tmt Cpns',
            'ket_tmt_cpns' => 'Ket Tmt Cpns',
            'no_sk_cpns' => 'No Sk Cpns',
            'tanggal_sk_cpns' => 'Tanggal Sk Cpns',
            'tmt_pns' => 'Tmt Pns',
            'no_sk_pns' => 'No Sk Pns',
            'tanggal_sk_pns' => 'Tanggal Sk Pns',
            'riwayat_pangkat_id' => 'Riwayat Pangkat ID',
            'riwayat_gaji_id' => 'Riwayat Gaji ID',
            'tmt_golongan' => 'Tmt Golongan',
            'riwayat_jabatan_id' => 'Riwayat Jabatan ID',
            'tipe_jabatan' => 'Tipe Jabatan',
            'jabatan_struktural_id' => 'Jabatan Struktural ID',
            'jabatan_fu_id' => 'Jabatan Fu ID',
            'jabatan_ft_id' => 'Jabatan Ft ID',
            'tmt_pensiun' => 'Tmt Pensiun',
            'perubahan_masa_kerja' => 'array(\'tahun\'=>value,\'bulan\'=>value)',
            'nomor_kesehatan' => 'Nomor Kesehatan',
            'tanggal_kesehatan' => 'Tanggal Kesehatan',
            'created' => 'Created',
            'created_user_id' => 'Created User ID',
            'modified' => 'Modified',
            'modified_user_id' => 'Modified User ID',
            'bup' => 'Bup',
        ];
    }

    public function fields() {
        $fields = parent::fields();
        $fields['fotoUrl'] = function ($model) {
            return (empty($this->foto)) ? 'img/noimage.jpg' : 'img/avatar/' . $this->foto;
        };

        return $fields;
    }

}
