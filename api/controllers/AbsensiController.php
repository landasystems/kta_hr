<?php

namespace app\controllers;

use Yii;
use app\models\TblKaryawan;
use app\models\AbsensiEttLog;
use yii\web\Controller;
use yii\filters\VerbFilter;

class AbsensiController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'absensiharian' => ['get'],
                    'lemburharian' => ['get'],
                    'lembur' => ['get'],
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
        $niknama = (isset($params['niknama'])) ? $params['niknama'] : '';
        $date = date('Y-m-d', strtotime($params['tanggal']));
        $models = [];

        $abs = AbsensiEttLog::absen($date, $date);
        $kry = TblKaryawan::aktif($niknama);

        foreach ($kry as $r) {
            if (isset($abs[$r->nik][$date]) && $params['status'] == 'hadir') {
                $absensi = $abs[$r->nik][$date];
                if ($absensi['masuk'] == $absensi['keluar']) { //lupa absent keluar
                    $absensi['keluar'] = '';
                }

                $models[] = ['nik' => $r->nik, 'nama' => $r->nama, 'masuk' => $absensi['masuk'], 'keluar' => $absensi['keluar']];
            } elseif (!isset($abs[$r->nik][$date]) && $params['status'] == 'tidakhadir') {
                $models[] = ['nik' => $r->nik, 'nama' => $r->nama, 'masuk' => '', 'keluar' => ''];
            }
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionLemburharian() {
        $params = $_REQUEST;
        $niknama = (isset($params['niknama'])) ? $params['niknama'] : '';
        $date = date('Y-m-d', strtotime($params['tanggal']));
        $masuk = $date . ' 7:45';

        if (date('w', strtotime($params['tanggal']))==6) { //jika sabtu, pulang jam 12
            $pulang = $date . ' 12:00';
        } else {
            $pulang = $date . ' 16:00';
        }

        $models = [];

        $abs = AbsensiEttLog::absen($date, $date);
        $kry = TblKaryawan::aktif($niknama);

        foreach ($kry as $r) {
            $models[] = ['nik' => $r->nik, 'nama' => $r->nama, 'masuk' => '', 'lemburpagi' => '-', 'keluar' => '', 'lembursore' => '-'];
            if (isset($abs[$r->nik][$date])) {
                $absensi = $abs[$r->nik][$date];
                if ($absensi['masuk'] == $absensi['keluar']) { //lupa absent keluar
                    $absensi['keluar'] = '';
                }

                //--------lembur pagi
                $from_time = strtotime($absensi['masuk']);
                $to_time = strtotime($masuk);
                if ($from_time < $to_time) {
                    $lemburpagi = round(abs($to_time - $from_time) / 60, 2);
                    if ($lemburpagi < 105) { //toleransi 15 menit
                        $lemburpagi = '-';
                    } else {
                        $lemburpagi = round($lemburpagi / 60, 0, PHP_ROUND_HALF_DOWN);
                    }
                } else {
                    $lemburpagi = '-';
                }


                //-------lembur sore
                $from_time = strtotime($pulang);
                $to_time = strtotime($absensi['keluar']);
                if ($to_time > $from_time) {
                    $lembursore = round(abs($to_time - $from_time) / 60, 2);
                    if ($lembursore < 115) { //toleransi 5 menit
                        $lembursore = '-';
                    } else {
                        $lembursore = round($lembursore / 60, 0, PHP_ROUND_HALF_DOWN);
                    }
                } else {
                    $lembursore = '-';
                }

                $models[] = ['nik' => $r->nik, 'nama' => $r->nama, 'masuk' => $absensi['masuk'], 'lemburpagi' => $lemburpagi, 'keluar' => $absensi['keluar'], 'lembursore' => $lembursore];
            }
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionLembur() {
        $params = $_REQUEST;
        $niknama = (isset($params['niknama'])) ? $params['niknama'] : '';
        $date = date('Y-m-d', strtotime($params['tanggal']));
        $date_sampai = date('Y-m-d', strtotime($params['tanggal_sampai']));

        //---init perulangan tanggal
        $begin = new \DateTime($date);
        $end = new \DateTime($date_sampai);
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);
        //--------

        $models = [];

        $abs = AbsensiEttLog::absen($date, $date_sampai);
        $kry = TblKaryawan::aktif($niknama);

        //============PROSES HITUNG LEMBUR
        foreach ($kry as $r) {
            $lembur[$r->nik] = 0;

            foreach ($period as $dt) {
                $masuk = $dt->format("Y-m-d") . ' 7:45';
                if (date('w', strtotime($dt->format("Y-m-d"))) == 6) { //jika sabtu, pulang jam 12
                    $pulang = $dt->format("Y-m-d") . ' 12:00';
                } else {
                    $pulang = $dt->format("Y-m-d") . ' 16:00';
                }

                if (isset($abs[$r->nik][$dt->format("Y-m-d")])) {
                    $absensi = $abs[$r->nik][$dt->format("Y-m-d")];
                    if ($absensi['masuk'] == $absensi['keluar']) { //lupa absent keluar
                        $absensi['keluar'] = '';
                    }

                    //--------lembur pagi
                    $lemburpagi = 0;
                    $from_time = strtotime($absensi['masuk']);
                    $to_time = strtotime($masuk);
                    if ($from_time < $to_time) {
                        $lemburpagi = round(abs($to_time - $from_time) / 60, 2);
                        if ($lemburpagi < 105) { //toleransi 15 menit
                            $lemburpagi = 0;
                        } else {
                            $lemburpagi = round($lemburpagi / 60, 0, PHP_ROUND_HALF_DOWN);
                        }
                    } else {
                        $lemburpagi = 0;
                    }


                    //-------lembur sore
                    $lemburpagi = 0;
                    $from_time = strtotime($pulang);
                    $to_time = strtotime($absensi['keluar']);
                    if ($to_time > $from_time) {
                        $lembursore = round(abs($to_time - $from_time) / 60, 2);
                        if ($lembursore < 115) { //toleransi 5 menit
                            $lembursore = 0;
                        } else {
                            $lembursore = round($lembursore / 60, 0, PHP_ROUND_HALF_DOWN);
//                            echo date('Y-m-d, H:i', $to_time) . '-' . date('Y-m-d, H:i', $from_time);
                        }
                    } else {
                        $lembursore = 0;
                    }

                    //cek lembur,jika sudah ada
                    $lembur[$r->nik] += $lemburpagi + $lembursore;
                }
            }
//            echo '<br/>';
        }

        //=========PROSES MASUKKAN DATA
        foreach ($kry as $r) {
            $models[$r->nik] = ['nik' => $r->nik, 'nama' => $r->nama, 'lembur' => $lembur[$r->nik]];
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

}

?>
