<?php

namespace app\controllers;

use Yii;
use app\models\TblJpenilaian;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class JpenilaianController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'excell' => ['get'],
                    'excelrekap' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'rekap' => ['post'],
                    'delete' => ['delete'],
                    'cari' => ['get'],
                    'kode' => ['get'],
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
        $query->from('tbl_jpenilaian')
                ->select('*')
                ->orderBy('no_jpenilaian DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->queryOne();
        $urut = (empty($models)) ? 1 : ((int) substr($models['no_jpenilaian'], -8)) + 1;
        $kode = 'JP' . substr('00000000' . $urut, -8);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_jpenilaian DESC";
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
                ->from('tbl_jpenilaian')
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
                    $query->andFilterWhere(['between', 'tbl_jpenilaian.tgl_penilaian', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }

        session_start();
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();

        foreach ($models as $key => $val) {
            if (!empty($val['nik_penilai'])) {
                $penilai = \app\models\TblKaryawan::findOne($val['nik_penilai']);
                $models[$key]['bagPenilai'] = (empty($penilai)) ? array() : $penilai->attributes;
            }
            if (!empty($val['nik'])) {
                $ternilai = \app\models\TblKaryawan::findOne($val['nik']);
                $models[$key]['ternilai'] = (empty($ternilai)) ? array() : $ternilai->attributes;
            }
        }
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekap() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $sort = "tgl_penilaian ASC";

        //create query
        $query = new Query;
        $query->from('tbl_jpenilaian')
                ->where('YEAR(tgl_penilaian)="' . $params['tanggal'] . '"')
                ->orderBy($sort)
                ->select("*");

        if ($params['semester'] == 1) {
            $query->andWhere('MONTH(tgl_penilaian) >=1 AND MONTH(tgl_penilaian) <= 6');
        } else {
            $query->andWhere('MONTH(tgl_penilaian) >=7 AND MONTH(tgl_penilaian) <= 12');
        }
        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['params'] = $params;

        $command = $query->createCommand();
        $models = $command->queryAll();

        if (!empty($models)) {
            foreach ($models as $key => $val) {
                $models[$key]['month'] = date('m', strtotime($val['tgl_penilaian']));
                $models[$key]['day'] = date('d', strtotime($val['tgl_penilaian']));
            }
        }

        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new TblJpenilaian();
        $model->attributes = $params;
//        $model->tgl = date('Y-m-d',strtotime($model->tgl));

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
        if (($model = TblJpenilaian::findOne($id)) !== null) {
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

    public function actionExcell() {
        session_start();
        $query = $_SESSION['query'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();

        
        if (isset($_GET['print'])) {
            return $this->render("/exprekap/jpenilaian", ['models' => $models]);
        } else {
            $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/master-jpenilaian.xls';
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $objDrawing = new \PHPExcel_Worksheet_Drawing();
            $objPHPExcel = $objReader->load($path);
//
            $background = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                ),
                'font' => array(
                    'bold' => false,
                ),
            );
//
            $border = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
//
            $baseRow = 4;
            $objPHPExcel->getActiveSheet()->setCellValue('C1', "Tgl Pelaporan :  " . date('d F Y'));
            $path_img = \Yii::$app->params['path'] . "/img/logo.png";
            $objDrawing->setPath($path_img);
            $objDrawing->setCoordinates('A2');
            $objDrawing->setHeight(70);
            $offsetX = 80 - $objDrawing->getWidth();
            $objDrawing->setOffsetX($offsetX);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $no = 1;
            foreach ($models as $r => $arr) {
//                set_time_limit(40);
                if (isset($row))
                    $row++;
                else
                    $row = $baseRow + $r;
                
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $arr['no_jpenilaian']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, date('d-M-y', strtotime($arr['tgl_penilaian'])));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['penilai']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['dep_penilai']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $arr['nama']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $arr['bagian']);
            }
            
            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="jadwal-penilaian.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
    }

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $params = $_SESSION['params'];
        $semester = ($params['semester'] == 1) ? 'I' : 'II';
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();

        if (!empty($models)) {
            foreach ($models as $key => $val) {
                $models[$key]['month'] = date('m', strtotime($val['tgl_penilaian']));
                $models[$key]['day'] = date('d', strtotime($val['tgl_penilaian']));
            }
        }
        return $this->render("/exprekap/jadwalpenilaian", ['models' => $models, 'semester' => $semester]);
        
    }

    public function actionExcelrekap() {
        session_start();
        $query = $_SESSION['query'];
        $params = $_SESSION['params'];
        $semester = ($params['semester'] == 1) ? 'I' : 'II';
//        $end = $params['tanggal']['endDate'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();

        if (!empty($models)) {
            foreach ($models as $key => $val) {
                $models[$key]['month'] = date('m', strtotime($val['tgl_penilaian']));
                $models[$key]['day'] = date('d', strtotime($val['tgl_penilaian']));
            }
        }
        return $this->render("/exprekap/jadwalpenilaian", ['models' => $models, 'params' => $params, 'semester' => $semester]);
    }

}

?>
