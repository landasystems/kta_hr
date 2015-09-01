<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property string $kd_cust
 * @property string $nm_customer
 * @property string $kategori
 * @property string $nm_pemilik
 * @property string $market
 * @property string $alamat1
 * @property string $alamat2
 * @property string $provinsi
 * @property string $pulau
 * @property string $telp
 * @property string $fax
 * @property string $hp
 * @property string $email
 * @property string $web
 * @property string $cp
 * @property string $npwp
 * @property string $nppkp
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kd_cust'], 'unique'],
            [['kd_cust'], 'string', 'max' => 8],
            [['nm_customer', 'alamat1', 'alamat2'], 'string', 'max' => 100],
            [['kategori', 'nm_pemilik', 'market', 'provinsi', 'pulau', 'email', 'web', 'cp'], 'string', 'max' => 50],
            [['telp', 'fax', 'hp'], 'string', 'max' => 20],
            [['npwp', 'nppkp'], 'string', 'max' => 25]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kd_cust' => 'Kd Cust',
            'nm_customer' => 'Nm Customer',
            'kategori' => 'Kategori',
            'nm_pemilik' => 'Nm Pemilik',
            'market' => 'Market',
            'alamat1' => 'Alamat1',
            'alamat2' => 'Alamat2',
            'provinsi' => 'Provinsi',
            'pulau' => 'Pulau',
            'telp' => 'Telp',
            'fax' => 'Fax',
            'hp' => 'Hp',
            'email' => 'Email',
            'web' => 'Web',
            'cp' => 'Cp',
            'npwp' => 'Npwp',
            'nppkp' => 'Nppkp',
        ];
    }
}
