<?php

namespace app\controllers;

use Yii;
use app\models\SubSection;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class SubsectionController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'listsection' => ['get'],
                    'list' => ['get'],
                    'cari' => ['get'],
                    'carilist' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'kode' => ['get'],
                ],
            ]
        ];
    }

    public function actionCari() {

        $params = $_REQUEST;
        $query = new Query;
        $query->from('pekerjaan')
                ->select("kd_kerja,kerja")
                ->andWhere(['like', 'kerja', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }
    
    public function actionCarilist() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('pekerjaan')
                ->select("*")
                ->orderBy('kd_kerja ASC')
                ->where(['like','kerja',$params['nama']]);
        
        if (!empty($params['sec'])) {
            $query->andWhere(['id_section' => $params['sec']]);
        }
        
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }
    
    public function actionList() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('pekerjaan')
                ->select("*")
                ->orderBy('kd_kerja ASC');
        
        if(!empty($params['nama'])){
            $query->andWhere(['id_section'=> $params['nama']]);
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
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

    public function actionKode() {
        $query = new Query;
        $query->from('pekerjaan')
                ->select('*')
                ->orderBy('kd_kerja DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->query()->read();
        $kode_mdl = (substr($models['kd_kerja'], -5) + 1);
        $kode = substr('00000' . $kode_mdl, strlen($kode_mdl));
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kode' => 'KR' . $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "pekerjaan.kd_kerja ASC";
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
                ->from('pekerjaan')
                ->join('JOIN', 'tbl_section', 'pekerjaan.id_section = tbl_section.id_section')
                ->orderBy($sort)
                ->select("pekerjaan.*,tbl_section.section");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'id_sections') {
                    $query->andFilterWhere(['like', 'pekerjaan.id_section', $val]);
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

    public function actionView($id) {

        $model = $this->findModel($id);
        $data = $model->attributes;
        $sec = \app\models\Section::find()
                ->where(['id_section' => $model['id_section']])
                ->One();
        $id_section = (isset($sec->id_section)) ? $sec->id_section : '';
        $section = (isset($sec->section)) ? $sec->section : '';
        $data['Sections'] = ['id_section' => $id_section, 'section' => $section];
        
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new SubSection();
        $model->attributes = $params;
        $model->id_section = $params['Sections']['id_section'];


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
        $model->id_section = $params['Sections']['id_section'];

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
        if (($model = SubSection::findOne($id)) !== null) {
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
         return $this->render("/expmaster/subsection", ['models' => $models]);
        } else {
            $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/master-section.xls';
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
                $kd_kerja = (!empty($arr['kd_kerja'])) ? $arr['kd_kerja'] : '';
                $kerja = (!empty($arr['kerja'])) ? $arr['kerja'] : '';
                $section = (!empty($arr['section'])) ? $arr['section'] : '';
                if (isset($row))
                    $row++;
                else
                    $row = $baseRow + $r;
//
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $kd_kerja);
                $objPHPExcel->getActiveSheet()->mergeCells('B' . $row . ':C' . $row)->setCellValue('B' . $row, $kerja);
                $objPHPExcel->getActiveSheet()->mergeCells('D' . $row . ':F' . $row)->setCellValue('D' . $row, $section);
//
            }
//
            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="master-sub-section.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
    }

}

?>
