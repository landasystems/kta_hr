<?php

namespace app\controllers;

use Yii;
use app\models\Delivery;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class DeliveryController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'customer' => ['post'],
                    'delete' => ['delete'],
                    'no_wo' => ['post'],
                    'det_nowo' => ['get'],
                    'rekap' => ['get'],
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
//        Yii::error($allowed);

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionCustomer() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        $query->from('view_wo_spk')
                ->where('no_wo="' . $params['no_wo'] . '"')
                ->select("kd_cust, nm_customer");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'customer' => $models));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "delivery.id ASC";
        $offset = 0;
        $limit = 10;
        //        Yii::error($params);
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
                ->from('delivery')
                ->join('JOIN', 'customer as cu', 'delivery.kd_cust = cu.kd_cust')
                ->join('JOIN', 'wo_masuk', 'delivery.no_wo = wo_masuk.no_wo')
                ->join('JOIN', 'spk', 'spk.no_spk = wo_masuk.no_spk')
                ->join('JOIN', 'chassis', 'chassis.kd_chassis = spk.kd_chassis')
                ->join('JOIN', 'model', 'model.kd_model = spk.kd_model')
                ->join('JOIN', 'tbl_karyawan', 'tbl_karyawan.nik = spk.nik')
                ->orderBy($sort)
                ->select("delivery.*, chassis.merk as merk, model.model as model, tbl_karyawan.nama as sales,cu.*");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'nama') {
                    $query->andFilterWhere(['like', 'tbl_karyawan.' . $key, $val]);
                } elseif ($key == 'model') {
                    $query->andFilterWhere(['like', 'model.' . $key, $val]);
                } elseif ($key == 'merk') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                }
                $query->andFilterWhere(['like', $key, $val]);
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();
        foreach ($models as $key => $val) {
            $customer = \app\models\Customer::findOne($val['kd_cust']);
            $models[$key]['customer'] = (!empty($customer)) ? $customer->attributes : array();
            $nowo = \app\models\Womasuk::findOne($val['no_wo']);
            $models[$key]['nowo'] = (!empty($nowo)) ? $nowo->attributes : array();
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekap() {
        $params = $_REQUEST;
        $filter = array();
        $sort = "dev.id desc";
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
                ->from('delivery as dev')
                ->join('JOIN', 'customer', 'dev.kd_cust = customer.kd_cust')
                ->join('JOIN', 'view_wo_spk as vws', 'dev.no_wo = vws.no_wo')
                ->join('JOIN', 'spk', 'spk.no_spk = vws.no_spk')
                ->join('JOIN', 'chassis', 'chassis.kd_chassis = spk.kd_chassis')
                ->join('JOIN', 'model', 'model.kd_model = spk.kd_model')
                ->orderBy($sort)
                ->select("dev.*, spk.jml_unit, customer.nm_customer, model.model, chassis.merk, chassis.tipe");
//filter

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                if (isset($key) && $key == 'tgl_delivery') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'dev.tgl_delivery', $start, $end]);
                } elseif ($key == 'no_delivery') {
                    $query->andFilterWhere(['like', 'dev.' . $key, $val]);
                } elseif ($key == 'no_wo') {
                    $query->andFilterWhere(['like', 'dev.' . $key, $val]);
                } elseif ($key == 'nm_customer') {
                    $query->andFilterWhere(['like', 'customer.' . $key, $val]);
                }
            }
        }
        $command = $query->createCommand();
        $models = $command->queryAll();
//        Yii::error($models);
        $totalItems = $query->count();

        $query->limit(null);
        $query->offset(null);
        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['filter'] = $filter;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $query = new Query;
        $query->from('view_wo_spk')
                ->join('LEFT JOIN', 'spk', 'spk.no_spk = view_wo_spk.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->join('JOIN', 'model', 'model.kd_model = view_wo_spk.kd_model')
                ->select("view_wo_spk.*, model.model as model, tk.nama as sales")
                ->where("no_wo='" . $model['no_wo'] . "'");

        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $key => $val) {
            $nowo = (isset($val['no_wo'])) ? $val['no_wo'] : '';
            $merk = (isset($val['merk'])) ? $val['merk'] : '';
            $jancok = (isset($val['model'])) ? $val['model'] : '';
            $cok = (isset($val['sales'])) ? $val['sales'] : '';


            $model['no_wo'] = ['no_wo' => $nowo, 'merk' => $merk, 'model' => $jancok, 'sales' => $cok];
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        \Yii::error($params);
        $model = new Delivery();
        $model->attributes = $params;
        if ($model->tujuan == "customer") {
            $model->status = 1;
        } else {
            $model->status = 0;
        }

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
        \Yii::error($params);
        $model = $this->findModel($id);
        $model->attributes = $params;
//        $model->no_wo = $params['no_wo']['no_wo'];
        if ($model->tujuan == "customer") {
            $model->status = 1;
        } else {
            $model->status = 0;
        }

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
        if (($model = Delivery::findOne($id)) !== null) {
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
        $filter = $_SESSION['filter'];

        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/deliveryunit", ['models' => $models, 'filter' => $filter]);
    }

}

?>