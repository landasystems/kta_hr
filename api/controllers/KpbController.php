<?php

namespace app\controllers;

use Yii;
use app\models\Kpb;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class KpbController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'jabkpb' => ['get'],
                    'listbahan' => ['post'],
                    'simpanprint' => ['post'],
                    'bukaprint' => ['post'],
                ],
            ]
        ];
    }

    public function beforeAction($event) {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } elseif (excel(isset($this->actions['*']))) {
            $verbs = $this->actions['*'];
        } else {
            return $event->isValid;
        }
        $verb = Yii::$app->getRequest()->getMethod();
        $allowed = array_map('strtoupper', $verbs);
        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }
        return true;
    }

    public function actionSimpanprint() {
        $param = json_decode(file_get_contents("php://input"), true);
        $cek = Kpb::find()->where(['no_wo' => $param['no_wo'], 'kd_jab' => $param['kd_jab']])->one();
        if (empty($cek)) {
            $kpb = new Kpb;
        } else {
            $kpb = $cek;
        }
        $kpb->no_wo = $param['no_wo'];
        $kpb->kd_jab = $param['kd_jab'];
        $kpb->status = 1;
        $kpb->save();
        echo json_encode(array('status' => 0, 'error_code' => 400, 'msg' => 'Anda telah mencetak kartu pengambilan bahan, hubungi admin untuk mencetak ulang', 'print' => 1), JSON_PRETTY_PRINT);
    }

    public function actionBukaprint() {
        $param = json_decode(file_get_contents("php://input"), true);
        $kpb = Kpb::updateAll(['status' => 0], ['no_wo' => $param['no_wo'], 'kd_jab' => $param['kd_jab']]);

        if ($kpb) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'msg' => '', 'print' => 0), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => 'Terjadi kesalahan'), JSON_PRETTY_PRINT);
        }
    }

    public function actionJabkpb() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('tbl_jabatan as tj')
                ->join('JOIN', 'det_standar_bahan as dsb', 'dsb.kd_jab = tj.id_jabatan')
                ->select("tj.*")
                ->where(['dsb.kd_bom' => $param['key']]);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => array_unique($models, SORT_REGULAR)));
    }

    public function actionListbahan() {
        $param = json_decode(file_get_contents("php://input"), true);

//cek apakah sdh di print
        $cek = Kpb::find()->where(['no_wo' => $param['kd_bom']['no_wo'], 'kd_jab' => $param['kd_jab'], 'status' => 1])->count();

        $query = new Query;
        $query->from('det_standar_bahan as dsb')
                ->join('Join', 'tbl_jabatan as tj', 'dsb.kd_jab = tj.id_jabatan')
                ->join('Join', 'pekerjaan as p', 'tj.krj = p.kd_kerja')
                ->join('Join', 'barang as b', 'dsb.kd_barang = b.kd_barang')
                ->select("dsb.*, b.nm_barang, b.satuan, p.*")
                ->where(['dsb.kd_bom' => $param['kd_bom']['kd_bom'], 'dsb.kd_jab' => $param['kd_jab']]);

        $command = $query->createCommand();
        $models = $command->queryAll();

        if (empty($models)) {
            $list = array();
        } else {
            $list = $models;
        }

        session_start();

        if ($cek == 1 and $_SESSION['user']['id'] != "1") {
            $msg = 'Kartu pengambilan bahan hanya boleh dicetak 1 kali, silahkan menghubungi admin untuk mencetak ulang';
            $print = 1;
        } else {
            $msg = '';
            $print = 0;
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => array_unique($list, SORT_REGULAR), 'msg' => $msg, 'print' => $print));
    }

    private function setHeader($status) {

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . "Nintriva <nintriva.com>");
    }

    private function _getStatusCodeMessage($status) {
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("excel", ['models' => $models]);
    }

}

?>
