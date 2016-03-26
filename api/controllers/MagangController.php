<?php

namespace app\controllers;

use Yii;
use app\models\Tblmagang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class MagangController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'rekap' => ['post'],
                    'delete' => ['delete'],
                    'cari' => ['get'],
                    'kode' => ['get']
                ],
            ]
        ];
    }

    public function beforeAction($event) {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } else if (excel(isset($this->actions['*']))) {
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
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "mag.no_magang DESC";
        $offset = 0;
        $limit = 10;

        //limit & offset pagination
        if (isset($params['limit']))
            $limit = $params['limit'];
        if (isset($params['offset']))
            $offset = $params['offset'];

        //sorting
        if (isset($params['sort'])) {
            $sort = $params['sort'];
            if (isset($params['order'])) {
                if ($params['order'] == "false")
                    $sort.=" ASC";
                else
                    $sort.=" DESC";
            }
        }

        //create query
        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from('tbl_magang as mag')
                ->join('LEFT JOIN', 'tbl_bagian as bag', 'mag.bagian = bag.kd_bagian')
                ->orderBy($sort)
                ->select("*");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
//                if ($key == "kat") {
//                    $query->andFilterWhere(['=', $key, $val]);
//                } else {
                $query->andFilterWhere(['like', $key, $val]);
//                }
            }
        }

        session_start();
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $key => $val) {
            if (!empty($val['kd_bagian'])) {
                $bagian = \app\models\TblBagian::findOne($val['kd_bagian']);
                $models[$key]['Bagian'] = $bagian->attributes;
            }
        }
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekap() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "mag.no_magang DESC";
        $offset = 0;
        $limit = 10;
        $starts = date('Y-m-d', strtotime($params['tanggal']['startDate']));
        $ends = date('Y-m-d', strtotime($params['tanggal']['startDate']));
        $siswa_magang = $this->ceksiswa($starts, $ends);

        //create query
        $query = new Query;
        $query->from('tbl_magang as mag')
                ->join('LEFT JOIN', 'tbl_bagian as bag', 'mag.bagian = bag.kd_bagian')
                ->where(['in', 'no_magang', $siswa_magang])
//                ->offset($offset)
//                ->limit($limit)
                ->orderBy($sort)
                ->select("*");

        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['params'] = $params;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function ceksiswa($start, $end) {


        $prd = $this->periode($start, $end);

        $data_ku = [];
        foreach ($prd as $date) {
            $tes = $this->cekid($date);
            foreach ($tes as $set) {
                $data_ku[] = $set;
            }
        }
        $data_a = array_count_values($data_ku);
        $data_akhir = [];
        foreach ($data_a as $k => $value) {
            array_push($data_akhir, $k);
        }

        return $data_akhir;
    }

    public function cekid($tanggal) {
///
        $model = Tblmagang::find()->all();
        $data_mg = [];
        foreach ($model as $model_val) {
            $data_mg[$model_val->no_magang] = [
                "mulai" => $model_val->tgl_mulai,
                "selesai" => $model_val->tgl_selesai
            ];
        }


//periode per user
        $dat = [];
        foreach ($data_mg as $key => $val) {
            $dat[$key][] = $this->periode($val['mulai'], $val['selesai']);
        }

//cek
        $dataku = [];
        foreach ($dat as $keys => $value) {
            foreach ($value as $dt) {
                foreach ($dt as $values) {

                    if ($values == $tanggal) {
                        $dataku[] = $keys;
                    }
                }
            }
        }

        return $dataku;
    }

    public function periode($mulai, $selesai) {
        $begin = new \DateTime($mulai);
        $end = new \DateTime($selesai);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval, $end);
        $data = [];
        foreach ($daterange as $date) {
            $data[] = $date->format("Y-m-d");
        }
        return $data;
    }

    public function actionKode() {
//        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        $query->from('tbl_magang')
                ->select('*')
                ->orderBy('no_magang DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->queryOne();
        $urut = (empty($models)) ? 1 : ((int) substr($models['no_magang'], -5)) + 1;
        $kode = 'MG' . substr('00000' . $urut, -5);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionView($id) {

        $model = $this->findModel($id);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Tblmagang();
        $model->attributes = $params;

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
        if (($model = Tblmagang::findOne($id)) !== null) {
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

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $params = $_SESSION['params'];
        $start = $params['tanggal']['startDate'];
        $end = $params['tanggal']['endDate'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/exprekap/rekapmagang", ['models' => $models, 'start' => $start, 'end' => $end]);
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_magang')
                ->select("*")
                ->where(['like', 'no_magang', $params['nama']])
                ->orWhere(['like', 'nama', $params['nama']])
                ->limit(10);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}

?>
