<?php

namespace app\controllers;

use Yii;
use app\models\Tblstockatk;
use app\models\TblHtransAtk;
use app\models\TblHtransAtkKeluar;
use app\models\TblDtransAtkKeluar;
use app\models\TblDtransAtk;
use app\models\DetSatuan;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class BarangatkController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['post'],
                    'excel' => ['get'],
                    'excelstock' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'listsatuan' => ['post'],
                    'delete' => ['delete'],
                    'jenis' => ['get'],
                    'kode' => ['get'],
                    'rekapstock' => ['post'],
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
     public function actionListsatuan() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        $query->from('det_satuan')
                ->select("*")
                ->where([$params['tabel'] => $params['value']])
                ->orderBy("konversi ASC");
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionKode() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        $query->from('tbl_stock_atk')
                ->select('*')
                ->orderBy('kode_brng DESC')
                ->limit(1);

        $command = $query->createCommand();
        $models = $command->queryOne();
        $urut = (empty($models)) ? 1 : ((int) substr($models['kode_brng'], -3)) + 1;
        $kode = 'ATK' . substr('000' . $urut, -3);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "kode_brng ASC";
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
                    $sort.=" DESC";
                else
                    $sort.=" ASC";
            }
        }

        //create query
        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from('tbl_stock_atk')
                ->orderBy($sort)
                ->select("*");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == "kat") {
                    $query->andFilterWhere(['=', $key, $val]);
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

    public function actionRekapstock() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $sort = "kode_brng ASC";

        //create query
        $query = new Query;
        $query->from('tbl_stock_atk')
                ->orderBy($sort)
                ->select("*");

        session_start();
        $_SESSION['params'] = $params;
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();

        if (!empty($models)) {
            $saldoAwal = 0;
            $saldoAkhir = 0;
            $masuk = 0;
            $keluar = 0;
            foreach ($models as $key => $val) {
                $atkMasuk = TblDtransAtk::find()
                        ->select('sum(jmlh_brng) as stockmasuk')
                        ->join('JOIN', 'tbl_htrans_atk', 'tbl_htrans_atk.no_transaksi = tbl_dtrans_atk.no_trans')
                        ->where('tbl_htrans_atk.tgl >= "' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND tbl_htrans_atk.tgl <= "' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '" and tbl_dtrans_atk.kd_brng="' . $val['kode_brng'] . '"')
                        ->one();
                $atkKeluar = TblDtransAtkKeluar::find()
                        ->select('sum(jmlh_brng) as stockkeluar')
                        ->join('JOIN', 'tbl_htrans_atk_keluar', 'tbl_htrans_atk_keluar.no_transaksi = tbl_dtrans_atk_keluar.no_trans')
                        ->where('tbl_htrans_atk_keluar.tgl >= "' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND tbl_htrans_atk_keluar.tgl <= "' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '" and tbl_dtrans_atk_keluar.kd_brng="' . $val['kode_brng'] . '"')
                        ->one();
//                Yii::error($atkKeluar->stockkeluar);
                $saldoMasuk = TblDtransAtk::find()
                        ->select('sum(jmlh_brng) as saldomasuk')
                        ->join('JOIN', 'tbl_htrans_atk', 'tbl_htrans_atk.no_transaksi = tbl_dtrans_atk.no_trans')
                        ->where('tbl_htrans_atk.tgl >= "' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" and tbl_dtrans_atk.kd_brng="' . $val['kode_brng'] . '"')
                        ->one();
                $saldoKeluar = TblDtransAtkKeluar::find()
                        ->select('sum(jmlh_brng) as saldokeluar')
                        ->join('JOIN', 'tbl_htrans_atk_keluar', 'tbl_htrans_atk_keluar.no_transaksi = tbl_dtrans_atk_keluar.no_trans')
                        ->where('tbl_htrans_atk_keluar.tgl >= "' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" and tbl_dtrans_atk_keluar.kd_brng="' . $val['kode_brng'] . '"')
                        ->one();
                $jmlBarang = (empty($val['jumlah_brng'])) ? 0 : $val['jumlah_brng'];

                $saldoAwal = $jmlBarang + $saldoKeluar->saldokeluar - $saldoMasuk->saldomasuk;


                $masuk = (empty($atkMasuk->stockmasuk)) ? 0 : $atkMasuk->stockmasuk;
                $keluar = (empty($atkKeluar->stockkeluar)) ? 0 : $atkKeluar->stockkeluar;

                $saldoAkhir = $saldoAwal + $masuk - $keluar;
                $models[$key]['masuk'] = $masuk;
                $models[$key]['keluar'] = $keluar;
                $models[$key]['saldo_awal'] = $saldoAwal;
                $models[$key]['saldo_akhir'] = $saldoAkhir;
            }
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionView() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($params['kode']);
        $model_all = DetSatuan::find()->where(['kode_atk' => $model->kode_brng])->all();
        $detail = [];
        foreach ($model_all as $key => $val) {
            $detail[$key] = $val->attributes;
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes), 'detail' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Tblstockatk();
        $model->attributes = $params['form'];
        $model->merk = $params['form']['merk'];
        $model->keterangan = $params['form']['keterangan'];

        if ($model->save()) {
            foreach ($params['detail'] as $key => $val) {
                $det = new DetSatuan();
                $det->nama = $val['nama'];
                $det->konversi = $val['konversi'];
                $det->kode_atk = $model->kode_brng;
                $det->save();
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
        $model->merk = $params['form']['merk'];
        $model->keterangan = $params['form']['keterangan'];

        if ($model->save()) {
            $list_id  = [];
            foreach ($params['detail'] as $key => $val) {
                if (isset($val['id'])) {
                    $det = DetSatuan::findOne(['id' => $val['id']]);
                } else {
                    $det = new DetSatuan();
                }
                $det->nama = $val['nama'];
                $det->konversi = $val['konversi'];
                $det->kode_atk = $model->kode_brng;
                $det->save();
                $list_id[] = $det->id;
            }
            
            $delete = DetSatuan::deleteAll("id NOT IN (". implode(',', $list_id) .") AND kode_atk='{$model->kode_brng}'");
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
        if (($model = Tblstockatk::findOne($id)) !== null) {
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
            return $this->render("/expmaster/barangatk", ['models' => $models]);
        } else {
            $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/master-barang-atk.xls';
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
                if (isset($row))
                    $row++;
                else
                    $row = $baseRow + $r;

                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $no++);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $arr['kode_brng']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['nama_brng']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['merk']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $arr['jumlah_brng']);
                $objPHPExcel->getActiveSheet()->mergeCells("F{$row}:G{$row}")->setCellValue('F' . $row, $arr['keterangan']);
            }

            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="master-filelegalitas.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
    }

    public function actionExcelstock() {
        session_start();
        $query = $_SESSION['query'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        $params = $_SESSION['params'];

        if (!empty($models)) {
            $saldoAwal = 0;
            $saldoAkhir = 0;
            $masuk = 0;
            $keluar = 0;
            foreach ($models as $key => $val) {
                $atkMasuk = TblDtransAtk::find()
                        ->select('sum(jmlh_brng) as stockmasuk')
                        ->join('JOIN', 'tbl_htrans_atk', 'tbl_htrans_atk.no_transaksi = tbl_dtrans_atk.no_trans')
                        ->where('tbl_htrans_atk.tgl >= "' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND tbl_htrans_atk.tgl <= "' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '" and tbl_dtrans_atk.kd_brng="' . $val['kode_brng'] . '"')
                        ->one();
                $atkKeluar = TblDtransAtkKeluar::find()
                        ->select('sum(jmlh_brng) as stockkeluar')
                        ->join('JOIN', 'tbl_htrans_atk_keluar', 'tbl_htrans_atk_keluar.no_transaksi = tbl_dtrans_atk_keluar.no_trans')
                        ->where('tbl_htrans_atk_keluar.tgl >= "' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND tbl_htrans_atk_keluar.tgl <= "' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '" and tbl_dtrans_atk_keluar.kd_brng="' . $val['kode_brng'] . '"')
                        ->one();
//                Yii::error($atkKeluar->stockkeluar);
                $saldoMasuk = TblDtransAtk::find()
                        ->select('sum(jmlh_brng) as saldomasuk')
                        ->join('JOIN', 'tbl_htrans_atk', 'tbl_htrans_atk.no_transaksi = tbl_dtrans_atk.no_trans')
                        ->where('tbl_htrans_atk.tgl >= "' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" and tbl_dtrans_atk.kd_brng="' . $val['kode_brng'] . '"')
                        ->one();
                $saldoKeluar = TblDtransAtkKeluar::find()
                        ->select('sum(jmlh_brng) as saldokeluar')
                        ->join('JOIN', 'tbl_htrans_atk_keluar', 'tbl_htrans_atk_keluar.no_transaksi = tbl_dtrans_atk_keluar.no_trans')
                        ->where('tbl_htrans_atk_keluar.tgl >= "' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" and tbl_dtrans_atk_keluar.kd_brng="' . $val['kode_brng'] . '"')
                        ->one();
                $jmlBarang = (empty($val['jumlah_brng'])) ? 0 : $val['jumlah_brng'];

                $saldoAwal = $jmlBarang + $saldoKeluar->saldokeluar - $saldoMasuk->saldomasuk;


                $masuk = (empty($atkMasuk->stockmasuk)) ? 0 : $atkMasuk->stockmasuk;
                $keluar = (empty($atkKeluar->stockkeluar)) ? 0 : $atkKeluar->stockkeluar;

                $saldoAkhir = $saldoAwal + $masuk - $keluar;
                $models[$key]['masuk'] = $masuk;
                $models[$key]['keluar'] = $keluar;
                $models[$key]['saldo_awal'] = $saldoAwal;
                $models[$key]['saldo_akhir'] = $saldoAkhir;
            }
        }

        return $this->render("/exprekap/stockatk", ['models' => $models, 'params' => $params]);
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_stock_atk')
                ->select("*")
                ->where(['like', 'kode_brng', $params['nama']])
                ->orWhere(['like', 'nama_brng', $params['nama']])
                ->limit(10);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}

?>
