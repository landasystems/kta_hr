<?php

namespace app\controllers;

use Yii;
use app\models\TblMonitoringService;
use app\models\TblMonitoringDservice;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class MserviceController extends Controller {

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
                    'kode' => ['get'],
                    'cari' => ['get'],
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
        $query->from('tbl_monitoring_service')
                ->select('*')
                ->orderBy('no_mservice DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->queryOne();
        $urut = (empty($models)) ? 1 : ((int) substr($models['no_mservice'], -5)) + 1;
        $kode = 'MSVC' . substr('00000' . $urut, -5);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_mservice DESC";
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
                ->from('tbl_monitoring_service')
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
        $totalItems = $query->count();

        if (!empty($models)) {
            foreach ($models as $key => $val) {
                $file = \app\models\TblKendaraan::find()->where(['nopol' => $val['nopol']])->one();
                $models[$key]['kendaraan'] = (!empty($file)) ? $file->attributes : [];
            }
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekap() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "no_mservice DESC";
        $offset = 0;
        $limit = 10;
        //create query
        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from('tbl_monitoring_service')
                ->where('(tgl >= "' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND tgl <="' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '")')
                ->orderBy($sort)
                ->select("*");

        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['params'] = $params;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $details = [];
        $i = 0;

        foreach ($models as $key => $val) {
            $detail = \app\models\Tblmonitoringdservice::findAll(['no' => $val['no_mservice']]);
            if (!empty($detail)) {
//                Yii::error($detail);
                foreach ($detail as $key2 => $val2) {
                    $details[$i] = $val2->attributes;
                    $i++;
                }
            }
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models, 'detail' => $details, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

//        $model = $this->findModel($id);
        $detail = \app\models\Tblmonitoringdservice::findAll(['no' => $id]);
        $models = [];
        if (!empty($detail)) {
            foreach ($detail as $key => $val) {
                $models[$key] = $val->attributes;
            }
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new TblMonitoringService();
        $model->attributes = $params['form'];
        $total = 0;
        if ($model->save()) {
            foreach ($params['detail'] as $key => $val) {
                $detail = new \app\models\Tblmonitoringdservice();
                $detail->attributes = $val;
                $detail->no = $model->no_mservice;
                $detail->save();
                $total += $detail->biaya;
            }
            $upTotal = $this->findModel($model->no_mservice);
            $upTotal->total_biaya = $total;
            $upTotal->save();
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
        $model->attributes = $params['form'];
        $total = 0;

        if ($model->save()) {
            $deleteAll = \app\models\Tblmonitoringdservice::deleteAll('no="' . $model->no_mservice . '"');
            foreach ($params['detail'] as $key => $val) {
                $detail = new \app\models\Tblmonitoringdservice();
                $detail->attributes = $val;
                $detail->no = $model->no_mservice;
                $detail->save();
                $total += $detail->biaya;
            }
            $upTotal = $this->findModel($model->no_mservice);
            $upTotal->total_biaya = $total;
            $upTotal->save();

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $delDetail = \app\models\Tblmonitoringdservice::deleteAll(['no' => $id]);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = TblMonitoringService::findOne($id)) !== null) {
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
        $params = $_SESSION['params'];
        if (isset($_GET['print'])) {
            return $this->render("/exprekap/moservicekendaraan", ['models' => $models, 'start' => $params['tanggal']['startDate'], 'end' => $params['tanggal']['endDate']]);
        } else {
            $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/monitoring-service.xls';
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
            $baseRow = 7;
            $objPHPExcel->getActiveSheet()->setCellValue('E1', "Periode :  " . date('d F Y', strtotime($params['tanggal']['startDate'])) . ' S/D ' . date('d F Y', strtotime($params['tanggal']['endDate'])));
            $path_img = \Yii::$app->params['path'] . "/img/logo.png";
            $objDrawing->setPath($path_img);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setHeight(70);
            $offsetX = 70 - $objDrawing->getWidth();
            $objDrawing->setOffsetX($offsetX);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $no = 1;
            foreach ($models as $r => $arr) {
                set_time_limit(40);
                if (isset($row))
                    $row++;
                else
                    $row = $baseRow + $r;
//
                $detail = TblMonitoringDservice::findAll(['no' => $arr['no_mservice']]);
//                $n = 0;
                $arr_detail = [];
                foreach ($detail as $ky => $val) {
                    $arr_detail[$ky]['ket'] = $val->ket_service;
                    $arr_detail[$ky]['biaya'] = Yii::$app->landa->rp($val->biaya);
                }
////                
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':L' . $row)->getFont()->setSize(9);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':L' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $no)
                        ->setCellValue('B' . $row, Yii::$app->landa->date2Ind(date('d-M-Y', strtotime($arr['tgl']))))
                        ->setCellValue('C' . $row, $arr['nopol'])
                        ->setCellValue('D' . $row, $arr['merk'])
                        ->setCellValue('E' . $row, $arr['tipe'])
                        ->setCellValue('F' . $row, $arr['model'])
                        ->setCellValue('G' . $row, $arr['warna'])
                        ->setCellValue('H' . $row, $arr['user'])
                        ->setCellValue('I' . $row, Yii::$app->landa->rp($arr['total_biaya']));
//
                if (count($arr_detail) > 1) {
                    $row_awl = $row;
                    $row = $row_awl + (count($arr_detail) - 1);
                    $objPHPExcel->getActiveSheet()->mergeCells("A{$row_awl}:A{$row}")
                            ->mergeCells("B{$row_awl}:B{$row}")
                            ->mergeCells("C{$row_awl}:C{$row}")
                            ->mergeCells("D{$row_awl}:D{$row}")
                            ->mergeCells("E{$row_awl}:E{$row}")
                            ->mergeCells("F{$row_awl}:F{$row}")
                            ->mergeCells("G{$row_awl}:G{$row}")
                            ->mergeCells("H{$row_awl}:H{$row}")
                            ->mergeCells("I{$row_awl}:I{$row}");
                    $objPHPExcel->getActiveSheet()->getStyle("A{$row_awl}:A{$row}")->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->getStyle("B{$row_awl}:B{$row}")->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->getStyle("C{$row_awl}:C{$row}")->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->getStyle("D{$row_awl}:D{$row}")->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->getStyle("E{$row_awl}:E{$row}")->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->getStyle("F{$row_awl}:F{$row}")->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->getStyle("G{$row_awl}:G{$row}")->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->getStyle("I{$row_awl}:I{$row}")->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->getStyle("H{$row_awl}:H{$row}")->applyFromArray($background);

                    $r = 0;
                    foreach ($arr_detail as $key => $val) {
                        $r = $row_awl + $key;
                        $objPHPExcel->getActiveSheet()->mergeCells("J{$r}:K{$r}");
                        $objPHPExcel->getActiveSheet()->getStyle("J{$r}:L{$r}")->getFont()->setSize(9);
                        $objPHPExcel->getActiveSheet()->getStyle("J{$r}:L{$r}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->setCellValue("J" . $r, $val['ket'])
                                ->setCellValue("L" . $r, $val['biaya']);
                    }
                } else if (count($arr_detail) == 1) {
                    $objPHPExcel->getActiveSheet()->mergeCells("J{$row}:K{$row}");
                    $objPHPExcel->getActiveSheet()->getStyle("J{$row}:L{$row}")->getFont()->setSize(9);
                    $objPHPExcel->getActiveSheet()->getStyle("J{$row}:L{$row}")->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->setCellValue("J" . $row, $arr_detail[0]['ket'])
                            ->setCellValue("L" . $row, $arr_detail[0]['biaya']);
                }
                $no++;
            }

            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="rekap-monitoring-service.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_monitoring_service')
                ->select("*")
                ->where(['like', 'no_mservice', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}

?>
