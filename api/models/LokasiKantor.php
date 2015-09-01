<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_lokasi_kantor".
 *
 * @property string $id_lokasi_kantor
 * @property string $lokasi_kantor
 */
class LokasiKantor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_lokasi_kantor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_lokasi_kantor'], 'unique'],
            [['lokasi_kantor'], 'safe'],
            [['id_lokasi_kantor', 'lokasi_kantor'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_lokasi_kantor' => 'Id Lokasi Kantor',
            'lokasi_kantor' => 'Lokasi Kantor',
        ];
    }
}
