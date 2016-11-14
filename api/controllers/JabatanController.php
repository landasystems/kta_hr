<?php

namespace app\controllers;

use Yii;
use app\models\Jabatan;
use app\models\AbsensiEmp;
use app\models\AbsensiEttLog;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class JabatanController extends Controller {

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
                    'kode' => ['get'],
                    'list' => ['get'],
                    'carilist' => ['get'],
                    'listkaryawan' => ['get'],
                    'listkaryawanabsent' => ['get'],
                    'listkaryawansales' => ['get'],
                    'cari' => ['get'],
                ],
            ]
        ];
    }

    public function beforeAction($event) {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } elseif (excel(isset($this->actions['*']))) {
            $verbs = $this->actions['*'];
        } else {
            return $event->isValid;
        }
        $verb = Yii::$app->getRequest()->getMethod();
        $allowed = array_map('strtoupper', $verbs);
//        Yii::error($allowed);

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionCarilist() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_jabatan')
                ->select("*")
                ->orderBy('id_jabatan ASC')
                ->where(['like', 'jabatan', $params['nama']]);
        if (!empty($params['subsec'])) {
            $query->andWhere(['krj' => $params['subsec']]);
        }

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionList() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_jabatan')
                ->select("*")
                ->orderBy('id_jabatan ASC');
        if (!empty($params['nama'])) {
            $query->andWhere(['krj' => $params['nama']]);
        }

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionListkaryawanabsent() {

        $absen = AbsensiEttLog::find()
                ->joinWith('karyawan')
                ->select("emp.first_name, emp.pin, date(scan_date) as scan_date")
                ->where('date(scan_date) = "' . date("Y-m-d") . '"')
                ->limit(100)
                ->all();

        $sudahAbsen = array();
        foreach ($absen as $key => $val) {
            $sudahAbsen[] = $val['karyawan']['nik'];
        }

        $param = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan')
                ->select("nik, nama")
                ->where('nama like "%' . $param['nama'] . '%"')
                ->andWhere(['nik' => $sudahAbsen]);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionListkaryawan() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan')
                ->select("nik, nama")
                ->where('nama like "%' . $param['nama'] . '%"');

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionListkaryawansales() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan')
                ->select("nik, nama")
                ->where('nama like "%' . $param['nama'] . '%"')
                ->andWhere(['department' => 'DPRT005']);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionCari() {
        $param = $_REQUEST;
        $query = new Query;
        $query->from('tbl_jabatan')
                ->select("*")
                ->where('jabatan like "%' . $param['nama'] . '%"')
                ->orWhere(['like', 'id_jabatan', $param['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionKode() {
        $query = new Query;
        $query->from('tbl_jabatan')
                ->select('*')
                ->orderBy('id_jabatan DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();
        $kode_mdl = (substr($models['id_jabatan'], -3) + 1);
        $kode = substr('000' . $kode_mdl, strlen($kode_mdl));
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => 'JBTN' . $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "tbl_jabatan.id_jabatan ASC";
        $offset = 0;
        $limit = 10;
        //        Yii::error($params);
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
                ->from('tbl_jabatan')
                ->join('JOIN', 'pekerjaan', 'tbl_jabatan.krj = pekerjaan.kd_kerja')
                ->orderBy($sort)
                ->select("tbl_jabatan.*,pekerjaan.kerja");

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

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $data = $model->attributes;
        $subsec = \app\models\SubSection::find()
                ->where(['kd_kerja' => $model['krj']])
                ->One();
        $kd_kerja = (isset($subsec->kd_kerja)) ? $subsec->kd_kerja : '';
        $kerja = (isset($subsec->kerja)) ? $subsec->kerja : '';
        $data['subSection'] = ['kd_kerja' => $kd_kerja, 'kerja' => $kerja];

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Jabatan();
        $model->attributes = $params;
        $model->krj = $params['subSection']['kd_kerja'];


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
        $model->krj = $params['subSection']['kd_kerja'];

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
        if (($model = Jabatan::findOne($id)) !== null) {
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
        
        
        if (isset($_GET['print'])) {
            return $this->render("/expmaster/jabatan", ['models' => $models]);
        } else {
            $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/master-jabatan.xls';
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
//
            $objPHPExcel->getActiveSheet()->setCellValue('C1', " Tgl Pelaporan :  " . date("d F Y"));
            $path_img = \Yii::$app->params['path'] . "/img/logo.png";
            $objDrawing->setPath($path_img);
            $objDrawing->setCoordinates('A2');
            $objDrawing->setHeight(70);
            $offsetX = 100 - $objDrawing->getWidth();
            $objDrawing->setOffsetX($offsetX);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            foreach ($models as $r => $arr) {
                $id_jabatan = (!empty($arr['id_jabatan'])) ? $arr['id_jabatan'] : '';
                $jabatan = (!empty($arr['jabatan'])) ? $arr['jabatan'] : '';
                $kerja = (!empty($arr['kerja'])) ? $arr['kerja'] : '';
                if (isset($row))
                    $row++;
                else
                    $row = $baseRow + $r;
//
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $id_jabatan);
                $objPHPExcel->getActiveSheet()->mergeCells('B' . $row . ':C' . $row)->setCellValue('B' . $row, $jabatan);
                $objPHPExcel->getActiveSheet()->mergeCells('D' . $row . ':F' . $row)->setCellValue('D' . $row, $kerja);
//
            }
//
            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="master-jabatan.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
    }

}

?>
