<?php

namespace app\controllers;

use Yii;
use app\models\TblKaryawan;
use app\models\AbsensiEttLog;
use app\models\TblAbsent;
use app\models\TblHtransPotongan;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\db\Query;

class AbsensiController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'absensiharian' => ['get'],
                    'absensiproduksi' => ['post'],
                    'absensioperator' => ['get'],
                    'lemburharian' => ['get'],
                    'lembur' => ['get'],
                    'penggajian' => ['post'],
                    'rekap' => ['get'],
                    'penggajianexcel' => ['post'],
                    'karyawanexcel' => ['post'],
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

    public function actionRekap() {
        $params = $_REQUEST;
//        $params = json_decode(file_get_contents("php://input"), true);
        $niknama = (isset($params['niknama'])) ? $params['niknama'] : '';
        if (isset($params['tanggal'])) {
            $test = json_decode($params['tanggal'], true);
            $start = date("Y-m-d", strtotime($test['startDate']));

            $endate = date("Y-m-d", strtotime($test['endDate']));
        }
//         Yii::error($params['test']);
        $kry = TblKaryawan::aktif($niknama);
        /////
        $begin = new \DateTime($start);
//        $begine = $begin->format('Y-m-d');
        $end = new \DateTime($endate);
        $end = $end->modify('+1 day');
        $ende = $end->format('Y-m-d');
        $interval = \DateInterval::createFromDateString('1 day');

//        Yii::error($start);
        //array periode
        $period = new \DatePeriod($begin, $interval, $end);
        $abs = AbsensiEttLog::absen($start, $endate);
        $arrtgl = $this->Hitunghr($start, $endate);
//        Yii::error($ende);
//        echo json_encode($arrtgl);
        $dnew = [];
//        Yii::error($period);

        foreach ($kry as $r) {
            $hadir = 0;
            $absen = 0;
            $cuti = 0;
            $sakit = 0;
            $izin = 0;
            $sd = 0;
            $sh = 0;
            foreach ($arrtgl as $dt) {
//                echo json_encode($dt);

                if (isset($abs[$r->nik][$dt])) {
                    $hadir +=1;
                } else {
                    $ketAbsen = $this->Izin("$r->nik", $dt);
//                    echo json_encode($ketAbsen);
                    if ($ketAbsen == 'Absent') {
                        $absen +=1;
                    } elseif ($ketAbsen == 'Izin') {
                        $izin +=1;
                    } elseif ($ketAbsen == 'Cuti') {
                        $cuti +=1;
                    } elseif ($ketAbsen == 'Dinas Luar') {
                        $hadir +=1;
                    } elseif ($ketAbsen == 'Hadir') {
                        $hadir +=1;
                    } elseif ($ketAbsen == 'Sakit') {
                        $sakit +=1;
                    } elseif ($ketAbsen == 'Setengah Hari') {
                        $sh +=1;
                    } elseif ($ketAbsen == 'Surat Dokter') {
                        $sd +=1;
                    } elseif ($ketAbsen == '-') {
                        $absen +=1;
//                        echo json_encode('cuk');
                    } else {
                        $absen +=1;
                    }
                }
            }
            $dnew[] = [
                'nik' => $r->nik,
                'nama' => $r->nama,
                'Absen' => $absen,
                'Izin' => $izin,
                'Cuti' => $cuti,
                'Sakit' => $sakit,
                'Surat_Dokter' => $sd,
                'Setengah_Hari' => $sh,
                'Hadir' => $hadir
            ];
        }
        session_start();
        $_SESSION['tglStart'] = $start;
        $_SESSION['tglEnd'] = $endate;

        $this->setHeader(200);

//        echo json_encode(array('status' => 1, 'data' => $datas, 'start' => $start, 'end' => $end, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
        echo json_encode(array('status' => 1, 'data' => $dnew, 'start' => $start, 'end' => $endate), JSON_PRETTY_PRINT);
    }

    public function Izin($nik, $tanggal) {

        $absen = TblAbsent::find()->where(['nik' => $nik, 'tanggal' => $tanggal])->select('ket')->one();

        return isset($absen->ket) ? $absen->ket : '-';
    }

    public function Hitunghr($day1, $day2) {

        // memecah string tanggal awal untuk mendapatkan
        // tanggal, bulan, tahun
        $pecah1 = explode("-", $day1);
        $date1 = $pecah1[2];
        $month1 = $pecah1[1];
        $year1 = $pecah1[0];

        // memecah string tanggal akhir untuk mendapatkan
        // tanggal, bulan, tahun
        $pecah2 = explode("-", $day2);
        $date2 = $pecah2[2];
        $month2 = $pecah2[1];
        $year2 = $pecah2[0];

        // mencari total selisih hari dari tanggal awal dan akhir
        $jd1 = GregorianToJD($month1, $date1, $year1);
        $jd2 = GregorianToJD($month2, $date2, $year2);

        $selisih = $jd2 - $jd1;

        $libur1 = '';
        $libur2 = '';
        $arrtgl = [];
        // proses menghitung tanggal merah dan hari minggu
        // di antara tanggal awal dan akhir
        for ($i = 0; $i <= $selisih; $i++) {

            // menentukan tanggal pada hari ke-i dari tanggal awal
            $tanggal = mktime(0, 0, 0, $month1, $date1 + $i, $year1);
            $tglstr = date("Y-m-d", $tanggal);

            // menghitung jumlah tanggal pada hari ke-i
            // yang masuk dalam daftar tanggal merah selain minggu
//            if (!empty($tglLibur)) {
//                if (in_array($tglstr, $tglLibur)) {
//                    $libur1++;
//                }
//            }
            // menghitung jumlah tanggal pada hari ke-i
            // yang merupakan hari minggu
            if ((date("w", $tanggal) != 0)) {
                $arrtgl[] = $tglstr;
            }
        }

        // menghitung selisih hari yang bukan tanggal merah dan hari minggu
        return $arrtgl;
    }

    public function Htghr($m, $y) {

        $hasil = cal_days_in_month(CAL_GREGORIAN, $m, $y);

        return $hasil;
    }

    public function Depart($kd) {
        $qs = \app\models\Jabatan::find()
                ->where(['id_jabatan' => $kd])
                ->one();
        if (isset($qs->jabatan))
            return $qs->jabatan;
        else
            return '';
    }

    public function actionAbsensiproduksi() {
        $params = json_decode(file_get_contents("php://input"), true);
        $niknama = (isset($params['niknama'])) ? $params['niknama'] : '';
        $section = (isset($params['Section']['id_section'])) ? $params['Section']['id_section'] : '';
        $bulan = $params['bulan'];
        $tahun = $params['tahun'];
        $awaltgl = $tahun . "-" . $bulan . "-1";
        $akhirtgl = $tahun . "-" . $bulan . "-" . $this->Htghr($bulan, $tahun);
        $start = date("Y-m-d", strtotime($awaltgl));
        $endate = date("Y-m-d", strtotime($akhirtgl));
        //==========================[tanggal]=======================
        $begin = new \DateTime($start);
        $end = new \DateTime($endate);
        $end = $end->modify('+1 day');
        $interval = \DateInterval::createFromDateString('1 day');

        //array periode
        $period = new \DatePeriod($begin, $interval, $end);
        $abs = AbsensiEttLog::absen($start, $endate);

        $mm = date("m", strtotime($endate));
        $yy = date("Y", strtotime($endate));
        $htghr = $this->Htghr($mm, $yy);

        //==========================[karyawan]=======================
        $kry = TblKaryawan::aktif($niknama, $section);
        $data = [];
        $hadir = [];
        $i = 0;

        //taruh data karyawan
        foreach ($kry as $r) {
            if (!empty($r->status_karyawan)) {
                $data[$r->jabatan]['title'] = $this->Depart($r->jabatan);
                $data[$r->jabatan]['body'][$r->status_karyawan]['title'] = strtoupper($r->status_karyawan);
                $data[$r->jabatan]['body'][$r->status_karyawan]['subbody'][$i]['nik'] = $r->nik;
                $data[$r->jabatan]['body'][$r->status_karyawan]['subbody'][$i]['nama'] = $r->nama;

                foreach ($period as $dt) {
                    if (!isset($hadir[$dt->format("Y-m-d")])) {
                        $hadir[$dt->format("Y-m-d")] = 0;
                    }
                    if (isset($abs[$r->nik][$dt->format("Y-m-d")])) {

                        $hadir[$dt->format("Y-m-d")] += 1;
                        $data[$r->jabatan]['body'][$r->status_karyawan]['subbody'][$i]['tanggal'][$dt->format("Y-m-d")] = 'v';
                    } else {
                        $data[$r->jabatan]['body'][$r->status_karyawan]['subbody'][$i]['tanggal'][$dt->format("Y-m-d")] = '-';
                    }
                }
                $i++;
            }
        }


        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'start' => $start, 'end' => $endate, 'jmlhr' => $htghr, 'jmlhdr' => $hadir), JSON_PRETTY_PRINT);
    }

    public function actionAbsensioperator() {
        $params = $_REQUEST;
        $niknama = (isset($params['niknama'])) ? $params['niknama'] : '';
        $bulan = $params['bulan'];
        $tahun = $params['tahun'];
        $awaltgl = $tahun . "-" . $bulan . "-1";
        $akhirtgl = $tahun . "-" . $bulan . "-" . $this->Htghr($bulan, $tahun);
        //==========================[tanggal]=======================
        $start = date("Y-m-d", strtotime($awaltgl));
        $endate = date("Y-m-d", strtotime($akhirtgl));
        $begin = new \DateTime($start);
        $end = new \DateTime($endate);
        $end = $end->modify('+1 day');
        $interval = \DateInterval::createFromDateString('1 day');

        //array periode
        $period = new \DatePeriod($begin, $interval, $end);
        $abs = AbsensiEttLog::absen($start, $endate);
        //array karyawan aktif
        $kry = TblKaryawan::aktif($niknama);

        $mm = date("m", strtotime($endate));
        $yy = date("Y", strtotime($endate));
        $htghr = $this->Htghr($mm, $yy);

        $i = 0;
        $jml = 1;

        $data = [];
        $hadir = [];
        $tidakhadir = [];

        foreach ($kry as $kr) {

            if (!empty($kr->status_karyawan)) {

                $jml = isset($data[$kr->status_karyawan]['jumlah']) ? $data[$kr->status_karyawan]['jumlah'] + 1 : 0;
//                $jml = $this->Jmlkry($kr->status_karyawan);

                $data[$kr->status_karyawan]['title'] = $kr->status_karyawan;
                $data[$kr->status_karyawan]['jumlah'] = $jml;
                foreach ($period as $dt) {
                    //init data
                    if (!isset($hadir[$dt->format("Y-m-d")])) {
                        $hadir[$dt->format("Y-m-d")] = 0;
                        $jml_hadir = 0;
                    }

                    if (!isset($tidakhadir[$dt->format("Y-m-d")])) {
                        $tidakhadir[$dt->format("Y-m-d")] = 0;
                        $jml_tak_hadir = 0;
                    }

                    if (isset($abs[$kr->nik][$dt->format("Y-m-d")])) {
                        $jml_hadir = isset($data[$kr->status_karyawan]['hadir'][$dt->format("Y-m-d")]) ? $data[$kr->status_karyawan]['hadir'][$dt->format("Y-m-d")] + 1 : 0;
                        $hadir[$dt->format("Y-m-d")] += 1;
                        $data[$kr->status_karyawan]['listjumlah'][$dt->format("Y-m-d")] = $jml;
                        $data[$kr->status_karyawan]['hadir'][$dt->format("Y-m-d")] = isset($data[$kr->status_karyawan]['hadir'][$dt->format("Y-m-d")]) ? $data[$kr->status_karyawan]['hadir'][$dt->format("Y-m-d")] + 1 : 1;
                        $data[$kr->status_karyawan]['tdkhadir'][$dt->format("Y-m-d")] = $jml - $jml_hadir;
                    } else {
                        $jml_tak_hadir = isset($data[$kr->status_karyawan]['tdkhadir'][$dt->format("Y-m-d")]) ? $data[$kr->status_karyawan]['tdkhadir'][$dt->format("Y-m-d")] + 1 : 0;
                        $tidakhadir[$dt->format("Y-m-d")] += 1;
                        $data[$kr->status_karyawan]['listjumlah'][$dt->format("Y-m-d")] = $jml;
                        $data[$kr->status_karyawan]['hadir'][$dt->format("Y-m-d")] = $jml - $jml_tak_hadir;
                        $data[$kr->status_karyawan]['tdkhadir'][$dt->format("Y-m-d")] = $jml_tak_hadir;
                    }
                }
            }
        }
        $this->setHeader(200);
//        echo json_encode($data);
        echo json_encode(array('status' => 1, 'data' => $data, 'jmlhr' => $htghr, 'end' => $endate, 'totalhadir' => $hadir, 'totaltakhadir' => $tidakhadir), JSON_PRETTY_PRINT);
    }

    public function Jmlkry($status) {
        $jml = TblKaryawan::find()
                ->where(['status' => 'kerja', 'status_karyawan' => $status])
                ->groupBy('status_karyawan')
                ->select('count(*) as mgm')
                ->one();

        return $jml->mgm;
    }

    public function actionAbsensiharian() {
        $params = $_REQUEST;
        $niknama = (isset($params['niknama'])) ? $params['niknama'] : '';
        $lokasi_kntr = (isset($params['lokasi_kntr'])) ? $params['lokasi_kntr'] : '';
        $date = date('Y-m-d', strtotime($params['tanggal']));
        $models = [];

        $abs = AbsensiEttLog::absen($date, $date);
//        Yii::error($abs);
        $kry = TblKaryawan::aktif($niknama,'', $lokasi_kntr);
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

        if (date('w', strtotime($params['tanggal'])) == 6) { //jika sabtu, pulang jam 12
            $pulang = $date . ' 12:00';
        } else {
            $pulang = $date . ' 16:00';
        }

        $models = [];

        $abs = AbsensiEttLog::absen($date, $date); // ?
        $kry = TblKaryawan::aktif($niknama); // mengambil semua data karyawan

        foreach ($kry as $r) {
            $models[$r->nik] = ['nik' => $r->nik, 'nama' => $r->nama, 'masuk' => '', 'lemburpagi' => '-', 'keluar' => '', 'lembursore' => '-'];
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
                        $lemburpagi = floor($lemburpagi / 60);
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
                        $lembursore = floor($lembursore / 60);
                    }
                } else {
                    $lembursore = '-';
                }

                $models[$r->nik] = ['nik' => $r->nik, 'nama' => $r->nama, 'masuk' => $absensi['masuk'], 'lemburpagi' => $lemburpagi, 'keluar' => $absensi['keluar'], 'lembursore' => $lembursore];
            }
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionLembur() {
        $params = $_REQUEST;
        $niknama = (isset($params['niknama'])) ? $params['niknama'] : '';
        $date = date('Y-m-d', strtotime($params['tanggal']));
        $date_sampai = date('Y-m-d', strtotime($params['tanggal_sampai'] . ' +1day'));
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
                            $lemburpagi = floor($lemburpagi / 60);
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
                            $lembursore = floor($lembursore / 60);
                        }
                    } else {
                        $lembursore = 0;
                    }

                    //cek lembur,jika sudah ada
                    $lembur[$r->nik] += $lemburpagi + $lembursore;
                }
            }
        }

        //=========PROSES MASUKKAN DATA
        foreach ($kry as $r) {
            $models[$r->nik] = ['nik' => $r->nik, 'nama' => $r->nama, 'lembur' => $lembur[$r->nik]];
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionPenggajian() {
        $params = json_decode(file_get_contents("php://input"), true);
        $niknama = (isset($params['niknama'])) ? $params['niknama'] : '';
        $section = (isset($params['Section']['id_section'])) ? $params['Section']['id_section'] : '';

        $bulan = $params['bulan'];
        $tahun = $params['tahun'];

        //last month
        $last_month = $bulan - 1 % 12;
        $lyear = ($last_month == 0 ? ($tahun - 1) : $tahun);
        $lmonth = ($last_month == 0 ? '12' : $last_month);

//        $start = $lyear . '-' . $lmonth . '-20';

        $endate = $tahun . '-' . $bulan . '-20';

        $date = $lyear . '-' . $lmonth . '-21';


        $lokasi = $params['lokasi_kntr'];

        $arrtgl = $this->Hitunghr($date, $endate);
//        Yii::error($arrtgl);
        
        //--------
//         Yii::error($date);
        $models = [];

        $abs = AbsensiEttLog::absen($date, $endate);
        $kry = TblKaryawan::aktif($niknama, $section, $lokasi);

        //============PROSES HITUNG LEMBUR
        foreach ($kry as $r) {
            $lembur[$r->nik] = 0;

            foreach ($arrtgl as $dt) {
                $masuk = $dt . ' 7:45';
                if (date('w', strtotime($dt)) == 6) { //jika sabtu, pulang jam 12
                    $pulang = $dt . ' 12:00';
                } else {
                    $pulang = $dt . ' 16:00';
                }

                if (isset($abs[$r->nik][$dt])) {
                    $absensi = $abs[$r->nik][$dt];
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
                            $lemburpagi = floor($lemburpagi / 60);
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
                            $lembursore = floor($lembursore / 60);
                        }
                    } else {
                        $lembursore = 0;
                    }

                    //cek lembur,jika sudah ada
                    $lembur[$r->nik] += $lemburpagi + $lembursore;
                }
            }
        }

        //=========PROSES HITUNG ABSENT
        $ijin = TblAbsent::find()
                ->where('tanggal>="' . $date . '" AND tanggal<="' . $endate . '"')
