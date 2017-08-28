<?php

namespace app\controllers;

use Yii;
use app\models\TblDtransAtk;
use app\models\TblHtransAtk;
use app\models\DetSatuan;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class AtkmasukController extends Controller {

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
        $query->from('tbl_htrans_atk')
                ->select('*')
                ->orderBy('no_transaksi DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->queryOne();
        $urut = (empty($models)) ? 1 : ((int) substr($models['no_transaksi'], -5)) + 1;
        $kode = 'MATK' . substr('00000' . $urut, -5);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_transaksi DESC";
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
                ->from('tbl_htrans_atk as atk')
                ->join('LEFT JOIN', 'tbl_karyawan as peg', 'atk.kd_karyawan=peg.nik')
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
                    $query->andFilterWhere(['between', 'atk.tgl', $start, $end]);
                } elseif ($key == 'barang') {
                    $nm_barang = $this->namabarang($val);
                    $query->andWhere("no_transaksi in ('$nm_barang')");
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

        foreach ($models as $key => $val) {
            if (!empty($val['kd_karyawan'])) {
                $pegawai = \app\models\TblKaryawan::findOne($val['kd_karyawan']);
                $models[$key]['karyawan'] = (!empty($pegawai)) ? $pegawai->attributes : array();
            }
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionCarikar() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan as kar')->where('kar.nik like "%' . $params['nama'] . '%" OR kar.nama like "%' . $params['nama'] . '%" AND kar.status="Kerja"');
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        $data = [];
        foreach ($models as $key => $val) {
            if ($val['nik'] == "01027" OR $val['nik'] == "01025" OR $val['nik'] == '00909') {
                $data[$key] = $val;
            }
        }
        echo json_encode(array('status' => 1, 'data' => $data));
    }

    public function namabarang($param) {
        $query = new Query;
        $query->select("*")->from("tbl_dtrans_atk")->where(['like', "nm_brng", $param]);
        $command = $query->createCommand();
        $model = $command->queryAll();
        foreach ($model as $value) {
            $data[] = $value['no_trans'];
        }
        return implode(",", $data);
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
//                ->limit($limit)
                ->from('tbl_dtrans_atk as det')
                ->JOIN('LEFT JOIN', 'tbl_htrans_atk as atk', 'det.no_trans = atk.no_transaksi')
                ->join('LEFT JOIN', 'tbl_karyawan as peg', 'atk.kd_karyawan=peg.nik')
                ->where('(atk.tgl >="' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND atk.tgl <="' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '")')
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
 
        $detail = array();
        $query = new Query;
        $query ->select("*")->from('tbl_dtrans_atk')->where(['no_trans' => $id]);
//        $findDet = TblDtransAtk::findAll(['no_trans' => $id]);
        $command = $query->createCommand();
        $models = $command->queryAll();
        if (!empty($models)) {
            foreach ($models as $key => $val) {
                $detail[$key] = $val;
                $atk = \app\models\Tblstockatk::findOne($val['kd_brng']);
                $detail[$key]['barang'] = $atk->attributes;
                $detail[$key]['jumlah_brng'] = $atk->jumlah_brng;
            }
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $detail,'id' => $id), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new TblHtransAtk();
        $model->attributes = $params['form'];

        if ($model->save()) {
            foreach ($params['detail'] as $key => $val) {

                $satuan = DetSatuan::findOne(['kode_atk' => $val['kd_brng'], 'id' => $val['satuan_id']]);
                $jml_awal = $val['jumlah_brng'];
                $jml = $val['jmlh_brng'];
                $hasil = (isset($satuan->konversi)) ? $jml * $satuan->konversi : $jml;
                $stock = \app\models\Tblstockatk::findOne(['kode_brng' => $val['kd_brng']]);


                $detail = new TblDtransAtk();
                $detail->no_trans = $model->no_transaksi;
                $detail->attributes = $val;
                $detail->save();

                if (!empty($stock)) {
//                    $error = ['satuan' => $satuan->attributes, 'awl_brg' => $stock->jumlah_brng, 'hasil' => $hasil, 'penjumlahan' => ($jml_awal + $hasil)];
//                    Yii::error($error);
                    $stock->jumlah_brng = ($jml_awal + $hasil);
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
//            $delDet = TblDtransAtk::deleteAll(['no_trans' => $model->no_transaksi]);
            foreach ($params['detail'] as $key => $val) {
                $satuan = DetSatuan::findOne(['kode_atk' => $val['kd_brng'], 'id' => $val['satuan_id']]);
                $jml_awal = $val['jumlah_brng'];
                $jml = $val['jmlh_brng'];
                $hasil = (isset($satuan->konversi)) ? $jml * $satuan->konversi : $jml;
                //
                $detail = TblDtransAtk::findOne($val['id']);
                $jmlLama = (!empty($detail)) ? $detail->jmlh_brng : 0;

                if (empty($detail)) {
                    $detail = new TblDtransAtk();
                    $detail->no_trans = $model->no_transaksi;
                    $detail->attributes = $val;
                    $detail->save();

                    $stock = \app\models\Tblstockatk::findOne($val['kd_brng']);

                    if (!empty($stock)) { 
                        $stock->jumlah_brng = ($jml_awal + $hasil);
                        $stock->save();
                    }
                    
                } else {
                    $detail->no_trans = $model->no_transaksi;
                    $detail->attributes = $val;
                    $detail->save();

                    $stock = \app\models\Tblstockatk::findOne($val['kd_brng']);
                    $pengurangan = $stock->jumlah_brng - $jmlLama;
                    if (!empty($stock)) {
                        $stock->jumlah_brng =  ($pengurangan + $hasil);
                        $stock->save();
                    }
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
        if (($model = TblHtransAtk::findOne($id)) !== null) {
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
            return $this->render("/exprekap/atkmasuk", ['models' => $models, 'start' => $start, 'end' => $end]);
        } else {
            $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/rekap-atk-masuk.xls';
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
            $baseRow = 5;
            $objPHPExcel->getActiveSheet()->setCellValue('B3', "Tgl Pelaporan :  " . date('d F Y'));
            $path_img = \Yii::$app->params['path'] . "/img/logo.png";
            $objDrawing->setPath($path_img);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setHeight(80);
            $offsetX = 100 - $objDrawing->getWidth();
            $objDrawing->setOffsetX($offsetX);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $no = 1;
            foreach ($models as $r => $arr) {
                if (isset($row))
                    $row++;
                else
                    $row = $baseRow + $r;
//
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $arr['no_transaksi'])
                        ->setCellValue('B' . $row, date('d-m-Y', strtotime($arr['tgl'])))
                        ->setCellValue('C' . $row, $arr['kd_brng'])
                        ->setCellValue('D' . $row, $arr['nm_brng'])
                        ->setCellValue('E' . $row, $arr['jmlh_brng'])
                        ->setCellValue('F' . $row, $arr['nama']);
                $no++;
            }

            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="rekap-atk-masuk.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_htrans_atk')
                ->select("*")
                ->where(['like', 'no_transaksi', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}

?>
