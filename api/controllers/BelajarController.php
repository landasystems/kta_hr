<?php

namespace app\controllers;

use yii\rest\ActiveController;

class BelajarController extends ActiveController {

    public $modelClass = 'app\models\Pegawai';

    public function behaviors() {
        return
                \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
                    'corsFilter' => [
                        'class' => \yii\filters\Cors::className(),
                    ],
        ]);
    }
    
    public function actionCariNip(){
//        $result = Pegawai::findOne()
//              ->where(['nip' => $keyword])
//              ->all();
//    return $result;

        return "aaa";
    }
}

?>
