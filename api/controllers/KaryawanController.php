<?php

namespace app\controllers;

use Yii;
use app\models\Tblkaryawan;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class KaryawanController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'excelkeluar' => ['get'],
                    'excelmasuk' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'rekapkeluar' => ['post'],
                    'rekapiso' => ['post'],
                    'rekapmasuk' => ['post'],
                    'delete' => ['delete'],
                    'cari' => ['get'],
                    'carikontrak' => ['get'],
                    'kode' => ['get'],
                    'keluar' => ['post'],
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
        $query->from('tbl_karyawan')
                ->select('*')
                ->orderBy('nik DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->queryOne();
        $urut = (empty($models)) ? 1 : ((int) substr($models['nik'], -5)) + 1;
        $kode = substr('00000' . $urut, -5);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "nik DESC";
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
                ->from('tbl_karyawan k')
                ->join('LEFT JOIN', 'pekerjaan as ss', 'ss.kd_kerja = k.sub_section')
                ->join('LEFT JOIN', 'tbl_section as s', 's.id_section = k.section')
                ->join('LEFT JOIN', 'tbl_department as d', 'd.id_department= k.department')
                ->join('LEFT JOIN', 'tbl_jabatan as j', 'j.id_jabatan = k.jabatan')
                ->orderBy($sort)
                ->select("*");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                $query->andFilterWhere(['like', $key, $val]);
            }
        }

        session_start();
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();
        
        if(!empty($model)){
            foreach($models as $key => $val){
                
            }
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekapkeluar() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "nik DESC";
        $offset = 0;
        $limit = 10;

        //create query
        $query = new Query;
        $query->offset($offset)
//                ->limit($limit)
                ->from('tbl_karyawan')
                ->where('status like "%Keluar%" AND (tgl_keluar_kerja >="' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND tgl_keluar_kerja <="' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '")')
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

    public function actionRekapmasuk() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "nik DESC";
        $offset = 0;
        $limit = 10;

        //create query
        $query = new Query;
        $query->offset($offset)
//                ->limit($limit)
                ->from('v_karyawan_masuk')
                ->where('(tgl_masuk_kerja >="' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND tgl_masuk_kerja <="' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '")')
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
    public function actionRekapiso() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "v.nik DESC";
        $offset = 0;
        $limit = 10;
        $adWhere = (!empty($params['Section']['id_section'])) ? ' AND v.id_section="'.$params['Section']['id_section'].'"' : '';

        //create query
        $query = new Query;
        $query->offset($offset)
//                ->limit($limit)
                ->from('v_karyawan_masuk as v')
                ->join('LEFT JOIN','tbl_karyawan as k','k.nik = v.nik')
                ->where('(v.tgl_masuk_kerja >="' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND v.tgl_masuk_kerja <="' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '")'.$adWhere)
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

    public function actionView($id) {

        $model = $this->findModel($id);
        $department = [];
        $section = [];
        $subSection = [];
        $jabatan = [];
        
        if(!empty($model)){
            
            $dep = \app\models\Department::findOne($model->department);
            $department = (empty($dep))? [] : $dep->attributes;
            $sec = \app\models\Section::findOne($model->section);
            $section = (empty($sec)) ? [] : $sec->attributes;
            $sub= \app\models\SubSection::findOne($model->sub_section);
            $subSection = (empty($sub)) ? [] : $sub->attributes;
            $jab= \app\models\Jabatan::findOne($model->jabatan);
            $jabatan= (empty($jab)) ? [] : $jab->attributes;
           
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'department' => $department,'section'=> $section,'subSection'=> $subSection,'jabatan'=> $jabatan), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Tblkaryawan();
        $model->attributes = $params;
        $model->department = $params['Department']['id_department'];
        $model->section = $params['Section']['id_section'];
        $model->sub_section = $params['SubSection']['kd_kerja'];
        $model->jabatan = $params['Jabatan']['id_jabatan'];
        $model->tgl_keluar_kerja = null;
        $model->alasan_keluar = null;
        $model->status = "Kerja";

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
        $model->department = $params['Department']['id_department'];
        $model->section = $params['Section']['id_section'];
        $model->sub_section = $params['SubSection']['kd_kerja'];
        $model->jabatan = $params['Jabatan']['id_jabatan'];

        if ($model->save()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionKeluar($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($id);
        $model->status = 'Keluar';
        $model->tgl_keluar_kerja = date('Y-m-d', strtotime($params['form']['tgl_keluar_kerja']));
        $model->alasan_keluar = $params['form']['alasan_keluar'];

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
        if (($model = Tblkaryawan::findOne($id)) !== null) {
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
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expmaster/karyawan", ['models' => $models]);
    }
    
    public function actionExcelmasuk() {
        session_start();
        $query = $_SESSION['query'];
        $params = $_SESSION['params'];
        $start = $params['tanggal']['startDate'];
        $end = $params['tanggal']['endDate'];
        $section = (!empty($params['Section']['section'])) ? $params['Section']['section'] : '';
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        $rekap = (!empty($_GET['rekap'])) ? $_GET['rekap'] : '';
        return $this->render("/exprekap/" . $rekap, ['models' => $models, 'start' => $start, 'end' => $end,'section' => $section]);
    }

    public function actionExcelkeluar() {
        session_start();
        $query = $_SESSION['query'];
        $params = $_SESSION['params'];
        $start = $params['tanggal']['startDate'];
        $end = $params['tanggal']['endDate'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/exprekap/karyawankeluar", ['models' => $models, 'start' => $start, 'end' => $end]);
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan as kar')
                ->join('LEFT JOIN', 'tbl_section as sec', 'sec.id_section = kar.section')
                ->join('LEFT JOIN', 'pekerjaan as sub', 'sub.kd_kerja = kar.sub_section')
                ->join('LEFT JOIN', 'tbl_department as dep', 'dep.id_department = kar.department')
                ->join('LEFT JOIN', 'tbl_jabatan as jab', 'jab.id_jabatan= kar.jabatan')
                ->select("*,sub.kerja as subSection,kar.nik, kar.nama,jab.jabatan,dep.department,sub.kerja as sub_section,sec.section")
                ->where('kar.nik like "%' . $params['nama'] . '%" OR kar.nama like "%' . $params['nama'] . '%"')
                ->limit(10);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionCarikontrak() {

        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan as kar')
                ->join('LEFT JOIN', 'tbl_section as sec', 'sec.id_section = kar.section')
                ->join('LEFT JOIN', 'pekerjaan as sub', 'sub.kd_kerja = kar.sub_section')
                ->join('LEFT JOIN', 'tbl_department as dep', 'dep.id_department = kar.department')
                ->join('LEFT JOIN', 'tbl_jabatan as jab', 'jab.id_jabatan= kar.jabatan')
                ->select("kar.nik, kar.nama,jab.jabatan,dep.department,sub.kerja as sub_section,sec.section")
                ->where('kar.nik like "%' . $params['nama'] . '%" OR kar.nama like "%' . $params['nama'] . '%" AND status_karyawan like "%Kontrak%"')
                ->limit(10);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}
?>
