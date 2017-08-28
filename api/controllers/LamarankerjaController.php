<?php

namespace app\controllers;

use Yii;
use app\models\Tbllamarankaryawan;
use app\models\Tbllamarandetails;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class LamarankerjaController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'lapexcel' => ['get'],
                    'rekapperpribadi' => ['post'],
                    'create' => ['post'],
                    'update' => ['post'],
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
        $query->from('tbl_lamaran_karyawan')
                ->select('*')
                ->orderBy('no_lamaran DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->queryOne();
        $urut = (empty($models)) ? 1 : ((int) substr($models['no_lamaran'], -5)) + 1;
        $kode = 'LK' . substr('00000' . $urut, -5);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "no_lamaran DESC";
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
                ->from('tbl_lamaran_karyawan')
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
                    $query->andFilterWhere(['between', 'tbl_lamaran_karyawan.tgl', $start, $end]);
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
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekapperpribadi() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "tgl DESC";
        $offset = 0;
        $limit = 10;

        //create query
        $query = new Query;
        $query->offset($offset)
//                ->limit($limit)
                ->from('tbl_lamaran_karyawan')
                ->where('tgl >="' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND tgl <="' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '"')
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
        $findDet = Tbllamarandetails::findAll(['no_lamaran' => $id]);
        if (!empty($findDet)) {
            foreach ($findDet as $key => $val) {
                $detail[$key] = $val->attributes;
                $detail[$key]['tanggal']['startDate'] = $val->periode_awal;
                $detail[$key]['tanggal']['endDate'] = $val->periode_akhir;
            }
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($detail)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);

        $model = new Tbllamarankaryawan();
        $model->attributes = $params['form'];

        if ($model->save()) {
            foreach ($params['detail'] as $key => $val) {
                $detail = new Tbllamarandetails();
                $detail->attributes = $val;
                if (!empty($detail->perusahaan)) {
                    $detail->no_lamaran = $model->no_lamaran;
                    $detail->periode_awal = date('Y-m-d', strtotime($val['tanggal']['startDate']));
                    $detail->periode_akhir = date('Y-m-d', strtotime($val['tanggal']['endDate']));
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

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);

//        Yii::error($params);
        $model = $this->findModel($id);
        $model->attributes = $params['form'];

        if ($model->save()) {
            $deleteAll = Tbllamarandetails::deleteAll('no_lamaran="' . $params['form']['no_lamaran'] . '"');
            foreach ($params['detail'] as $key => $val) {

                $detail = new Tbllamarandetails();
                $detail->attributes = $val;

                if (!empty($detail->perusahaan)) {
                    $detail->no_lamaran = $model->no_lamaran;
                    $detail->periode_awal = isset($val['tanggal']['startDate']) ? date('Y-m-d', strtotime($val['tanggal']['startDate'])) : '';
                    $detail->periode_akhir = isset($val['tanggal']['endDate']) ? date('Y-m-d', strtotime($val['tanggal']['endDate'])) : '';
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
        if (($model = Tbllamarankaryawan::findOne($id)) !== null) {
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

        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        $render = (!empty($_GET['render'])) ? $_GET['render'] : '';
        return $this->render("/exprekap/" . $render, ['models' => $models, 'params' => $params]);
    }

    public function actionLapexcel() {
        session_start();
        $query = $_SESSION['query'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $key => $value) {
            $models[$key] = $value;
            $detail = Tbllamarandetails::find()->where(['no_lamaran' => $value['no_lamaran']])->all();
            $perusahaan = [];
            $bagian = [];
            foreach ($detail as $ky => $val) {
                $perusahaan[$ky] = $val->perusahaan;
                $bagian[$ky] = $val->bagian;
            }
            $models[$key]['perusahaan'] = (!empty($perusahaan)) ? $perusahaan : [];
            $models[$key]['bagian'] = (!empty($bagian)) ? $bagian : [];
        }
//        Yii::error($models);
        $render = (!empty($_GET['render'])) ? $_GET['render'] : '';
        if ($render == "rekaplamaran") {
            if (isset($_GET['print'])) {
                return $this->render("/exprekap/rekaplamaran", ['models' => $models, 'detail' => $detail]);
            } else {
                $data = array();
                $i = 0;

                $path = \Yii::$app->params['path'] . 'api/templates/rekap-lamaran-krja.xls';
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
                $objPHPExcel->getActiveSheet()->setCellValue('I1', "Tgl Pelaporan :  " . date('d F Y'));
                $path_img = \Yii::$app->params['path'] . "/img/logo.png";
                $objDrawing->setPath($path_img);
                $objDrawing->setCoordinates('A2');
                $objDrawing->setHeight(70);
                $offsetX = 100 - $objDrawing->getWidth();
                $objDrawing->setOffsetX($offsetX);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                $no = 1;
                foreach ($models as $r => $arr) {
                    set_time_limit(40);
                    if (isset($row))
                        $row++;
                    else
                        $row = $baseRow + $r;

                    $detail = Tbllamarandetails::find()->where(['no_lamaran' => $arr['no_lamaran']])->all();
                    $n = 0;
                    $arr_perusahaan = [];
                    foreach ($detail as $ky => $val) {
                        $arr_perusahaan[$ky]['perusahaan'] = $val->perusahaan;
                        $arr_perusahaan[$ky]['bagian'] = $val->bagian;
                    }
//                
                    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                    $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':R' . $row)->getFont()->setSize(9);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':R' . $row)->applyFromArray($background);

                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $no)
                            ->setCellValue('B' . $row, $arr['no_lamaran'])
                            ->setCellValue('C' . $row, date("d-m-Y", strtotime($arr['tgl'])))
                            ->setCellValue('D' . $row, strtoupper($arr['posisi']))
                            ->setCellValue('E' . $row, strtoupper($arr['nama']))
                            ->setCellValue('F' . $row, strtoupper($arr['pendidikan']))
                            ->setCellValue('G' . $row, strtoupper($arr['jurusan']))
                            ->setCellValue('H' . $row, strtoupper($arr['informal']));

                    $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, strtoupper($arr['tempat_lahir']))
                            ->setCellValue('L' . $row, date("d-m-Y", strtotime($arr['tanggal_lahir'])))
                            ->setCellValue('M' . $row, strtoupper($arr['alamat_jln']))
                            ->setCellValue('N' . $row, $arr['rt'])
                            ->setCellValue('O' . $row, $arr['rw'])
                            ->setCellValue('P' . $row, strtoupper($arr['kelurahan']))
                            ->setCellValue('Q' . $row, strtoupper($arr['kecamatan']))
                            ->setCellValue('R' . $row, strtoupper($arr['kabupaten']));

                    if (count($arr_perusahaan) > 1) {
                        $row_awl = $row;
                        $row = $row_awl + (count($arr_perusahaan) - 1);
                        $objPHPExcel->getActiveSheet()->mergeCells("A{$row_awl}:A{$row}")
                                ->mergeCells("B{$row_awl}:B{$row}")
                                ->mergeCells("C{$row_awl}:C{$row}")
                                ->mergeCells("D{$row_awl}:D{$row}")
                                ->mergeCells("E{$row_awl}:E{$row}")
                                ->mergeCells("F{$row_awl}:F{$row}")
                                ->mergeCells("G{$row_awl}:G{$row}")
                                ->mergeCells("H{$row_awl}:H{$row}")
                                ->mergeCells("K{$row_awl}:K{$row}")
                                ->mergeCells("L{$row_awl}:L{$row}")
                                ->mergeCells("M{$row_awl}:M{$row}")
                                ->mergeCells("N{$row_awl}:N{$row}")
                                ->mergeCells("O{$row_awl}:O{$row}")
                                ->mergeCells("P{$row_awl}:P{$row}")
                                ->mergeCells("Q{$row_awl}:Q{$row}")
                                ->mergeCells("R{$row_awl}:R{$row}");
                        $objPHPExcel->getActiveSheet()->getStyle("A{$row_awl}:A{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("B{$row_awl}:B{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("C{$row_awl}:C{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("D{$row_awl}:D{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("E{$row_awl}:E{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("F{$row_awl}:F{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("G{$row_awl}:G{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("H{$row_awl}:H{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("K{$row_awl}:K{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("L{$row_awl}:L{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("M{$row_awl}:M{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("O{$row_awl}:O{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("P{$row_awl}:P{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("Q{$row_awl}:Q{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->getStyle("R{$row_awl}:R{$row}")->applyFromArray($background);
                        $n = 1;
                        $r = 0;
                        foreach ($arr_perusahaan as $key => $val) {
                            $r = $row_awl + $key;
                            $objPHPExcel->getActiveSheet()->getStyle("I{$r}:J{$r}")->getFont()->setSize(9);
                            $objPHPExcel->getActiveSheet()->getStyle("I{$r}:J{$r}")->applyFromArray($background);
                            $objPHPExcel->getActiveSheet()->setCellValue("I" . $r, $n . ". " . $val['perusahaan'])
                                    ->setCellValue("J" . $r, $n . ". " . $val['bagian']);
                            $n++;
                        }
                    } else if (count($arr_perusahaan) == 1) {
                        $objPHPExcel->getActiveSheet()->getStyle("I{$row}:J{$row}")->getFont()->setSize(9);
                        $objPHPExcel->getActiveSheet()->getStyle("I{$row}:J{$row}")->applyFromArray($background);
                        $objPHPExcel->getActiveSheet()->setCellValue("I" . $row, "1. " . $arr_perusahaan[0]['perusahaan'])
                                ->setCellValue("J" . $row, "1. " . $arr_perusahaan[0]['bagian']);
                    }
                    $no++;
                }

                header("Content-type: application/vnd-ms-excel");
                header('Content-Disposition: attachment;filename="rekap-lamaran-kerja.xlsx"');

                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
            }
        } else {
            return $this->render("/exprekap/" . $render, ['models' => $models, 'detail' => $detail]);
        }
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_lamaran_karyawan')
                ->select("*")
                ->where(['like', 'no_lamaran', $params['nama']])
                ->orWhere(['like', 'nama', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}

?>
