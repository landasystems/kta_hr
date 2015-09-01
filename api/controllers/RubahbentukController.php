<?php

namespace app\controllers;

use Yii;
use app\models\RubahBentuk;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class RubahbentukController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'excel' => ['get'],
                ],
            ]
        ];
    }

    public function beforeAction($event) {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } elseif (isset($this->actions['*'])) {
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

    public function actionIndex() {

        $params = $_REQUEST;
        $filter = array();
        $sort = "rb.kd_rubah DESC";
        $offset = 0;
        $limit = 10;
        if (isset($params['limit']))
            $limit = $params['limit'];
        if (isset($params['offset']))
            $offset = $params['offset'];


        if (isset($params['sort'])) {
            $sort = $params['sort'];
            if ($sort == 'no_wo') {
                $sort = 'rb.no_wo';
            }
            if (isset($params['order'])) {
                if ($params['order'] == "false")
                    $sort.=" ASC";
                else
                    $sort.=" DESC";
            }
        }


        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from('rubah_bentuk as rb')
                ->join('Left Join', 'view_wo_spk as vws', 'rb.no_wo = vws.no_wo')
                ->join('Left Join', 'spk', 'vws.no_spk = spk.no_spk')
                ->join('left Join', 'warna', 'vws.kd_warna = warna.kd_warna')
                ->orderBy($sort)
                ->select("*, warna.warna as warna_lama");

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'tgl') {
                    $tgl = explode(" - ", $val);
                    $_SESSION['periode'] = $key;
                    $start = date("Y-m-d", strtotime($tgl[0]));
                    $end = date("Y-m-d", strtotime($tgl[1]));
                    $query->andFilterWhere(['between', 'rb.tgl', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();
        session_start();
        $_SESSION['query'] = $query;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $query->limit(null);
        $query->offset(null);
        $query->select("rb.tgl, vws.no_wo, rb.kd_rubah, vws.merk, vws.tipe, rb.bentuk_baru, vws.no_chassis, vws.nm_customer, spk.jml_unit");
        $command = $query->createCommand();
        $models = $command->queryAll();
        $periode = isset($_SESSION['periode']) ? $_SESSION['periode'] : '-';
        return $this->render("/expretur/rubahbentuk", ['models' => $models, 'periode' => $periode]);
    }

    public function actionView($id) {

        $model = $this->findModel($id);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new RubahBentuk();

        $query = new Query;
        $query->select("kd_rubah")
                ->from("rubah_bentuk as rb")
                ->where("year(tgl) = year('" . $params['tgl'] . "') order by substr(kd_rubah,1,4) DESC");

        $command = $query->createCommand();
        $show = $command->query()->read();

//        echo $show['kd_rubah'];

        if (empty($show)) {
            $kode = "0001/TA III/SKJ/" . date("d/m/y", strtotime($params['tgl']));
        } else {
            $lastKode = substr($show['kd_rubah'], 0, 4) + 1;
            $kode = substr('0000' . $lastKode, -4) . "/TA III/SKJ/" . date("d/m/y", strtotime($params['tgl']));
        }
        \Yii::error($params['tgl']);
        $model->attributes = $params;
        $model->kd_rubah = $kode;
        $model->no_wo = $params['no_wo']['no_wo'];

        if ($model->save()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($id);
        $model->attributes = $params;
        $model->no_wo = $params['no_wo']['no_wo'];

        if ($model->save()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = RubahBentuk::findOne($id)) !== null) {
            return $model;
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Bad request'), JSON_PRETTY_PRINT);
            exit;
        }
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

}

?>
