<?php

namespace app\controllers;

use Yii;
use app\models\TblDtransApdMasuk;
use app\models\TblHtransApdMasuk;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class PemasukanapdController extends Controller {

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
                    'jenis' => ['get'],
                    'kode' => ['get'],
                    'cari' => ['get'],
                    'carikar' => ['get'],
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
        $query->from('tbl_htrans_apd_masuk')
                ->select('*')
                ->orderBy('no_transaksi DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->queryOne();
        $urut = (empty($models)) ? 1 : ((int) substr($models['no_transaksi'], -5)) + 1;
        $kode = 'KAPD' . substr('00000' . $urut, -5);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_transaksi ASC";
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
                ->from('tbl_htrans_apd_masuk as apd')
                ->join('LEFT JOIN', 'tbl_karyawan as peg', 'apd.nik_karyawan=peg.nik')
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

//        foreach ($models as $key => $val) {
//            if (!empty($val['nik_karyawan'])) {
//                $pegawai = \app\models\Tblkaryawan::findOne($val['nik_karyawan']);
//                $models[$key]['karyawan'] = (!empty($pegawai)) ? $pegawai->attributes : array();
//            }
//        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }
    
    public function actionCarikar()
    {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan as kar')->where('kar.nik like "%' . $params['nama'] . '%" OR kar.nama like "%' . $params['nama'] . '%" AND kar.status="Kerja" AND department="DPRT013"');
                 $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        
        echo json_encode(array('status' => 1, 'data' => $models));
        
    }

    public function actionRekap() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "no_transaksi DESC";
        $offset = 0;
        $limit = 10;

        //create query
        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from('tbl_dtrans_apd_masuk as det')
                ->join('LEFT JOIN', 'tbl_htrans_apd_masuk as trans', 'det.no_trans = trans.no_transaksi')
                ->where('(trans.tgl >="' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND trans.tgl <="' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '")')
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

//        $model = $this->findModel($id);
        $detail = array();
        $findDet = TblDtransApdMasuk::findAll(['no_trans' => $id]);
        if (!empty($findDet)) {
            foreach ($findDet as $key => $val) {
                $detail[$key] = $val->attributes;
                $apd = \app\models\Tblstockapd::findOne($val->kd_apd);
                $detail[$key]['apd'] = $apd->attributes;
                $detail[$key]['jumlah_apd'] = $apd->jumlah_apd;
            }
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new TblHtransApdMasuk();
        $model->attributes = $params['form'];

        if ($model->save()) {
            foreach ($params['detail'] as $key => $val) {
                $detail = new TblDtransApdMasuk();
                $detail->no_trans = $model->no_transaksi;
                $detail->attributes = $val;
                $detail->save();

                $stock = \app\models\Tblstockapd::findOne($detail->kd_apd);
                if (!empty($stock)) {
                    $stock->jumlah_apd = ($stock->jumlah_apd + $detail->jmlh_apd);
                    $stock->save();
                }
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
//            $delDet = TblDtransApdMasuk::deleteAll(['no_trans' => $model->no_transaksi]);
            foreach ($params['detail'] as $key => $val) {
                $detail = TblDtransApdMasuk::findOne($val['id']);
                $jmlLama = (!empty($detail)) ? $detail->jmlh_brng : 0;
                if (empty($detail))
                    $detail = new TblDtransApdMasuk();
                $detail->no_trans = $model->no_transaksi;
                $detail->attributes = $val;
                $detail->save();

                $stock = \app\models\Tblstockapd::findOne($detail->kd_apd);
                if (!empty($stock)) {
                    $stock->jumlah_apd = $stock->jumlah_apd - $jmlLama + $detail->jmlh_apd;
                    $stock->save();
                }
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
        if (($model = TblHtransApdMasuk::findOne($id)) !== null) {
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
           return $this->render("/exprekap/pemasukanapd", ['models' => $models, 'start' => $start, 'end' => $end]);
    } else {
            $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/rekap-pemasukan-apd.xls';
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
            $baseRow = 6;
            $objPHPExcel->getActiveSheet()->setCellValue('B4', "Tgl Pelaporan :  " . date('d F Y'));
            $path_img = \Yii::$app->params['path'] . "/img/logo.png";
            $objDrawing->setPath($path_img);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setHeight(70);
            $offsetX = 100 - $objDrawing->getWidth();
            $objDrawing->setOffsetX($offsetX);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $no = 1;
            foreach ($models as $r => $arr) {

//                set_time_limit(40);
                if (isset($row))
                    $row++;
                else
                    $row = $baseRow + $r;
//                
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':E' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, date("d-m-Y", strtotime($arr['tgl'])));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['kd_apd']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['nm_apd']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $arr['jmlh_apd']);
                $no++;
            }

            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="rekap-pemasukan-apd.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_htrans_apd_masuk')
                ->select("*")
                ->where(['like', 'no_transaksi', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}

?>
