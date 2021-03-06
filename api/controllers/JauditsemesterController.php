<?php

namespace app\controllers;

use Yii;
use app\models\TblHjauditSemester;
use app\models\TblDjauditSemester;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class JauditsemesterController extends Controller {

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
        $query->from('tbl_hjaudit_semester')
                ->select('*')
                ->orderBy('no_audit DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->queryOne();
        $urut = (empty($models)) ? 1 : ((int) substr($models['no_audit'], -8)) + 1;
        $kode = 'AS' . substr('00000000' . $urut, -8);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_audit DESC";
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
                ->from('tbl_hjaudit_semester')
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

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekap() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "no_audit DESC";
        $offset = 0;
        $limit = 10;

        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from('tbl_djaudit_semester as det')
                ->join('LEFT JOIN', 'tbl_hjaudit_semester as jad', 'det.no = jad.no_audit')
                ->join('LEFT JOIN', 'tbl_karyawan as tee', 'det.auditee = tee.nik')
                ->join('LEFT JOIN', 'tbl_karyawan as tor', 'det.auditor = tor.nik')
                ->where('(jad.tgl >="' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND jad.tgl <="' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '") AND jad.audit_ke=' . $params['audit_ke'])
                ->orderBy($sort)
                ->select("jad.no_audit as no_audit, jad.tgl as tgl, jad.jam as jam, det.dept_auditee, det.dept_auditor, tee.nama as auditee, tor.nama as auditor");

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

//        $model = $this->findModel($id);
        $detail = array();
        $findDet = TblDjauditSemester::findAll(['no' => $id]);
        if (!empty($findDet)) {
            foreach ($findDet as $key => $val) {
                $detail[$key] = $val->attributes;
                $auditee = \app\models\Tblkaryawan::findOne($val['auditee']);
                $auditor = \app\models\Tblkaryawan::findOne($val['auditor']);
                $detail[$key]['teraudit'] = $auditee->attributes;
                $detail[$key]['pengaudit'] = $auditor->attributes;
            }
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new TblHjauditSemester();
        $model->attributes = $params['form'];
//        $model->tgl = date('Y-m-d',strtotime($model->tgl));

        if ($model->save()) {
            foreach ($params['detail'] as $key => $val) {
                $detail = new TblDjauditSemester();
                $detail->no = $model->no_audit;
                $detail->attributes = $val;
                $detail->save();
            }
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

        if ($model->save()) {
            $delDet = TblDjauditSemester::deleteAll(['no' => $model->no_audit]);
            foreach ($params['detail'] as $key => $val) {
                $detail = new TblDjauditSemester();
                $detail->no = $model->no_audit;
                $detail->attributes = $val;
                $detail->save();
            }
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
        if (($model = TblHjauditSemester::findOne($id)) !== null) {
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
        if (isset($_GET['print'])) {
            return $this->render("/exprekap/jadwalauditsemester", ['models' => $models, 'start' => $start, 'end' => $end]);
        } else {
            $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/jadwal-audit.xls';
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
            $baseRow = 12;
            $objPHPExcel->getActiveSheet()->setCellValue('A5', "Tgl Pelaporan :  " . date('d F Y'));
            $objPHPExcel->getActiveSheet()->setCellValue('A9', "PERIODE :  " . date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)));
            $path_img = \Yii::$app->params['path'] . "/img/logo.png";
            $objDrawing->setPath($path_img);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setHeight(70);
            $offsetX = 70 - $objDrawing->getWidth();
            $objDrawing->setOffsetX($offsetX);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $no = 1;
            foreach ($models as $r => $arr) {
                $val1 = (!empty($models[$r - 1])) ? $models[$r - 1]['no_audit'] : '';
                $val2 = (!empty($models[$r])) ? $models[$r]['no_audit'] : '';
                if ($val1 == $val2) {
                    $tgl = '';
                    $jam = '';
                } else {
                    $tgl = date('d/m/Y', strtotime($arr['tgl']));
                    $jam = $arr['jam'];
                }
//                set_time_limit(40);
                if (isset($row))
                    $row++;
                else
                    $row = $baseRow + $r;
//                
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row,$tgl);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $jam);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['dept_auditee']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $arr['auditee']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $arr['dept_auditor']);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $arr['auditor']);
                $no++;
            }

            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="jadwal-audit-semester.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
    }

}

?>