//                ->where(['between','tanggal',$date,$endate])
                ->all();
//        echo json_encode($ijin);
        $ijin_jml = [];
        foreach ($ijin as $arr) {
            if (!isset($ijin_jml[$arr->nik]))
                $ijin_jml[$arr->nik] = 0;

            if ($arr->ket == 'Setengah Hari') {
                $ijin_jml[$arr->nik] += 0.5;
            } elseif ($arr->ket == 'Izin') {
                $ijin_jml[$arr->nik] += 1;
            } elseif ($arr->ket == 'Absent') {
                $ijin_jml[$arr->nik] += 1;
            }
        }
//        echo json_encode($ijin_jml);

        $query = new Query;
        $query->from('tbl_htrans_potongan as thp')
                ->join('LEFT JOIN', 'tbl_dtrans_potongan as tdp', 'tdp.no = thp.no_pot')
                ->where('tgl>="' . $date . '" AND tgl<="' . $endate . '"')
//                ->where(['between','tgl',$date,$endate])
                ->select("thp.tgl,thp.nik,tdp.jmlh");
        $command = $query->createCommand();
        $pinjaman = $command->queryAll();
//        Yii::error($potongan);
        $potongan_pinjaman = [];

        foreach ($pinjaman as $arr) {
            if (!isset($potongan_pinjaman[$arr['nik']])) {
                $potongan_pinjaman[$arr['nik']] = 0;
            }
            $potongan_pinjaman[$arr['nik']] += $arr['jmlh'];
        }
