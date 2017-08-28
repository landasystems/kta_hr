<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "supplier".
 *
 * @property string $kd_supplier
 * @property string $nama_supplier
 * @property string $alamat
 * @property string $telp
 * @property string $cp
 * @property string $email
 * @property string $fax
 * @property string $hp
 * @property string $ket
 * @property string $npwp
 */
class Supplier extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_supplier'], 'string', 'max' => 10],
            [['nama_supplier', 'alamat'], 'string', 'max' => 100],
            [['telp', 'cp', 'email', 'fax', 'hp'], 'string', 'max' => 50],
            [['ket'], 'string', 'max' => 255],
            [['npwp'], 'string', 'max' => 25],
            [['kd_supplier'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_supplier' => 'Kd Supplier',
            'nama_supplier' => 'Nama Supplier',
            'alamat' => 'Alamat',
            'telp' => 'Telp',
            'cp' => 'Cp',
            'email' => 'Email',
            'fax' => 'Fax',
            'hp' => 'Hp',
            'ket' => 'Ket',
            'npwp' => 'Npwp',
        ];
    }
}
