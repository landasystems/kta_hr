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
                    'absensiharian' => ['post'],
                    'absensiproduksi' => ['post'],
                    'absensioperator' => ['get'],
                    'lemburharian' => ['get'],
                    'lembur' => ['get'],
                    'listsec' => ['get'],
                    'penggajian' => ['post'],
                    'listkar' => ['post'],
                    'penggajiankaryawan' => ['post'],
                    'minggu' => ['get'],
                    'rekap' => ['get'],
                    'penggajianexcel' => ['post'],
                    'gjkryexcel' => ['get'],
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
//            Yii::error($start);
            $endate = date("Y-m-d", strtotime($test['endDate']));
        }
//         Yii::error($params['test']);
        $kry = TblKaryawan::aktif($niknama);
        /////
        $begin = new \DateTime($start);
        $begin1 = $begin->modify("+1 day");
        $begine = $begin1->format('Y-m-d');
        Yii::error($begine);
        $end = new \DateTime($endate);
        $end = $end->modify('+1 day');
        $ende = $end->format('Y-m-d');
        $interval = \DateInterval::createFromDateString('1 day');

//        Yii::error($start);
        //array periode
        $period = new \DatePeriod($begin1, $interval, $end);
        $abs = AbsensiEttLog::absen($begine, $endate);
        $arrtgl = $this->Hitunghr($begine, $endate);
        //== menentukan hari libur 
        $libr = \app\models\TblKalender::find()
                ->where('tgl>="' . $start . '" AND tgl<="' . $endate . '"')
                ->all();
        $tgl_libur = [];
        foreach ($libr as $vl) {
            $tgl_libur[$vl->tgl] = $vl->attributes;
        }
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
                    } elseif ($ketAbsen == 'Izin Keluar') {
                        $hadir +=1;
                    } elseif ($ketAbsen == 'Izin Absent') {
                        $hadir +=1;
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
//                    } elseif ($ketAbsen == '-') {
//                        $absen +=1;
                    } else {
                        if (isset($tgl_libur[$dt])) {
                            $absen +=0;
                        } else {
                            $absen +=1;
                        }
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

        echo json_encode(array('status' => 1, 'data' => $dnew, 'start' => strtotime($begine), 'end' => strtotime($endate)), JSON_PRETTY_PRINT);
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

    public function actionMinggu() {
        $params = $_REQUEST;
        $bulan = $params['bulan'];
        $tahun = $params['tahun'];

        //last month
        $last_month = $bulan - 1 % 12;
        $lyear = ($last_month == 0 ? ($tahun - 1) : $tahun);
        $lmonth = ($last_month == 0 ? '12' : $last_month);


        $endate = $tahun . '-' . $bulan . '-20';

        $date = $lyear . '-' . $lmonth . '-21';

        $minggu = $this->Sunday($date, $endate);
        $date2 = [];
        $mg5 = 0;
        $i = 0;
        foreach ($minggu as $key => $value) {
            $jml = 0;
            foreach ($value as $val) {
                $jml +=1;
            }
            $date2[$key]['hari'] = $value;
            $date2[$key]['jml_hari'] = $jml;
            $date2['hari'][$key] = $jml;
//            Yii::error($jml);
            $mg5 +=1;
            $i++;
        }

        $jmlHari = max($date2['hari']);

        $ng_hide = ($mg5 < 5) ? 'yes' : 'no';

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $date2, 'start' => $date, 'end' => $endate, 'hide' => $ng_hide, 'hari_max' => $jmlHari), JSON_PRETTY_PRINT);
    }

    public function Sunday($start, $end) {

        $begin = new \DateTime($start);
        $end = new \DateTime($end);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval, $end);

        $data = [];
        $data2 = [];
        $minggu1 = [];
        $minggu2 = [];
        $minggu3 = [];
        $minggu4 = [];
        $minggu5 = [];
        $i = 1;

        //mendeteksi hari minggu

        foreach ($daterange as $date) {

            $day = $date->format('d');
            $month = $date->format('m');
            $year = $date->format('Y');
            $tanggal = mktime(0, 0, 0, $month, $day, $year);

            if ((date("w", $tanggal) == 0)) {
                $data['minggu' . $i] = $date->format('Y-m-d');
                $i++;
            }
        }


        //mengkelompokkan hari per minggu

        foreach ($daterange as $val) {

            $tanggal = $val->format('Y-m-d');

            if ($tanggal < $data['minggu1']) {
                $minggu1[] = $tanggal;
            } else if ($tanggal > $data['minggu1'] && $tanggal < $data['minggu2']) {
                $minggu2[] = $tanggal;
            } else if ($tanggal > $data['minggu2'] && $tanggal < $data['minggu3']) {
                $minggu3[] = $tanggal;
            } else if ($tanggal > $data['minggu3'] && $tanggal < $data['minggu4']) {
                $minggu4[] = $tanggal;
            } else if ($tanggal > $data['minggu4']) {
                if (!empty($data['minggu5'])) {
                    if ($tanggal < $data['minggu5']) {
                        $minggu5[] = $tanggal;
                    }
                } else {
                    $minggu5[] = $tanggal;
                }
            }
        }

        //mengelompokkan kembali hari per minggu sesuai rumus incentive
        //jika minggu 1 kurang atau sama dengan 3 maka minggu 1 dan 2 di gabungkan
        if (count($minggu1) < 3) {
            if (!empty($minggu5)) {
                $data2 = ['minggu1' => array_merge($minggu1, $minggu2), 'minggu2' => $minggu3, 'minggu3' => $minggu4, 'minggu4' => $minggu5];
            } else {
                $data2 = ['minggu1' => array_merge($minggu1, $minggu2), 'minggu2' => $minggu3, 'minggu3' => $minggu4];
            }
        } else if (!empty($minggu5)) {

            //jika minggu terakhir kurang atau sama dengan 3 maka minggu terakhir di gabung dengan minggu sebelumnya
            if (count($minggu5) < 3) {
                $data2 = [ 'minggu1' => $minggu1, 'minggu2' => $minggu2, 'minggu3' => $minggu3, 'minggu4' => array_merge($minggu4, $minggu5)];
            } else {
                $data2 = [ 'minggu1' => $minggu1, 'minggu2' => $minggu2, 'minggu3' => $minggu3, 'minggu4' => $minggu4, 'minggu5' => $minggu5];
            }
        } else {

            //jika minggu terakhir kurang atau sama dengan 3 maka minggu terakhir di gabung dengan minggu sebelumnya
            if (count($minggu4) < 3) {
                $data2 = [ 'minggu1' => $minggu1, 'minggu2' => $minggu2, 'minggu3' => array_merge($minggu3, $minggu4)];
            } else {
                $data2 = [ 'minggu1' => $minggu1, 'minggu2' => $minggu2, 'minggu3' => $minggu3, 'minggu4' => $minggu4];
            }
        }



        return $data2;
    }

    public function actionListsec() {

        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_section')
                ->select("id_section,section");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
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

//        echo count($kry);

        $mm = date("m", strtotime($endate));
        $yy = date("Y", strtotime($endate));
        $htghr = $this->Htghr($mm, $yy);

        $i = 0;
        $jml = 1;

        $data = [];
        $hadir = [];
        $jmlHadir = [];
        $tidakhadir = [];
        $jmL = array();

        $tes = array();
        foreach ($kry as $kr) {
            if (!empty($kr->status_karyawan)) {
                $jmL[$kr->status_karyawan]['jumlah'] = isset($jmL[$kr->status_karyawan]['jumlah']) ? $jmL[$kr->status_karyawan]['jumlah'] + 1 : 1;
            }
        }

//        echo json_encode($jmL);

        foreach ($kry as $kr) {

            if (!empty($kr->status_karyawan)) {

                $jml = $jmL[$kr->status_karyawan]['jumlah'];

                $data[$kr->status_karyawan]['title'] = $kr->status_karyawan;
                $data[$kr->status_karyawan]['jumlah'] = $jml;
                foreach ($period as $dt) {
                    //init data
                    if (!isset($hadir[$kr->status_karyawan][$dt->format("Y-m-d")])) {
                        $hadir[$kr->status_karyawan][$dt->format("Y-m-d")] = 0;
                    }

                    if (!isset($jmlHadir[$dt->format("Y-m-d")])) {
                        $jmlHadir[$dt->format("Y-m-d")] = 0;
                    }

                    if (!isset($jmlTidakHadir[$dt->format("Y-m-d")])) {
                        $jmlTidakHadir[$dt->format("Y-m-d")] = 0;
                    }

                    if (isset($abs[$kr->nik][$dt->format("Y-m-d")])) {
                        $jmlHadir[$dt->format("Y-m-d")] += 1;
                        $hadir[$kr->status_karyawan][$dt->format("Y-m-d")] += 1;
                    }

                    $jmlTidakHadir[$dt->format("Y-m-d")] = ($jmL['Tetap']['jumlah'] + $jmL['Kontrak']['jumlah'] + $jmL['Borongan']['jumlah']) - $jmlHadir[$dt->format("Y-m-d")];

                    $data[$kr->status_karyawan]['listjumlah'][$dt->format("Y-m-d")] = $jml;
                    $data[$kr->status_karyawan]['hadir'][$dt->format("Y-m-d")] = $hadir[$kr->status_karyawan][$dt->format("Y-m-d")];
                    $data[$kr->status_karyawan]['tdkhadir'][$dt->format("Y-m-d")] = $jml - $hadir[$kr->status_karyawan][$dt->format("Y-m-d")];
                }
            }
        }
        $this->setHeader(200);
//        echo json_encode($data);
        echo json_encode(array('status' => 1, 'data' => $data, 'jmlhr' => $htghr, 'end' => $endate, 'totalhadir' => $jmlHadir, 'totaltakhadir' => $jmlTidakHadir), JSON_PRETTY_PRINT);
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
//        $params = $_REQUEST;
        $params = json_decode(file_get_contents("php://input"), true);
        $niknama = (isset($params['niknama'])) ? $params['niknama'] : '';
        $lokasi_kntr = (isset($params['lokasi_kntr'])) ? $params['lokasi_kntr'] : '';
//        $sec = json_decode($params['Section'], true);
//        $section = (isset($sec)) ? $sec['id_section'] : '';
        $section = (isset($params['section'])) ? $params['section'] : '';
        $date = date('Y-m-d', strtotime($params['tanggal']));
        $models = [];

        $abs = AbsensiEttLog::absen($date, $date);
//        Yii::error($abs);
        $kry = TblKaryawan::aktif($niknama, $section, $lokasi_kntr);

//        Yii::error($ijn);
        foreach ($kry as $r) {
            $pegawai = ['nik' => $r->nik, 'nama' => $r->nama];
//            Yii::error($pegawai);
            if (isset($abs[$r->nik][$date]) && $params['status'] == 'hadir') {
                $absensi = $abs[$r->nik][$date];

                if ($absensi['masuk'] == $absensi['keluar']) { //lupa absent keluar
                    $absensi['keluar'] = '';
                }
                $models[] = ['nik' => $r->nik, 'nama' => $r->nama, 'karyawan' => $pegawai, 'masuk' => date("H:i",strtotime($absensi['masuk'])), 'keluar' => date("h:i",  strtotime($absensi['keluar']))];
            } elseif (!isset($abs[$r->nik][$date]) && $params['status'] == 'tidakhadir') {
                $absen = $this->Sttsabsen($r->nik, $date);
                if ($absen == false) {
                    $models[] = ['nik' => $r->nik, 'nama' => $r->nama, 'karyawan' => $pegawai, 'masuk' => '', 'keluar' => '', 'disable' => $absen];
                }
            }
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function Sttsabsen($nik, $date) {
        $absen = TblAbsent::findOne(['nik' => $nik, 'tanggal' => $date]);
        $return = (isset($absen)) ? true : false;
        return $return;
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

    public function actionListkar() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        $section = [];
        foreach ($params as $key => $value) {
            $section[] = $value['id_section'];
        }

//        Yii::error($section);

        if (empty($section)) {
            $kar = \app\models\TblKaryawan::find()->select("nik,nama")->where(['status' => 'Kerja'])->all();
        } else {
            $kar = \app\models\TblKaryawan::find()->select("nik,nama")->where(['status' => 'Kerja'])->andWhere(['in', 'section', $section])->all();
        }

        $data = [];
        foreach ($kar as $key => $val) {
            $data[$key]['nik'] = $val->nik;
            $data[$key]['nama'] = $val->nama;
        }

//        Yii::error($data);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data), JSON_PRETTY_PRINT);
    }

    public function actionPenggajiankaryawan() {
        $params = json_decode(file_get_contents("php://input"), true);

        if (!empty($params['Sections'])) {
            $section = [];
            foreach ($params['Sections'] as $key => $value) {
                $section[] = $value['id_section'];
            }
        } else {
            $section = '';
        }

        if (!empty($params['Namakr'])) {
            $nmkr = [];
            foreach ($params['Namakr'] as $keys => $values) {
                $nmkr[] = $values['nik'];
            }
        } else {
            $nmkr = '';
        }


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
        $sunday = $this->Sunday($date, $endate);

        Yii::error($nmkr);

        $models = [];

        $abs = AbsensiEttLog::absen($date, $endate);
        $kry = TblKaryawan::aktif($nmkr, $section, $lokasi);

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

        //== menentukan hari libur 
        $libr = \app\models\TblKalender::find()
                ->where('tgl>="' . $date . '" AND tgl<="' . $endate . '"')
                ->all();
        $tgl_libur = [];
        foreach ($libr as $vl) {
            $tgl_libur[$vl->tgl] = $vl->attributes;
        }

        //=========PROSES HITUNG ABSENT
        $ijin = TblAbsent::find()
                ->where('tanggal>="' . $date . '" AND tanggal<="' . $endate . '"')
//                ->where(['between','tanggal',$date,$endate])
                ->all();
//        echo json_encode($ijin);
//        error e di kene
        $ijin_jml = [];
        $sth = [];
        $ijnh = [];
        $absh = [];
        $cth = [];
        $sdh = [];
        $skh = [];
        $data_absen = [];
        $data_absen_tes = [];
        foreach ($ijin as $arr) {
            $data_absen[$arr->nik][$arr->tanggal] = $arr->attributes;
            $data_absen_tes["01006"][$arr->tanggal] = $arr->attributes;
        }
//        Yii::error($data_absen_tes);
        //==== absen
//        echo json_encode($ijin_jml);
//        Yii::error($data_absen['00001']['2015-12-26']);
        $query = new Query;
        $query->from('tbl_htrans_potongan as thp')
                ->join('LEFT JOIN', 'tbl_dtrans_potongan as tdp', 'tdp.no = thp.no_pot')
                ->where('tgl<="' . $endate . '" AND DATE_ADD(thp.tgl, INTERVAL thp.cicilan MONTH) >= "' . $endate . '"')
                ->select('thp.tgl,thp.nik,tdp.perbulan');
        $command = $query->createCommand();
        $pinjaman = $command->queryAll();
//        Yii::error($potongan);
        $potongan_pinjaman = [];
//        Yii::error($pinjaman);
        foreach ($pinjaman as $arr) {
//            Yii::error($arr['tgl_ak']);
//            if($endate <= $arr['tgl_ak']){
            if (!isset($potongan_pinjaman[$arr['nik']])) {
                $potongan_pinjaman[$arr['nik']] = 0;
            }
            $potongan_pinjaman[$arr['nik']] += $arr['perbulan'];
//            }
        }
//                    Yii::error($potongan_pinjaman);
        //=========PROSES MASUKKAN DATA
//        Yii::error($cth['00003']);

        $no = 1;
        foreach ($kry as $r) {

//            if ($r->nik !== '00000') {
//                hempi 00001
//                kesit 00123
            if (isset($potongan_pinjaman[$r->nik])) { //cari potongan pinjaman, dari proses di atas
                $potongan_pinjaman_rp = $potongan_pinjaman[$r->nik];
            } else {
                $potongan_pinjaman_rp = 0;
            }

            /* start incentive */

            $data = [];
            $data2 = [];
            $incentive = 0;
            $ket_cuti = [];
            $ket_sakit = [];
            $ket_absent = [];
            $ket_sdokter = [];
            $ket_izin = [];
            $potongan_abs = [];
            $penjelasan = [];
            $ket_stengah = [];
            foreach ($sunday as $key => $value) {

                foreach ($value as $val) {

                    if (isset($abs[$r->nik][$val])) {

                        $out = $abs[$r->nik][$val]['keluar'];
                        $in = $abs[$r->nik][$val]['masuk'];
                        $dataabs = isset($data_absen[$r->nik][$val]) ? $data_absen[$r->nik][$val] : '';

                        if (!isset($ket_absent[$r->nik])) {
                            $ket_absent[$r->nik] = 0;
                        }
                        if (!isset($ket_sdokter[$r->nik])) {
                            $ket_sdokter[$r->nik] = 0;
                        }
                        if (!isset($ket_stengah[$r->nik])) {
                            $ket_stengah[$r->nik] = 0;
                        }
                        if (!isset($ket_sakit[$r->nik])) {
                            $ket_sakit[$r->nik] = 0;
                        }
                        if (!isset($ket_cuti[$r->nik])) {
                            $ket_cuti[$r->nik] = 0;
                        }
                        if (!isset($ket_izin[$r->nik])) {
                            $ket_izin[$r->nik] = 0;
                        }
                        /////

                        if (is_array($dataabs)) {
//                                if ($r->nik == '01006') {
//                                    Yii::error($val);
//                                    Yii::error($dataabs['ket']);
//                                }
                            if ($dataabs['ket'] == 'Absent') {

                                $ket_absent[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (' . $dataabs['ket'] . ')';
                                $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                            } else if ($dataabs['ket'] == 'Sakit') {

                                $ket_sakit[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (' . $dataabs['ket'] . ')';
                                $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                            } else if ($dataabs['ket'] == 'Setengah Hari') {

                                $ket_stengah[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (1/2 Hari)';
                                $data2[$key] = (empty($data2[$key])) ? 0 : $data2[$key];
                            } else if ($dataabs['ket'] == 'Izin') {

                                $ket_izin[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (' . $dataabs['ket'] . ')';
                                $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                            } else if ($dataabs['ket'] == 'Cuti') {

                                $ket_cuti[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (' . $dataabs['ket'] . ')';
                                $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                            } else if ($dataabs['ket'] == 'Surat Dokter') {

                                $ket_sdokter[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (SD)';
                                $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                            } else if ($dataabs['ket'] == 'Dinas Luar' OR $dataabs['ket'] == 'Izin Absent') {

                                $data2[$key] = (empty($data2[$key])) ? 0 : $data2[$key];
                            }
                        } else if ($out >= "7.45" && $out <= "09.59") {

                            $ket_absent[$r->nik] += 1;
                            $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (Pulang)';
                            $data2[$key] = (empty($data2[$key])) ? 0 : $data2[$key];
                        } else if ($out >= "10.00" && $out <= "13.00") {

                            $ket_stengah[$r->nik] += 1;
                            $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (1/2 Hari)';
                            $data2[$key] = (empty($data2[$key])) ? 0 : $data2[$key];
                        } else if ($out > "13.00") {

                            $data2[$key] = (empty($data2[$key])) ? 0 : $data2[$key];
                        }
                    } else {
                        $dataabs = isset($data_absen[$r->nik][$val]) ? $data_absen[$r->nik][$val] : '';

                        if (!empty($dataabs)) {

                            if (!isset($ket_cuti[$r->nik])) {
                                $ket_cuti[$r->nik] = 0;
                            }
                            if (!isset($ket_sakit[$r->nik])) {
                                $ket_sakit[$r->nik] = 0;
                            }
                            if (!isset($ket_sdokter[$r->nik])) {
                                $ket_sdokter[$r->nik] = 0;
                            }
                            if (!isset($ket_absent[$r->nik])) {
                                $ket_absent[$r->nik] = 0;
                            }
                            if (!isset($ket_izin[$r->nik])) {
                                $ket_izin[$r->nik] = 0;
                            }
                            if (!isset($ket_stengah[$r->nik])) {
                                $ket_stengah[$r->nik] = 0;
                            }

                            /////
                            if ($dataabs['ket'] == 'Dinas Luar' OR $dataabs['ket'] == 'Izin Absent') {

                                $data2[$key] = (empty($data2[$key])) ? 0 : $data2[$key];
                            } else if ($dataabs['ket'] == 'Cuti') {

                                $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                                $ket_cuti[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (' . $dataabs['ket'] . ')';
                            } else if ($dataabs['ket'] == 'Sakit') {

                                $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                                $ket_sakit[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (' . $dataabs['ket'] . ')';
                            } else if ($dataabs['ket'] == 'Surat Dokter') {

                                $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                                $ket_sdokter[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (SD)';
                            } else if ($dataabs['ket'] == 'Absent') {

                                $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                                $ket_absent[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (' . $dataabs['ket'] . ')';
                            } else if ($dataabs['ket'] == 'Izin') {

                                $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                                $ket_izin[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (' . $dataabs['ket'] . ')';
                            } else if ($dataabs['ket'] == 'Setengah Hari') {

                                $data2[$key] = (empty($data2[$key])) ? 0 : $data2[$key];
                                $ket_stengah[$r->nik] += 1;
                                $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (1/2 Hari)';
                            }
                        } else if (isset($tgl_libur[$val])) {
                            $data2[$key] = (empty($data2[$key])) ? 0 : $data2[$key];
                        } else {
                            if (!isset($ket_absent[$r->nik])) {
                                $ket_absent[$r->nik] = 0;
                            }
                            $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                            $ket_absent[$r->nik] += 1;
                            $penjelasan[$r->nik][] = Date('d M y', strtotime($val)) . ' (Absent)';
                        }
                    }
                }
//                    Yii::error($r->nik);
                $teswae = isset($data2[$key]) ? $data2[$key] : 1;
                $data[$key] = ($teswae == 0) ? 1 : 0;
            }

//            Yii::error($data);
            foreach ($data as $newdt) {
                $incentive += $newdt;
            }

            /* end incentive */

            /*
              sementara
             */

            //deskripsi
            $inc = $incentive;
            $jml_inc = $r->t_kehadiran;
            $ttl_inc = ($inc * $jml_inc);
            $ttl_kopensasi = ($r->gaji_pokok + $r->t_fungsional + $r->mgm + $ttl_inc);
            $ketengakerjaan = ($r->gaji_pokok * 3 / 100);
            $kesehatan = ($r->gaji_pokok * 1 / 100);
            $pinjaman = $potongan_pinjaman_rp;
            $nama = ($r->nik . ' - ' . $r->nama);

            $mg1 = '';
            $mg2 = '';
            $mg3 = '';
            $mg4 = '';
            $mg5 = '';

            if (isset($data['minggu1'])) {
                if ($data['minggu1'] === 1) {
                    $mg1 = 1;
                } else {
                    $mg1 = 'x';
                }
            }
            if (isset($data['minggu2'])) {
                if ($data['minggu2'] === 1) {
                    $mg2 = 1;
                } else {
                    $mg2 = 'x';
                }
            }
            if (isset($data['minggu3'])) {
                if ($data['minggu3'] === 1) {
                    $mg3 = 1;
                } else {
                    $mg3 = 'x';
                }
            }
            if (isset($data['minggu4'])) {
                if ($data['minggu4'] === 1) {
                    $mg4 = 1;
                } else {
                    $mg4 = 'x';
                }
            }
            if (isset($data['minggu5'])) {
                if ($data['minggu5'] === 1) {
                    $mg5 = 1;
                } else {
                    $mg5 = 'x';
                }
            }
            ////
//            Yii::error($mg1);
            //
               
                $ketabsen = (!empty($ket_absent[$r->nik])) ? $ket_absent[$r->nik] : 0;
            $ketizin = (!empty($ket_izin[$r->nik])) ? $ket_izin[$r->nik] : 0;
            $ketsakit = (!empty($ket_sakit[$r->nik])) ? $ket_sakit[$r->nik] : 0;
            $ketdokter = (!empty($ket_sdokter[$r->nik])) ? $ket_sdokter[$r->nik] : 0;
            $ketsetengah = (!empty($ket_stengah[$r->nik])) ? $ket_stengah[$r->nik] : 0;
            $ketcuti = (!empty($ket_cuti[$r->nik])) ? $ket_cuti[$r->nik] : 0;
            $potabs = ($ketabsen + $ketizin + $ketsakit);
            if (isset($r->section)) {
                $secmod = $r->sect->section;
            } else {
                $secmod = '-';
            }

            if (!empty($penjelasan[$r->nik])) {

                $kets = implode(', ', $penjelasan[$r->nik]);
            } else {

                $kets = '';
            }


            $potong_setengah = ($ketsetengah * 0.5);

            $models[$r->nik] = [
                'no' => $no,
                'sect' => $secmod,
                'nama' => $nama,
                'mg1' => $mg1,
                'mg2' => $mg2,
                'mg3' => $mg3,
                'mg4' => $mg4,
                'mg5' => $mg5,
                'ttlinc' => $incentive,
                'absh' => $ketabsen,
                'ijnh' => $ketizin,
                'skh' => $ketsakit,
                'sdh' => $ketdokter,
                'sth' => $ketsetengah,
                'cth' => $ketcuti,
                'ptga' => $potabs,
                'thp' => (25 - ($ketabsen + $ketizin + $ketsakit + $potong_setengah)),
                'ptgs' => $ketsetengah,
                'ket' => $kets,
            ];
            $no++;
        }

//        Yii::error($models);
        session_start();
        $_SESSION['data_gajikr']['data'] = $models;
        $_SESSION['data_gajikr']['tahun'] = $tahun;
        $_SESSION['data_gajikr']['start'] = strtotime($date);
        $_SESSION['data_gajikr']['end'] = strtotime($endate);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models, 'tahun' => $tahun, 'start' => strtotime($date), 'end' => strtotime($endate)), JSON_PRETTY_PRINT);
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
        $sunday = $this->Sunday($date, $endate);

        // Yii::error($sunday['minggu1']);
//         Yii::error($date);
        $models = [];

        $abs = AbsensiEttLog::absen($date, $endate);
        $libr = \app\models\TblKalender::find()
                ->where('tgl>="' . $date . '" AND tgl<="' . $endate . '"')
                ->all();
        $tgl_libur = [];
        foreach ($libr as $vl) {
            $tgl_libur[$vl->tgl] = $vl->attributes;
        }
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
                ->where('tgl<="' . $endate . '" AND DATE_ADD(thp.tgl, INTERVAL thp.cicilan MONTH) >= "' . $endate . '"')
                ->select('thp.tgl,thp.nik,tdp.perbulan');
        $command = $query->createCommand();
        $pinjaman = $command->queryAll();
//        Yii::error($potongan);
        $potongan_pinjaman = [];
//        Yii::error($pinjaman);
        foreach ($pinjaman as $arr) {
//            Yii::error($arr['tgl_ak']);
//            if($endate <= $arr['tgl_ak']){
            if (!isset($potongan_pinjaman[$arr['nik']])) {
                $potongan_pinjaman[$arr['nik']] = 0;
            }
            $potongan_pinjaman[$arr['nik']] += $arr['perbulan'];
//            }
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

            /* start incentive */

            $data = [];
            $data2 = [];
            $incentive = 0;

            foreach ($sunday as $key => $value) {

                foreach ($value as $val) {

                    if (isset($abs[$r->nik][$val])) {

                        $data2[$key] = (empty($data2[$key])) ? 0 : $data2[$key];
                    } else {
                        if (!isset($tgl_libur[$val])) {
                            $data2[$key] = (empty($data2[$key])) ? 1 : $data2[$key] + 1;
                        }
                    }
                }

                $data[$key] = ($data2[$key] == 0) ? 1 : 0;
            }
            foreach ($data as $newdt) {
                $incentive += $newdt;
            }
//            Yii::error($data);
            /* end incentive */

            /*
              sementara
             */

            //deskripsi
            $inc = $incentive;
            $jml_inc = $r->t_kehadiran;
            $ttl_inc = ($inc * $jml_inc);
            $ttl_kopensasi = ($r->gaji_pokok + $r->t_fungsional + $r->mgm + $ttl_inc);
            $ketengakerjaan = ($r->gaji_pokok * 3 / 100);
            $kesehatan = ($r->gaji_pokok * 1 / 100);
            $pinjaman = $potongan_pinjaman_rp;
//            echo json_encode($ijin);
            $absen = (($r->gaji_pokok / 25) * $ijin);
//            $test= 'nik'.$r->nik.' => '.$ketengakerjaan."+".$kesehatan."+".$pinjaman."+".$absen;
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
        echo json_encode(array('status' => 1, 'data' => $models, 'tahun' => $tahun, 'start' => $date, 'end' => $endate), JSON_PRETTY_PRINT);
    }

    public function actionPenggajianexcel() {
        $params = json_decode(file_get_contents("php://input"), true);
        return $this->render("/absensi/penggajian", ['models' => $params]);
    }

    public function actionGjkryexcel() {
        session_start();
        $data = $_SESSION['data_gajikr']['data'];
        $tahun = $_SESSION['data_gajikr']['tahun'];
        $date = $_SESSION['data_gajikr']['start'];
        $endate = $_SESSION['data_gajikr']['end'];
        return $this->render("/absensi/gjkry", ['data' => $data, 'tahun' => $tahun, 'start' => $date, 'end' => $endate]);
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
