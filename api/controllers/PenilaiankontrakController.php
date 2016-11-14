<?php

namespace app\controllers;

use Yii;
use app\models\TblPenilaianKontrak;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class PenilaiankontrakController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['post'],
                    'excel' => ['get'],
                    'excell' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'rekap' => ['post'],
                    'delete' => ['delete'],
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

    public function actionIndex() {
        //init variable
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "id DESC";
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
                ->from('tbl_penilaian_kontrak as pen')
//                ->join('LEFT JOIN','tbl_karyawan as kar','pen.nik = kar.nik')
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
        foreach ($models as $key => $val) {
            $kontrak = \app\models\Tblkaryawankontrak::getFullinfo($val['nik']);
//            $models[$key]

            if (!empty($kontrak)) {
                foreach ($kontrak as $key2 => $val2) {
                    $models[$key][$key2] = (empty($val2)) ? '' : $val2;
                }
            }
            $models[$key]['karyawan'] = (empty($kontrak)) ? array() : $kontrak;
        }
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {
        $params = json_decode(file_get_contents("php://input"), true);

        $query = new Query();
        $query->select("*")
                ->from('tbl_karyawan as kar')
                ->where('kar.nik="' . $id . '"')
                ->join('LEFT JOIN', 'tbl_penilaian_kontrak as pen', 'pen.nik = kar.nik');

        if ($params['tipe'] == 1) {
            $query->andWhere(['pen.nm_kontrak' => "Kontrak 1"]);
        } else if ($params['tipe'] == 2) {
            $query->andWhere(['pen.nm_kontrak' => "Kontrak 2"]);
        }

        $getInstance = $query->createCommand();
        $model = $getInstance->queryOne();
        $this->setHeader(200);

        session_start();
        $_SESSION['excell'] = $model;
        echo json_encode(array('status' => 1, 'data' => $model), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);

        $model = new TblPenilaianKontrak();
        $model->attributes = $params['form'];
        if ($params['kontrak'] == 1)
            $model->nm_kontrak = 'Kontrak 1';
        else
            $model->nm_kontrak = 'Kontrak 2';

        $model->tgl = date('Y-m-d', strtotime($params['form']['tgl']));

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
        $model->attributes = $params['form'];
        if ($params['kontrak'] == 1)
            $model->nm_kontrak = 'Kontrak 1';
        else
            $model->nm_kontrak = 'Kontrak 2';

        $model->tgl = date('Y-m-d', strtotime($params['form']['tgl']));

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
        if (($model = TblPenilaianKontrak::findOne($id)) !== null) {
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

        $penilaian = function($angka) {
            $hasil = '';
            if ($angka == 4) {
                $hasil = 'A';
            } else if ($angka == 3) {
                $hasil = 'B';
            } else if ($angka == 2) {
                $hasil = 'C';
            } else {
                $hasil = 'D';
            }
            return $hasil;
        };
        if (isset($_GET['print'])) {
            return $this->render("/exprekap/rekapnilaikontrak", ['models' => $models, 'penilaian' => $penilaian]);
        } else {
             $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/penilaian-kontrak.xls';
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
            $baseRow = 8;
            $objPHPExcel->getActiveSheet()->setCellValue('A6', "Tgl Pelaporan :  " . date('d F Y'));
            $path_img = \Yii::$app->params['path'] . "/img/logo.png";
            $objDrawing->setPath($path_img);
            $objDrawing->setCoordinates('A2');
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
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':Q' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, date('d-M-y', strtotime($arr['tgl'])));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['nm_kontrak']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $penilaian($arr['mutu_kerja']));
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $penilaian($arr['pengetahuan_teknis']));
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $penilaian($arr['tgjawab_pekerjaan']));
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $penilaian($arr['kerjasama_komunikasi']));
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $penilaian($arr['sikap_kerja']));
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $penilaian($arr['inisiatif']));
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $penilaian($arr['rasa_turut_memiliki']));
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $penilaian($arr['disiplinitas']));
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $penilaian($arr['kepemimpinan']));
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $penilaian($arr['pelaksanaan_managerial']));
                $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $penilaian($arr['problem_solving']));
                $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $penilaian($arr['kehadiran']));
                $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $penilaian($arr['administratif']));
                $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $arr['keterangan']);
                $no++;
            }
            
            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="penilaian-kontrak.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
    }

    public function actionExcell() {
        session_start();
        $models = $_SESSION['excell'];
        echo $models . 'a';
//        $query->offset("");
//        $query->limit("");
//        $command = $query->createCommand();
//        $models = $command->queryAll();
////        $params = $_SESSION['params'];
//
//        $penilaian = function($angka) {
//            $hasil = '';
//            if ($angka == 4) {
//                $hasil = 'A';
//            } else if ($angka == 3) {
//                $hasil = 'B';
//            } else if ($angka == 2) {
//                $hasil = 'C';
//            } else {
//                $hasil = 'D';
//            }
//            return $hasil;
//        };
//
//        return $this->render("/exprekap/rekapnilaikontrak", ['models' => $models, 'penilaian' => $penilaian]);
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_penilaian_kontrak')
                ->select("*")
                ->where(['like', 'nik', $params['nama']])
                ->orWhere(['like', 'nm_kontrak', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionRekap() {
        $params = json_decode(file_get_contents("php://input"), true);
        $sort = "tgl DESC";
        $offset = 0;
        $limit = 10;
        //create query
        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from('tbl_penilaian_kontrak as pen')
                ->join('LEFT JOIN', 'tbl_karyawan as kar', 'pen.nik = kar.nik')
                ->orderBy($sort)
                ->select("*");

        if ($params['tipe'] == 'kelompok') {
            $adWhere = (!empty($params['Section']['id_section'])) ? ' AND kar.section="' . $params['Section']['id_section'] . '"' : '';
            $query->andWhere('(pen.tgl <="' . date('Y-m-d', strtotime($params['tanggal'])) . '")' . $adWhere);
            if (!empty($params['Department']['id_department'])) {
                $query->andWhere(['department' => $params['Department']['id_department']]);
            }
            if (!empty($params['Jabatan']['id_jabatan'])) {
                $query->andWhere(['jabatan' => $params['Jabatan']['id_jabatan']]);
            }
            if (!empty($params['SubSection']['kd_kerja'])) {
                $query->andWhere(['sub_section' => $params['SubSection']['kd_kerja']]);
            }
        } else {
            $query->where('pen.nik="' . $params['karyawan']['nik'] . '"');
        }

        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['params'] = $params;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekapall() {
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "tgl DESC";
        $offset = 0;
        $limit = 10;


        //create query
        $head = array();
        $header = \app\models\Tblkaryawankontrak::findAll();
        if (!empty($header)) {
            foreach ($header as $key => $val) {
                $head[] = $val->attributes;
                $det = TblPenilaianKontrak::find()
                        ->where('')
                        ->all();
            }
        }


        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from('tbl_penilaian_kontrak as pen')
                ->join('LEFT JOIN', 'tbl_karyawan_kontrak as kar', 'pen.nik = kar.no_kontrak')
//                ->where('no_kontrak="' . $params['karyawan']['no_kontrak'] . '"')
                ->orderBy($sort)
                ->select("*");

        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['params'] = $params;

        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $key => $val) {
            $kKontrak = \app\models\Tblkaryawankontrak::findOne($val['nik']);
            $models[$key]['karyawan'] = (empty($kKontrak)) ? array() : $kKontrak->attributes;
        }
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

}

?>
