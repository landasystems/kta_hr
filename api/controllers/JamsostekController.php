<?php

namespace app\controllers;

use Yii;
use app\models\Tbljamsostek;
use app\models\TblJamsostekDet;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class JamsostekController extends Controller {

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
        $query->from('tbl_jamsostek')
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
        $sort = "atk.nik DESC";
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
                ->from('tbl_jamsostek as atk')
                ->join('LEFT JOIN', 'tbl_karyawan as peg', 'atk.nik=peg.nik')
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

        foreach ($models as $key => $val) {
            if (!empty($val['kd_karyawan'])) {
                $pegawai = \app\models\Tblkaryawan::findOne($val['kd_karyawan']);
                $models[$key]['karyawan'] = $pegawai->attributes;
            } else {
                $models[$key]['karyawan'] = [];
            }
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
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
                ->JOIN('LEFT JOIN', 'tbl_jamsostek as atk', 'det.no_trans = atk.no_transaksi')
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

//        $model = $this->findModel($id);
        $detail = array();
//        $findDet = \app\models\Tbllamarandeatils::findAll(['no_lamaran' => $id]);
        $findDet = TblJamsostekDet::findAll(['nik' => $id]);
        if (!empty($findDet)) {
            foreach ($findDet as $key => $val) {
                $detail[$key] = $val->attributes;
//                $detail[$key]['tanggal']['startDate'] = $val->periode_awal;
//                $detail[$key]['tanggal']['endDate'] = $val->periode_akhir;
            }
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);

        $model = new Tbljamsostek();
        $model->attributes = $params['form'];

        if ($model->save()) {
            foreach ($params['detail'] as $key => $val) {
                $detail = new TblJamsostekDet();
                $detail->attributes = $val;
                $detail->nik = $model->nik;
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
            $deleteAll = TblJamsostekDet::deleteAll('nik="' . $params['form']['nik'] . '"');
            foreach ($params['detail'] as $key => $val) {
                $detail = new TblJamsostekDet();
                $detail->attributes = $val;
                $detail->nik = $model->nik;
                if (!empty($detail->nn)) {
                    $detail->save();
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
        if (($model = Tbljamsostek::findOne($id)) !== null) {
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
        foreach ($models as $key => $value) {
            $models[$key] = $value;
            $detail = TblJamsostekDet::find()->where(['nik' => $value['nik']])->all();
            if (!empty($detail)) {
                foreach ($detail as $ky => $val) {
                    $details[] = $val->attributes;
                }
            } else {
                $details = [];
            }
        }

        if (isset($_GET['print'])) {
            return $this->render("/expmaster/jamsostek", ['models' => $models, 'details' => $details]);
        } else {
            $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/master-bpjs.xls';
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
            $objPHPExcel->getActiveSheet()->setCellValue('E1', "Tgl Pelaporan :  " . date('d F Y'));
            $path_img = \Yii::$app->params['path'] . "/img/logo.png";
            $objDrawing->setPath($path_img);
            $objDrawing->setCoordinates('A2');
            $objDrawing->setHeight(70);
            $offsetX = 80 - $objDrawing->getWidth();
            $objDrawing->setOffsetX($offsetX);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $no = 0;
            foreach ($models as $r => $arr) {
                $status = ($arr['status_pernikahan'] == 'Belum Kawin') ? "Belum" : "Kawin";
                if (isset($row))
                    $row++;
                else
                    $row = $baseRow + $r;
                 $detail = TblJamsostekDet::find()->where(['nik' => $arr['nik']])->all();
                 $arr_detail=[];
                 foreach ($detail as $ky => $val) {
                        $arr_detail[$ky]['nn'] = $val->nn;
                        $arr_detail[$ky]['kpj'] = $val->kpj;
                        $arr_detail[$ky]['p_kepesertaan'] = date ('m-Y', strtotime ($val->periode_kepesertaan));
                        $arr_detail[$ky]['jht'] = $val->jht;
                        $arr_detail[$ky]['jkm'] = $val->jkm;
                        $arr_detail[$ky]['jkk'] = $val->jkk;
                        $arr_detail[$ky]['kpj'] = $val->kpj;
                        $arr_detail[$ky]['pensiun'] = $val->pensiun;
                        $arr_detail[$ky]['iuran'] = $val->iuran;
                        $arr_detail[$ky]['keterangan'] = $val->keterangan;
                    }
                    
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':M' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $arr['nik']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $arr['nama']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $status);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['upah_tetap']);
                
                 if (count($arr_detail) > 1) {
                        $row_awl = $row;
                        $row = $row_awl + (count($arr_detail) - 1);
                        $objPHPExcel->getActiveSheet()->mergeCells("A{$row_awl}:A{$row}")
                                ->mergeCells("B{$row_awl}:B{$row}")
                                ->mergeCells("C{$row_awl}:C{$row}")
                                ->mergeCells("D{$row_awl}:D{$row}");
                                
                        $objPHPExcel->getActiveSheet()->getStyle("A{$row_awl}:A{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("B{$row_awl}:B{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("C{$row_awl}:C{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("D{$row_awl}:D{$row}")->applyFromArray($background);
                        $n = 1;
                        $r = 0;
                        foreach ($arr_detail as $key => $val) {
                            $r = $row_awl + $key;
                            $objPHPExcel->getActiveSheet()->getStyle("E{$r}:M{$r}")->getFont()->setSize(9);
                            $objPHPExcel->getActiveSheet()->getStyle("E{$r}:M{$r}")->applyFromArray($background);
                            $objPHPExcel->getActiveSheet()->setCellValue("E" . $r, $val['nn'])
                                    ->setCellValue("F" . $r,  $val['kpj'])
                                    ->setCellValue("G" . $r, date ('m-Y', strtotime ($val['p_kepesertaan'])))
                                    ->setCellValue("H" . $r, $val['jht'])
                                    ->setCellValue("I" . $r, $val['jkm'])
                                    ->setCellValue("J" . $r, $val['jkk'])
                                    ->setCellValue("K" . $r, $val['pensiun'])
                                    ->setCellValue("L" . $r, $val['iuran'])
                                    ->setCellValue("M" . $r, $val['keterangan']);
                            $n++;
                        }
                    } else if (count($arr_detail) == 1) {
                        $objPHPExcel->getActiveSheet()->getStyle("E{$row}:M{$row}")->getFont()->setSize(9);
                        $objPHPExcel->getActiveSheet()->getStyle("E{$row}:M{$row}")->applyFromArray($background);
                         $objPHPExcel->getActiveSheet()->setCellValue("E" . $row, $arr_detail[0]['nn'])
                                    ->setCellValue("F" . $row,  $arr_detail[0]['kpj'])
                                    ->setCellValue("G" . $row, date ('m-Y', strtotime ($arr_detail[0]['p_kepesertaan'])))
                                    ->setCellValue("H" . $row, $arr_detail[0]['jht'])
                                    ->setCellValue("I" . $row, $arr_detail[0]['jkm'])
                                    ->setCellValue("J" . $row, $arr_detail[0]['jkk'])
                                    ->setCellValue("K" . $row, $arr_detail[0]['pensiun'])
                                    ->setCellValue("L" . $row, $arr_detail[0]['iuran'])
                                    ->setCellValue("M" . $row, $arr_detail[0]['keterangan']);
                    }
                
            }
//
            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="master-bpjs.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_jamsostek')
                ->select("*")
                ->where(['like', 'no_transaksi', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}

?>
