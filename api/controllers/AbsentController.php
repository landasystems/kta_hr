<?php

namespace app\controllers;

use Yii;
use app\models\TblAbsent;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class AbsentController extends Controller {

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
                    'delete' => ['delete'],
                    'jenis' => ['get'],
                    'kode' => ['get'],
                    'cari' => ['get'],
                    'print' => ['get'],
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

    public function actionKode() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        $query->from('tbl_absent')
                ->select('*')
                ->orderBy('no_absent DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->queryOne();
        $urut = (empty($models)) ? 1 : ((int) substr($models['no_absent'], -10)) + 1;
        $kode = 'ABS' . substr('0000000000' . $urut, -10);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_absent DESC";
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
                ->from('tbl_absent')
                ->where('ket <> "Hadir" AND nama is not null')
                ->orderBy($sort)
                ->select("*");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'tanggal') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $arrDate = TblAbsent::createdaterange($start, $end);
                    $arrNoAbsent = TblAbsent::ijininrange($arrDate);
                    $query->andWhere(['in', 'no_absent', $arrNoAbsent['no_absent']]);
                    $query->andWhere(['in', 'nik', $arrNoAbsent['nik']]);
//                    if (!empty($arrNoAbsent)) {
//                        foreach ($arrNoAbsent['param'] as $val) {
//                            $query->orWhere('nik=' . $val['nik'] . ' and no_absent="' . $val['no_absent'] . '"');
//                        }
//                    }
                } else if ($key == "ket") {
                    $query->andFilterWhere(['=', $key, $val]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }

        session_start();
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        if (!empty($models)) {
            foreach ($models as $key => $val) {
                $pegawai = \app\models\TblKaryawan::findOne($val['nik']);
                $models[$key]['karyawan'] = (!empty($pegawai)) ? $pegawai->attributes : [];
            }
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {
        $query = new Query;
        $query->from('tbl_absent as abs')
                ->join('LEFT JOIN', 'tbl_karyawan as kar', 'abs.nik=kar.nik')
                ->join('LEFT JOIN', 'pekerjaan as pek', 'kar.sub_section=pek.kd_kerja')
                ->where('no_absent="' . $id . '"')
                ->select("*,abs.ket as ket_absen");
        $command = $query->createCommand();
        $models = $command->queryOne();
        session_start();
        $_SESSION['models'] = $models;

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);


        $date1 = new \DateTime(date('Y-m-d', strtotime($params['datesRange']['startDate'])));
        $date2 = new \DateTime(date('Y-m-d', strtotime($params['datesRange']['endDate'])));

        for ($i = 0; $i <= (int) ($date1->diff($date2)->d); $i++) {
            $model = new TblAbsent();
            if ($i == 0) {
                $model->attributes = $params;
                $model->tgl_pembuatan = date('Y-m-d');
            }
            $model->nik = $params['nik'];
            $model->ket = (!empty($params['ket'] || isset($params['ket']))) ? $params['ket'] : '';
            $model->no_absent = $params['no_absent'];
            $model->tanggal = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($params['datesRange']['startDate'])));
            $model->tanggal_kembali = date('Y-m-d', strtotime($params['datesRange']['endDate']));
//            Yii::error($params['datesRange']['startDate']."===");
//            Yii::error(date('Y-m-d', strtotime('+' . $i . ' days', strtotime($params['datesRange']['startDate']))));
            $model->save();
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

        $date1 = new \DateTime(date('Y-m-d', strtotime($params['datesRange']['startDate'])));
        $date2 = new \DateTime(date('Y-m-d', strtotime($params['datesRange']['endDate'])));
//        Yii::error($params['datesRange']['startDate']."===");
//        Yii::error($params['datesRange']['endDate']);

        $deleteAll = TblAbsent::deleteAll('no_absent="' . $params['no_absent'] . '" AND nama is NULL AND tgl_pembuatan is NULL');
        for ($i = 0; $i <= (int) ($date1->diff($date2)->d); $i++) {
            if ($i == 0) {
                $model = TblAbsent::findOne($id);
                $model->attributes = $params;
                $model->tgl_pembuatan = date('Y-m-d');
            } else {
                $model = new TblAbsent();
            }
            $model->nik = $params['nik'];
            $model->ket = (!empty($params['ket'] || isset($params['ket']))) ? $params['ket'] : '';
            $model->no_absent = $params['no_absent'];
            $model->tanggal = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($params['datesRange']['startDate'])));
            $model->tanggal_kembali = date('Y-m-d', strtotime($params['datesRange']['endDate']));
            $model->save();
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
        $model = TblAbsent::deleteAll(['no_absent' => $id]);
//        $model = $this->findModel($id);
//        $models 

        if ($model) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = TblAbsent::findOne($id)) !== null) {
            return $model;
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Bad request'), JSON_PRETTY_PRINT);
            exit;
        }
    }

    public function actionPrint() {

        session_start();
        $models = $_SESSION['models'];
        return $this->render("/expmaster/absenkeluar", ['models' => $models]);
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
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expmaster/absenkeluar", ['models' => $models]);
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_absent')
                ->select("*")
                ->where(['like', 'no_absent', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}

?>
