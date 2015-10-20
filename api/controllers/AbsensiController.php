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
                    'absensiproduksi' => ['get'],
                    'absensioperator' => ['get'],
                    'lemburharian' => ['get'],
                    'lembur' => ['get'],
                    'penggajian' => ['get'],
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
        $kry = TblKaryawan::aktif($niknama);
        Yii::error($niknama);
        $sort = "ta.nik ASC";
        $offset = 0;
        $limit = 10;

        $query = new Query;
        $query->offset(null)
//                ->limit(10)
                ->from('tbl_absent as ta')
//                ->orderBy($sort)
                ->groupBy(['ta.nik', 'ta.ket'])
                ->select("ta.*,count(ta.ket) as countKet");
        if (isset($params['tanggal'])) {
            $test = json_decode($params['tanggal'], true);
            $start = date("Y-m-d", strtotime($test['startDate']));
            $end = date("Y-m-d", strtotime($test['endDate']));

            $query->andFilterWhere(['between', 'ta.tanggal', $start, $end]);
        }


//        Yii::error($params['nama']);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $data = [];
        foreach ($models as $key => $val) {
            $data[$val['nik']]['nik'] = $val['nik'];
            $data[$val['nik']]['nama'] = $val['nama'];
            $data[$val['nik']][str_replace(" ", "_", $val['ket'])] = $val['countKet'];
        }


        $jml_hr = $this->Hitunghr($start, $end);

        foreach ($data as $key => $val) {
            $data[$key]['nik'] = $val['nik'];
            $data[$key]['nama'] = $val['nama'];
            $data[$key]['Absent'] = (!empty($val['Absent'])) ? $val['Absent'] : '0';
            $data[$key]['Izin'] = (!empty($data[$key]['Izin'])) ? $val['Izin'] : '0';
            $data[$key]['Surat_Dokter'] = (!empty($data[$key]['Surat_Dokter'])) ? $val['Surat_Dokter'] : '0';
            $data[$key]['Sakit'] = (!empty($val['Sakit'])) ? $val['Sakit'] : '0';
            $data[$key]['Cuti'] = (!empty($val['Cuti'])) ? $val['Cuti'] : '0';
            $data[$key]['Hadir'] = ($jml_hr - $data[$key]['Absent'] - $data[$key]['Izin'] - $data[$key]['Surat_Dokter'] - $data[$key]['Sakit'] - $data[$key]['Cuti']);
        }

        $datas = [];

        foreach ($kry as $ky) {
            if (isset($data[$ky->nik])) {
                $datas[] = [
                    'nik' => $ky->nik,
                    'nama' => $ky->nama,
                    'Absen' => $data[$ky->nik]['Absent'],
                    'Izin' => $data[$ky->nik]['Izin'],
                    'Surat_Dokter' => $data[$ky->nik]['Surat_Dokter'],
                    'Sakit' => $data[$ky->nik]['Sakit'],
                    'Cuti' => $data[$ky->nik]['Cuti'],
                    'Hadir' => $data[$ky->nik]['Hadir']
                ];
            } else {
                $datas[] = [
                    'nik' => $ky->nik,
                    'nama' => $ky->nama,
                    'Absen' => '0',
                    'Izin' => '0',
                    'Surat_Dokter' => '0',
                    'Sakit' => '0',
                    'Cuti' => '0',
                    'Hadir' => $jml_hr
                ];
            }
        }
        session_start();
        $_SESSION['tglStart'] = $start;
        $_SESSION['tglEnd'] = $end;

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $datas, 'start' => $start, 'end' => $end, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function Hitunghr($day1, $day2) {

        $libur = \app\models\TblKalender::find()
                ->where(['between', 'tgl', $day1, $day2])
                ->all();
        $tglibur = [];
        foreach ($libur as $as) {
            $tglibur[] = $as['tgl'];
        }

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
        // proses menghitung tanggal merah dan hari minggu
        // di antara tanggal awal dan akhir
        for ($i = 1; $i <= $selisih; $i++) {

            // menentukan tanggal pada hari ke-i dari tanggal awal
            $tanggal = mktime(0, 0, 0, $month1, $date1 + $i, $year1);
            $tglstr = date("Y-m-d", $tanggal);

            // menghitung jumlah tanggal pada hari ke-i
            // yang masuk dalam daftar tanggal merah selain minggu
            if (!empty($tglLibur)) {
                if (in_array($tglstr, $tglLibur)) {
                    $libur1++;
                }
            }

            // menghitung jumlah tanggal pada hari ke-i
            // yang merupakan hari minggu
            if ((date("w", $tanggal) == 0)) {
                $libur2++;
            }
        }

        // menghitung selisih hari yang bukan tanggal merah dan hari minggu
        return $selisih - $libur1 - $libur2;
    }

    public function Htghr($m, $y) {

        $hasil = cal_days_in_month(CAL_GREGORIAN, $m, $y);

        return $hasil;
    }

    public function Depart($kd) {
        $qs = \app\models\Jabatan::find()
                ->where(['id_jabatan' => $kd])
                ->one();
        return $qs->jabatan;
    }

    public function actionAbsensiproduksi() {
        $params = $_REQUEST;
        $niknama = (isset($params['niknama'])) ? $params['niknama'] : '';
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
        $kry = TblKaryawan::aktif($niknama);
        $data = [];
        $i = 0;

        //taruh data karyawan
        foreach ($kry as $r) {
            if (!empty($r->status_karyawan)) {
                $data[$r->jabatan]['title'] = $this->Depart($r->jabatan);
                $data[$r->jabatan]['body'][$r->status_karyawan]['title'] = strtoupper($r->status_karyawan);
                $data[$r->jabatan]['body'][$r->status_karyawan]['subbody'][$i]['nik'] = $r->nik;
                $data[$r->jabatan]['body'][$r->status_karyawan]['subbody'][$i]['nama'] = $r->nama;

                foreach ($period as $dt) {
                    if (isset($abs[$r->nik][$dt->format("Y-m-d")]))
                        $data[$r->jabatan]['body'][$r->status_karyawan]['subbody'][$i]['tanggal'][$dt->format("Y-m-d")] = 'v';
                    else
                        $data[$r->jabatan]['body'][$r->status_karyawan]['subbody'][$i]['tanggal'][$dt->format("Y-m-d")] = '-';
                }
                $i++;
            }
        }


        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'start' => $start, 'end' => $endate, 'jmlhr' => $htghr), JSON_PRETTY_PRINT);
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

                $data[$kr->status_karyawan]['title'] = $kr->status_karyawan;
                $data[$kr->status_karyawan]['jumlah'] = $jml;
                foreach ($period as $dt) {
                    //init data
                    if (!isset($hadir[$dt->format("Y-m-d")])) {
                        $hadir[$dt->format("Y-m-d")] = 0;
                    }
                    if (!(isset($tidakhadir[$dt->format("Y-m-d")]))) {
                        $tidakhadir[$dt->format("Y-m-d")] = 0;
                    }

                    if (isset($abs[$kr->nik][$dt->format("Y-m-d")])) {
                        $jml_hadir = isset($data[$kr->status_karyawan]['hadir'][$dt->format("Y-m-d")]) ? $data[$kr->status_karyawan]['hadir'][$dt->format("Y-m-d")] + 1 : 0;
                        $hadir[$dt->format("Y-m-d")] += $jml_hadir;
                        $data[$kr->status_karyawan]['listjumlah'][$dt->format("Y-m-d")] = $jml;
                        $data[$kr->status_karyawan]['hadir'][$dt->format("Y-m-d")] = $jml_hadir;
                        $data[$kr->status_karyawan]['tdkhadir'][$dt->format("Y-m-d")] = $jml - $jml_hadir;
                    } else {
                        $jml_tak_hadir = isset($data[$kr->status_karyawan]['tdkhadir'][$dt->format("Y-m-d")]) ? $data[$kr->status_karyawan]['tdkhadir'][$dt->format("Y-m-d")] + 1 : 0;
                        $tidakhadir[$dt->format("Y-m-d")] += $jml_tak_hadir;
                        $data[$kr->status_karyawan]['listjumlah'][$dt->format("Y-m-d")] = $jml;
                        $data[$kr->status_karyawan]['hadir'][$dt->format("Y-m-d")] = $jml - $jml_tak_hadir;
                        $data[$kr->status_karyawan]['tdkhadir'][$dt->format("Y-m-d")] = $jml_tak_hadir;
                    }
                }
            }
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data, 'jmlhr' => $htghr, 'end' => $endate, 'totalhadir' => $hadir, 'totaltakhadir' => $tidakhadir), JSON_PRETTY_PRINT);
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

        //=========PROSES HITUNG ABSENT
        $ijin = TblAbsent::find()->where('tanggal>="' . $date . '" AND tanggal<="' . $date_sampai . '"')->all();
        $ijin_jml = [];
        foreach ($ijin as $arr) {
            if (!isset($ijin_jml[$arr->nik]))
                $ijin_jml[$arr->nik] = 0;

            if ($arr->ket == 'Setengah Hari') {
                $ijin_jml[$arr->nik] += 0.5;
            } else {
                $ijin_jml[$arr->nik] += 1;
            }
        }

        //=========PROSES POTONGAN
        $potongan = TblHtransPotongan::find()->where('tgl>="' . $date . '" AND tgl<="' . $date_sampai . '"')->all();
        $potongan_pinjaman = [];
        $potongan_sepatu = [];
        $potongan_oksigen = [];
        foreach ($potongan as $arr) {
            if (!isset($potongan_pinjaman[$arr->nik]))
                $potongan_pinjaman[$arr->nik] = 0;
            if (!isset($potongan_sepatu[$arr->nik]))
                $potongan_sepatu[$arr->nik] = 0;
            if (!isset($potongan_oksigen[$arr->nik]))
                $potongan_oksigen[$arr->nik] = 0;

            if ($arr->no_pot == 'POT001') { //pinjaman
                $potongan_pinjaman[$arr->nik] += $arr->total;
            } elseif ($arr->no_pot == 'POT002') { //sepatu
                $potongan_sepatu[$arr->nik] += $arr->total;
            } elseif ($arr->no_pot == 'POT003') { //oksigen
                $potongan_oksigen[$arr->nik] += 1;
            }
        }

        //=========PROSES MASUKKAN DATA
        $no = 1;
        foreach ($kry as $r) {
            $bpjs = ($r->gaji_pokok * 2.5) / 100;
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
            if (isset($potongan_sepatu[$r->nik])) { //cari potongan sepatu, dari proses di atas
                $potongan_sepatu_rp = $potongan_pinjaman[$r->nik];
            } else {
                $potongan_sepatu_rp = 0;
            }
            if (isset($potongan_oksigen[$r->nik])) { //cari potongan oksigen, dari proses di atas
                $potongan_oksigen_rp = $potongan_pinjaman[$r->nik];
            } else {
                $potongan_oksigen_rp = 0;
            }

            $ijin_rp = ($r->gaji_pokok / 25) * $ijin;
            $kotor = $r->gaji_pokok - $bpjs - $ijin_rp;
            $bersih = $kotor - $potongan_pinjaman_rp - $potongan_sepatu_rp - $potongan_oksigen_rp;

            $models[$r->nik] = ['no' => $no, 'nik' => $r->nik, 'nama' => $r->nama, 'gaji_pokok' => $r->gaji_pokok, 'ijin' => $ijin, 'ijin_rp' => $ijin_rp, 'bpjs' => $bpjs,
                'kotor' => $kotor, 'potongan_pinjaman_rp' => $potongan_pinjaman_rp, 'potongan_sepatu_rp' => $potongan_sepatu_rp, 'potongan_oksigen_rp' => $potongan_oksigen_rp, 'bersih' => $bersih, 'lembur' => $lembur[$r->nik]];
            $no++;
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
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
