<?php

namespace app\controllers;

use Yii;
use app\models\Tblkaryawan;
use app\models\Tblijazah;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class KaryawanController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'excelkeluar' => ['get'],
                    'excelmasuk' => ['get'],
                    'excelkontrak' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'rekapkeluar' => ['post'],
                    'rekapiso' => ['post'],
                    'rekapmasuk' => ['post'],
                    'rekapkontrak' => ['post'],
                    'delete' => ['delete'],
                    'cari' => ['get'],
                    'carikontrak' => ['get'],
                    'department' => ['get'],
                    'section' => ['get'],
                    'subsection' => ['get'],
                    'jabatan' => ['get'],
                    'kode' => ['get'],
                    'ijazah' => ['get'],
                    'keluar' => ['post'],
                    'upload' => ['post'],
                    'removegambar' => ['post'],
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
        $params = $_REQUEST;
        $query = new Query;

        $query->from('tbl_karyawan')
                ->select('*')
//                ->where('nik like')
                ->orderBy('nik DESC')
                ->limit(1);
        if (!empty($params['status'] && $params['status'] == 'Borongan')) {
            $query->andWhere('status_karyawan = "Borongan"');
        } else {
            $query->andWhere('status_karyawan <> "Borongan"');
        }

        $command = $query->createCommand();
        $models = $command->queryOne();
        $urut = 0;
        $kode = '';
        if (!empty($models)) {
            $urut = ((int) substr($models['nik'], -4)) + 1;
            if ($models['status_karyawan'] == "Borongan") {
                $kode = 'B' . substr('0000' . $urut, -4);
            } else {
                $kode = '0' . substr('0000' . $urut, -4);
            }
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $kode));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "nik DESC";
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
                ->from('tbl_karyawan k')
                ->join('LEFT JOIN', 'pekerjaan as ss', 'ss.kd_kerja = k.sub_section')
                ->join('LEFT JOIN', 'tbl_section as s', 's.id_section = k.section')
                ->join('LEFT JOIN', 'tbl_department as d', 'd.id_department= k.department')
                ->join('LEFT JOIN', 'tbl_jabatan as j', 'j.id_jabatan = k.jabatan')
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
        $totalItems = $query->count();

//        if (!empty($models)) {
//            $ijazah = [];
//            foreach ($models as $key => $val) {
//                $ijz = Tblijazah::find()->where(['nik' => $val['nik']])->one();
//                $ijazah = (empty($ijz)) ? [] : $ijz->attributes;
//                if (!empty($ijazah)) {
//                    foreach ($ijazah as $k => $v) {
//                        $models[$key][$k] = $v;
//                    }
//                }
//            }
//        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekapkeluar() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "nik DESC";
        $offset = 0;
        $limit = 10;

        //create query
        $query = new Query;
        $query->offset($offset)
//                ->limit($limit)
                ->from('tbl_karyawan')
                ->where('status like "%Keluar%" AND (tgl_keluar_kerja >="' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND tgl_keluar_kerja <="' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '")')
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

    public function actionRekapmasuk() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $filter = array();
        $sort = "nik DESC";
        $offset = 0;
        $limit = 10;

        //create query
        $query = new Query;
        $query->offset($offset)
