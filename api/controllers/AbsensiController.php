<?php

namespace app\controllers;

use Yii;
use app\models\Tblkaryawan;
use app\models\AbsensiEttLog;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class AbsensiController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'absensiharian' => ['get'],
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

    public function actionAbsensiharian() {
        $params = $_REQUEST;
        
        $models = AbsensiEttLog::absen();
//        $query = new Query;
//        $query->from('ftm.att_log AS abs')
//                ->join('RIGHT JOIN', 'ftm.emp', 'emp.emp_id_auto = abs.pin AND date(abs.scan_date)="'.date('Y-m-d',strtotime($params['tanggal'])).'"')
//                ->join('INNER JOIN', 'tbl_karyawan as kry', 'kry.nik = emp.nik')
//                ->select('kry.nik AS nik, kry.nama, date(abs.scan_date) AS tanggal, min(abs.scan_date) AS masuk , max(abs.scan_date) AS keluar')
//                ->orderBy('kry.nik, tanggal')
//                ->groupBy('tanggal, nik');
//        
//        $query->andWhere('kry.status="Kerja"');
//        if (isset($params['niknama'])) {
//            $query->andWhere('(kry.nik LIKE "%'.$params['niknama'].'%" OR kry.nama LIKE "%'.$params['niknama'].'%")');
//        }        
//
//        $command = $query->createCommand();
//        $models = $command->queryAll();
        
        
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

}

?>