//                    Yii::error($potongan_pinjaman);
        //=========PROSES MASUKKAN DATA
        $no = 1;
        foreach ($kry as $r) {


            if (isset($ijin_jml[$r->nik])) { //cari ijin, dari proses di atas
                $ijin = $ijin_jml[$r->nik];
            } else {
                $ijin = 0;
            }

            if (isset($potongan_pinjaman[$r->nik])) { //cari potongan pinjaman, dari proses di atas
                $potongan_pinjaman_rp = $potongan_pinjaman[$r->nik];
            } else {
                $potongan_pinjaman_rp = 0;
            }

            /*
              sementara
             */

            //incentive
            $inc = 0;
            $jml_inc = 0;
            $ttl_inc = ($inc * $jml_inc);
            $ttl_kopensasi = ($r->gaji_pokok + $r->t_fungsional + $r->mgm + $ttl_inc);
            $ketengakerjaan = ($r->gaji_pokok * 3 / 100);
            $kesehatan = ($r->gaji_pokok * 1 / 100);
            $pinjaman = $potongan_pinjaman_rp;
//            echo json_encode($ijin);
            $absen = (($r->gaji_pokok / 25) * $ijin);
//            $test= 'nik'.$r->nik.'=>'.$ketengakerjaan."+".$kesehatan."+".$pinjaman."+".$absen;
//            Yii::error($test);
            $jml_potongan = ($ketengakerjaan + $kesehatan + $pinjaman + $absen);
            $netto = ($ttl_kopensasi - $absen - $jml_potongan);
            $nama = ($r->nik . ' - ' . $r->nama);
            
            ////

            $models[$r->nik] = [
                'no' => $no,
                'nama' => $nama,
                'thp' => $r->thp,
                'gaji_pokok' => $r->gaji_pokok,
                't_fungsional' => $r->t_fungsional,
                'mgm' => $r->mgm,
                'incentive' => $inc,
                'jml_inc' => $jml_inc,
                'ttl_incentive' => $ttl_inc,
                'jumlah_kopensasi' => $ttl_kopensasi,
                'ketenagakerjaan' => $ketengakerjaan,
                'kesehatan' => $kesehatan,
                'pinjaman' => $pinjaman,
                'jml_absen' => $ijin,
                'absen' => $absen,
                'jml_potongan' => $jml_potongan,
                'netto' => $netto
            ];
//                'lembur' => $lembur[$r->nik]];
            $no++;
        }

//        Yii::error($models);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models, 'tahun' => $tahun,'start'=>$date,'end' =>$endate), JSON_PRETTY_PRINT);
    }

    public function actionPenggajianexcel() {
        $params = json_decode(file_get_contents("php://input"), true);
        return $this->render("/absensi/penggajian", ['models' => $params]);
    }

    public function actionSlipgajiexcel() {
        $params = json_decode(file_get_contents("php://input"), true);
        return $this->render("/absensi/slipgaji", ['models' => $params]);
    }

    public function actionKaryawanexcel() {
        $start = $_SESSION['tglStart'];
        $end = $_SESSION['tglEnd'];

        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        return $this->render("/absensi/karyawan", ['models' => $params, 'start' => $start, 'end' => $end]);
    }

}

?>