//                ->limit($limit)
                ->from('tbl_karyawan')
                ->orderBy($sort)
                ->select("*");

        if ($params['tipe'] == 'kelompok') {
            $adWhere = (!empty($params['Section']['id_section'])) ? ' AND section="' . $params['Section']['id_section'] . '"' : '';
            $adWhere .= (!empty($params['lokasi_kantor'])) ? ' AND lokasi_kntr ="' . $params['lokasi_kantor'] . '"' : '';
            $query->where('(tgl_masuk_kerja >="' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND tgl_masuk_kerja <="' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '")' . $adWhere);
        } else {
            $query->where(['nik' => $params['Karyawan']['nik']]);
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

    public function actionRekapiso() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $sort = "nik DESC";
        $offset = 0;
        $limit = 10;
        $adWhere = (!empty($params['Section']['id_section'])) ? ' AND section="' . $params['Section']['id_section'] . '"' : '';
        $query = new Query;
        $query->offset($offset)
//                ->limit($limit)
                ->from('tbl_karyawan')
                ->where('(tgl_masuk_kerja <="' . date('Y-m-d', strtotime($params['tgl_end'])) . '") AND status="Kerja"' . $adWhere)
//                ->where($adWhere)
                ->orderBy($sort)
                ->select("*");
        $filter = [];
        if (!empty($params['status_karyawan'])) {
            foreach ($params['status_karyawan'] as $key => $val) {
                if ($val == true) {
                    $filter[] = $key;
                }
            }
        }

        if (!empty($filter)) {
            $query->andWhere('status_karyawan IN("' . implode('","', $filter) . '")');
        }

        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['params'] = $params;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();
        if (!empty($models)) {
            foreach ($models as $key => $val) {
                $dateWork = new \DateTime(date('Y-m-d', strtotime($val['tgl_masuk_kerja'])));
                $dateNow = new \DateTime(date('Y-m-d'));

                $models[$key]['usia'] = (!empty($val['tgl_lahir'])) ? floor((time() - strtotime(date('Y-m-d', strtotime($val['tgl_lahir'])))) / 31556926) : '';
                $models[$key]['tahun'] = floor((time() - strtotime(date('Y-m-d', strtotime($val['tgl_masuk_kerja'])))) / 31556926);

                $models[$key]['bulan'] = $dateWork->diff($dateNow)->m;
            }
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekapkontrak() {
        //init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $sort = "Kontrak_21 ASC, Kontrak_11 ASC";
        $offset = 0;

        $query = new Query;
        $query->offset($offset)
//                ->limit($limit)
                ->from('tbl_karyawan')
                ->join('LEFT JOIN', 'pekerjaan', 'tbl_karyawan.sub_section = pekerjaan.kd_kerja')
                ->where('tbl_karyawan.status_karyawan = "Kontrak" AND tbl_karyawan.status="Kerja"')
                ->orderBy($sort)
                ->select("*");
        if ($params['tipe'] == 'kelompok') {
            $adWhere = (!empty($params['Section']['id_section'])) ? ' AND section="' . $params['Section']['id_section'] . '"' : '';
            $query->andWhere('((MONTH(Kontrak_11) >="' . date('m', strtotime($params['tanggal'])) . '" AND YEAR(Kontrak_11) >="' . date('Y', strtotime($params['tanggal'])) . '" AND Kontrak_2 is NULL) OR (MONTH(Kontrak_21) >="' . date('m', strtotime($params['tanggal'])) . '" AND YEAR(Kontrak_21) >="' . date('Y', strtotime($params['tanggal'])) . '"))' . $adWhere);
        } else {
            $query->andWhere(['nik' => $params['Karyawan']['nik']]);
        }

        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['params'] = $params;

        $command = $query->createCommand();
        $models = $command->queryAll();

        if (!empty($models)) {
            foreach ($models as $key => $val) {
                $ternilai = \app\models\Tblpenilaiankontrak::find()->where([
                            'nik' => $val['nik'],
                        ])->orderBy('tgl DESC,id DESC,nm_kontrak DESC')->one();
                if (!empty($ternilai)) {
                    $models[$key]['status_penilaian'] = ($ternilai->nm_kontrak == "Kontrak 1") ? 'Kontrak 1' : 'Kontrak 2';
                    $models[$key]['tgl_penilaian'] = $ternilai->tgl;
                } else {
                    $models[$key]['status_penilaian'] = 'Belum di Nilai';
                    $models[$key]['tgl_penilaian'] = null;
                }
            }
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $department = [];
        $section = [];
        $subSection = [];
        $jabatan = [];
        $ijazah = [];

        if (!empty($model)) {

            $dep = \app\models\Department::findOne($model->department);
            $department = (empty($dep)) ? [] : $dep->attributes;
            $sec = \app\models\Section::findOne($model->section);
            $section = (empty($sec)) ? [] : $sec->attributes;
            $sub = \app\models\SubSection::findOne($model->sub_section);
            $subSection = (empty($sub)) ? [] : $sub->attributes;
            $jab = \app\models\Jabatan::findOne($model->jabatan);
            $jabatan = (empty($jab)) ? [] : $jab->attributes;
            $ijz = Tblijazah::find()->where(['nik' => $model->nik])->one();
            $ijazah = (empty($ijz)) ? [] : $ijz->attributes;
            $ket = Tblkaryawan::find()->where(['nik' => $model->nik_ketua])->one();
            $ketua = (empty($ket)) ? [] : $ket->attributes;
        }
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'ijazah' => $ijazah, 'ketua' => $ketua, 'department' => $department, 'section' => $section, 'subSection' => $subSection, 'jabatan' => $jabatan), JSON_PRETTY_PRINT);
    }

    public function actionUpload() {
        if (!empty($_FILES)) {
            $tempPath = $_FILES['file']['tmp_name'];
            $newName = \Yii::$app->landa->urlParsing($_FILES['file']['name']);

            $uploadPath = \Yii::$app->params['pathImg'] . $_GET['folder'] . DIRECTORY_SEPARATOR . $newName;

            move_uploaded_file($tempPath, $uploadPath);
            $a = \Yii::$app->landa->createImg($_GET['folder'] . '/', $newName, $_POST['nik']);

            $answer = array('answer' => 'File transfer completed', 'name' => $newName);
            if ($answer['answer'] == "File transfer completed") {
                $karyawan = Tblkaryawan::findOne($_POST['nik']);
                $foto = json_decode($karyawan->foto, true);
                $foto[] = array('name' => $newName);
                $karyawan->foto = json_encode($foto);
                $karyawan->save();
            }

            echo json_encode($answer);
        } else {
            echo 'No files';
        }
    }

    public function actionRemovegambar() {
        $params = json_decode(file_get_contents("php://input"), true);
        $barang = Tblkaryawan::findOne($params['nik']);
        $foto = json_decode($barang->foto, true);
        foreach ($foto as $key => $val) {
            if ($val['name'] == $params['nama']) {
                unset($foto[$key]);
                \Yii::$app->landa->deleteImg('barang/', $params['nik'], $params['nama']);
            }
        }
        $barang->foto = json_encode($foto);
        $barang->save();

        echo json_encode($foto);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Tblkaryawan();
        $model->attributes = $params;
        $model->status = "Kerja";
        $model->department = (!empty($params['Department']['id_department'])) ? $params['Department']['id_department'] : null;
        $model->section = (!empty($params['Section']['id_section'])) ? $params['Section']['id_section'] : null;
        $model->sub_section = (!empty($params['SubSection']['kd_kerja'])) ? $params['SubSection']['kd_kerja'] : null;
        $model->jabatan = (!empty($params['Jabatan']['id_jabatan'])) ? $params['Jabatan']['id_jabatan'] : null;
        $model->tgl_masuk_kerja = (!empty($params['tgl_masuk_kerja'])) ? date('Y-m-d', strtotime($params['tgl_masuk_kerja'])) : null;
        $model->Kontrak_1 = (!empty($params['Kontrak_1'])) ? date('Y-m-d', strtotime($params['Kontrak_1'])) : null;
        $model->Kontrak_11 = (!empty($params['Kontrak_11'])) ? date('Y-m-d', strtotime($params['Kontrak_11'])) : null;
        $model->Kontrak_2 = (!empty($params['Kontrak_2'])) ? date('Y-m-d', strtotime($params['Kontrak_2'])) : null;
        $model->Kontrak_21 = (!empty($params['Kontrak_21'])) ? date('Y-m-d', strtotime($params['Kontrak_21'])) : null;
        $model->tgl_lahir = (!empty($params['tgl_lahir'])) ? date('Y-m-d', strtotime($params['tgl_lahir'])) : null;
        $model->tgl_keluar_kerja = null;
        $model->alasan_keluar = null;


        if ($model->save()) {
            $ijazah = new Tblijazah();
            $ijazah->attributes = $params;
            $ijazah->atas_nama = $params['nama'];
            $ijazah->tgl_ijazah = (!empty($params['tgl_ijazah'])) ? date('Y-m-d', strtotime($params['tgl_ijazah'])) : null;
            $ijazah->tgl_masuk = (!empty($params['tgl_masuk'])) ? date('Y-m-d', strtotime($params['tgl_masuk'])) : null;
            $ijazah->nama_sekolah = (!empty($params['sekolah'])) ? $params['sekolah'] : null;
            $ijazah->status = 'Masuk';
            $ijazah->tempat_lahir = (!empty($params['tmpt_lahir'])) ? $params['tmpt_lahir'] : null;

            if ($ijazah->save()) {
                $this->setHeader(200);
                echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
            }
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($id);
        $model->attributes = $params;
        $model->department = (!empty($params['Department']['id_department'])) ? $params['Department']['id_department'] : null;
        $model->section = (!empty($params['Section']['id_section'])) ? $params['Section']['id_section'] : null;
        $model->sub_section = (!empty($params['SubSection']['kd_kerja'])) ? $params['SubSection']['kd_kerja'] : null;
        $model->jabatan = (!empty($params['Jabatan']['id_jabatan'])) ? $params['Jabatan']['id_jabatan'] : null;
        $model->tgl_masuk_kerja = (!empty($params['tgl_masuk_kerja'])) ? date('Y-m-d', strtotime($params['tgl_masuk_kerja'])) : null;
        $model->Kontrak_1 = (!empty($params['Kontrak_1'])) ? date('Y-m-d', strtotime($params['Kontrak_1'])) : null;
        $model->Kontrak_11 = (!empty($params['Kontrak_11'])) ? date('Y-m-d', strtotime($params['Kontrak_11'])) : null;
        if (!empty($params['Kontrak_2']) && date('Y-m-d', strtotime($model->Kontrak_11)) <= date('Y-m-d', strtotime($params['Kontrak_2']))) {
            $model->Kontrak_2 = (!empty($params['Kontrak_2'])) ? date('Y-m-d', strtotime($params['Kontrak_2'])) : null;
            $model->Kontrak_21 = (!empty($params['Kontrak_21'])) ? date('Y-m-d', strtotime($params['Kontrak_21'])) : null;
        }


        $model->tgl_lahir = (!empty($params['tgl_lahir'])) ? date('Y-m-d', strtotime($params['tgl_lahir'])) : null;

        if ($model->save()) {
            if (!empty($params['no'])) {
                $ijazah = Tblijazah::findOne($params['no']);
                if (empty($ijazah))
                    $ijazah = new Tblijazah();

                $ijazah->attributes = $params;
                $ijazah->atas_nama = (!empty($params['nama'])) ? $params['nama'] : null;
                $ijazah->tgl_ijazah = (!empty($params['tgl_ijazah'])) ? date('Y-m-d', strtotime($params['tgl_ijazah'])) : null;
                $ijazah->tgl_masuk = (!empty($params['tgl_masuk'])) ? date('Y-m-d', strtotime($params['tgl_masuk'])) : null;
                $ijazah->nama_sekolah = (!empty($params['sekolah'])) ? $params['sekolah'] : null;
                $ijazah->status = 'Masuk';
                $ijazah->tempat_lahir = (!empty($params['tmpt_lahir'])) ? $params['tmpt_lahir'] : null;

                $ijazah->save();
            }

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionKeluar($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($id);
        $model->status = 'Keluar';
        $model->tgl_keluar_kerja = date('Y-m-d', strtotime($params['form']['tgl_keluar_kerja']));
        $model->alasan_keluar = $params['form']['alasan_keluar'];

        if ($model->save()) {
            if (!empty($params['no'])) {
                $ijazah = Tblijazah::findOne($params['no']);
                $ijazah->tgl_keluar = date('Y-m-d', strtotime($model->tgl_keluar_kerja));
                $ijazah->status = 'Keluar';

                $ijazah->save();
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
        if (($model = Tblkaryawan::findOne($id)) !== null) {
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
        return $this->render("/expmaster/karyawan", ['models' => $models]);
    }

    public function actionExcelmasuk() {
        session_start();
        $query = $_SESSION['query'];
        $params = $_SESSION['params'];
        $start = $params['tanggal']['startDate'];
        $end = $params['tanggal']['endDate'];
        $section = (!empty($params['Section']['section'])) ? $params['Section']['section'] : '';
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        $rekap = (!empty($_GET['rekap'])) ? $_GET['rekap'] : '';
        if (!empty($models)) {
            foreach ($models as $key => $val) {
                $dateWork = new \DateTime(date('Y-m-d', strtotime($val['tgl_masuk_kerja'])));
                $dateNow = new \DateTime(date('Y-m-d'));

                $models[$key]['usia'] = (!empty($val['tgl_lahir'])) ? floor((time() - strtotime(date('Y-m-d', strtotime($val['tgl_lahir'])))) / 31556926) : '';
                $models[$key]['tahun'] = floor((time() - strtotime(date('Y-m-d', strtotime($val['tgl_masuk_kerja'])))) / 31556926);

                $models[$key]['bulan'] = $dateWork->diff($dateNow)->m;
            }
        }

        return $this->render("/exprekap/" . $rekap, ['models' => $models, 'start' => $start, 'end' => $end, 'section' => $section]);
    }

    public function actionExcelkontrak() {
        session_start();
        $query = $_SESSION['query'];
        $params = $_SESSION['params'];
        $tanggal = $params['tanggal'];
        $section = (!empty($params['Section']['section'])) ? $params['Section']['section'] : '';
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        if (!empty($models)) {
            foreach ($models as $key => $val) {
                $ternilai = \app\models\Tblpenilaiankontrak::find()->where([
                            'nik' => $val['nik'],
                        ])->orderBy('tgl DESC,id DESC,nm_kontrak DESC')->one();
                if (!empty($ternilai)) {
                    $models[$key]['status_penilaian'] = ($ternilai->nm_kontrak == "Kontrak 1") ? 'Kontrak 1' : 'Kontrak 2';
                    $models[$key]['tgl_penilaian'] = $ternilai->tgl;
                } else {
                    $models[$key]['status_penilaian'] = 'Belum di Nilai';
                    $models[$key]['tgl_penilaian'] = null;
                }
            }
        }

        $rekap = (!empty($_GET['rekap'])) ? $_GET['rekap'] : '';
        return $this->render("/exprekap/" . $rekap, ['models' => $models, 'tanggal' => $tanggal, 'section' => $section]);
    }

    public function actionExcelkeluar() {
        session_start();
        $query = $_SESSION['query'];
        $params = $_SESSION['params'];
        $start = $params['tanggal']['startDate'];
        $end = $params['tanggal']['endDate'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/exprekap/karyawankeluar", ['models' => $models, 'start' => $start, 'end' => $end]);
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan as kar')
                ->join('LEFT JOIN', 'tbl_section as sec', 'sec.id_section = kar.section')
                ->join('LEFT JOIN', 'pekerjaan as sub', 'sub.kd_kerja = kar.sub_section')
                ->join('LEFT JOIN', 'tbl_department as dep', 'dep.id_department = kar.department')
                ->join('LEFT JOIN', 'tbl_jabatan as jab', 'jab.id_jabatan= kar.jabatan')
                ->select("*,sub.kerja as subSection,kar.nik, kar.nama,jab.jabatan,dep.department,sub.kerja as sub_section,sec.section")
                ->where('kar.nik like "%' . $params['nama'] . '%" OR kar.nama like "%' . $params['nama'] . '%" AND kar.status="Kerja"')
                ->limit(10);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionCarikontrak() {

        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan as kar')
                ->join('LEFT JOIN', 'tbl_section as sec', 'sec.id_section = kar.section')
                ->join('LEFT JOIN', 'pekerjaan as sub', 'sub.kd_kerja = kar.sub_section')
                ->join('LEFT JOIN', 'tbl_department as dep', 'dep.id_department = kar.department')
                ->join('LEFT JOIN', 'tbl_jabatan as jab', 'jab.id_jabatan= kar.jabatan')
                ->select("kar.nik, kar.nama,jab.jabatan,dep.department,sub.kerja as sub_section,sec.section")
                ->where('kar.nik like "%' . $params['nama'] . '%" OR kar.nama like "%' . $params['nama'] . '%" AND status_karyawan like "%Kontrak%"')
                ->limit(10);

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionIjazah() {
        $params = $_REQUEST;
        $query = new Query();

        $query->select('*')
                ->from('tbl_ijazah')
                ->join('LEFT JOIN', 'tbl_karyawan', 'tbl_karyawan.nik = tbl_ijazah.nik')
                ->limit(10);

        $execute = $query->createCommand();
        $models = $execute->queryOne();

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}
