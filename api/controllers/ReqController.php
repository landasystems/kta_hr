<?php

namespace app\controllers;

use Yii;
use app\models\TblKaryawan;
use app\models\Tblijazah;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class ReqController extends Controller {

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
                    'tes' => ['get'],
                    'tes2' => ['get'],
                    'data' => ['get'],
                    'excelkontrak' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'rekapkeluar' => ['post'],
                    'rekap-ulang-tahun' => ['post'],
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
                    'urut-jabatan' => ['get'],
                    'kode' => ['get'],
                    'det-karyawan' => ['get'],
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

        if (!empty($models)) {
            $ijazah = [];
            foreach ($models as $key => $val) {
                $ijz = Tblijazah::find()->where(['nik' => $val['nik']])->one();
                $ijazah = (empty($ijz)) ? [] : $ijz->no;
                $models[$key] = $val;
                $models[$key]['status'] = ucfirst(strtolower($val['status']));
                $models[$key]['agama'] = ucfirst(strtolower($val['agama']));
                if (!empty($ijazah)) {
                    $models[$key]['no'] = $ijazah;
                }
            }
        }

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionData() {
        set_time_limit(500);
        $query1 = new Query;
        $query1->select("*")
                ->from('tbl_karyawan k')
                ->where(['status_karyawan' => 'Kontrak'])
                ->andWhere(['status' => 'Kerja'])
                ->orderBy("k.nik ASC");
        $command1 = $query1->createCommand();
        $model1 = $command1->queryAll();

        $no_ktp = [];
        foreach ($model1 as $val) {
            $no_ktp[] = $val['no_ktp'];
        }

        $query2 = new Query;
        $query2->select("*")
                ->from('tbl_karyawan k')
                ->join('LEFT JOIN', 'pekerjaan as ss', 'ss.kd_kerja = k.sub_section')
                ->join('LEFT JOIN', 'tbl_section as s', 's.id_section = k.section')
                ->join('LEFT JOIN', 'tbl_department as d', 'd.id_department= k.department')
                ->join('LEFT JOIN', 'tbl_jabatan as j', 'j.id_jabatan = k.jabatan')
                ->where(['no_ktp' => $no_ktp])
                ->orderBy("k.nama,k.nik ASC");
        $command2 = $query2->createCommand();
        $model = $command2->queryAll();

        $array = [];
        $reff = '';
        $periode = '';
        $no = 0;
        foreach ($model as $key => $val) {
            $array[$key] = $val;
            $nama = ($reff == $val['no_ktp']) ? "" : $val['nama'];

            $awal_kontrak1 = isset($val['Kontrak_1']) ? date("d-m-Y", strtotime($val['Kontrak_1'])) : "";
            $akhir_kontrak1 = isset($val['Kontrak_11']) ? date("d-m-Y", strtotime($val['Kontrak_11'])) : "";
            $awal_kontrak2 = isset($val['Kontrak_2']) ? date("d-m-Y", strtotime($val['Kontrak_2'])) : "";
            $akhir_kontrak2 = isset($val['Kontrak_21']) ? date("d-m-Y", strtotime($val['Kontrak_21'])) : "";

            if (isset($awal_kontrak1) && !empty($awal_kontrak1)) {
                $periode = $this->hitung($val['Kontrak_1'], $val['Kontrak_11']);
            }else{
                $periode = '';
            }

            if (isset($awal_kontrak2) && !empty($awal_kontrak2)) {
               $periode = $this->hitung($val['Kontrak_1'], $val['Kontrak_21']);
            }
            
            $reff = $val['no_ktp'];

            $array[$key]['nama'] = $nama;
            $array[$key]['periode'] = $periode;
        }

        $path = \Yii::$app->params['path'] . 'api/templates/req.xls';
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objPHPExcel = $objReader->load($path);

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

        $border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                )
            ),
        );

        $baseRow = 3;
        foreach ($array as $r => $arr) {
            if (isset($row))
                $row++;
            else
                $row = $baseRow + $r;


            $awal_kontrak1 = isset($arr['Kontrak_1']) ? date("d-m-Y", strtotime($arr['Kontrak_1'])) : "";
            $akhir_kontrak1 = isset($arr['Kontrak_11']) ? date("d-m-Y", strtotime($arr['Kontrak_11'])) : "";
            $awal_kontrak2 = isset($arr['Kontrak_2']) ? date("d-m-Y", strtotime($arr['Kontrak_2'])) : "";
            $akhir_kontrak2 = isset($arr['Kontrak_21']) ? date("d-m-Y", strtotime($arr['Kontrak_21'])) : "";
 

            $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
            $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':H' . $row)->applyFromArray($background);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('A' . $row, $arr['nama'])
                    ->setCellValue('B' . $row, $arr['nik'])
                    ->setCellValue('C' . $row, $arr['section'])
                    ->setCellValue('D' . $row, $awal_kontrak1)
                    ->setCellValue('E' . $row, $akhir_kontrak1)
                    ->setCellValue('F' . $row, $awal_kontrak2)
                    ->setCellValue('G' . $row, $akhir_kontrak2)
                    ->setCellValue('H' . $row,  $arr['periode']);
        }
        header("Content-type: application/vnd-ms-excel");
        header('Content-Disposition: attachment;filename="karyawan-aktif.xlsx"');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

//echo json_encode(array_filter($array));
    }

    public function hitung($tgl_awal, $tgl_akhir) {

        $tgl_awl = date("Y-m-d", strtotime($tgl_awal));
        $tgl_akr = date("Y-m-d", strtotime($tgl_akhir));
        $datetime1 = new \DateTime($tgl_awl);
        $datetime1->setTimezone(new \DateTimeZone('Asia/Jakarta'));

        $datetime2 = new \DateTime($tgl_akr);
        $datetime2->setTimezone(new \DateTimeZone('Asia/Jakarta'));
        $difference = $datetime1->diff($datetime2);


        return $difference->m + ($datetime1->diff($datetime2)->y*12);
    }

    public function actionTes() {
        set_time_limit(500);
        $query = new Query;
        $query->select("*")
                ->from('tbl_karyawan k')
                ->join('LEFT JOIN', 'pekerjaan as ss', 'ss.kd_kerja = k.sub_section')
                ->join('LEFT JOIN', 'tbl_section as s', 's.id_section = k.section')
                ->join('LEFT JOIN', 'tbl_department as d', 'd.id_department= k.department')
                ->join('LEFT JOIN', 'tbl_jabatan as j', 'j.id_jabatan = k.jabatan')
                ->where(['status_karyawan' => 'Kontrak'])
                ->andWhere(['status' => 'Kerja'])
                ->orderBy("k.nik ASC");
//        
//        if($params['status'] = 'aktif'){
//            $query->andWhere(['status' => 'Kerja']);
//        }else{
//            $query->andWhere(['status' => 'Keluar']);
//        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $path = \Yii::$app->params['path'] . 'api/templates/tes2.xls';
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
        $baseRow = 3;

        $no = 0;
        foreach ($models as $r => $arr) {
            if (!empty($arr['nik_ketua'])) {
                $tbl = TblKaryawan::findOne(['nik' => $arr['nik_ketua']]);
                $ketua = $tbl->nama;
            } else {
                $ketua = '';
            }
            if (isset($row))
                $row++;
            else
                $row = $baseRow + $r;
////////
            $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
            $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($background);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $r + 1);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('B' . $row, $arr['nama'])
                    ->setCellValue('C' . $row, $arr['section'])
                    ->setCellValue('D' . $row, $arr['Kontrak_1'])
                    ->setCellValue('E' . $row, $arr['Kontrak_11'])
                    ->setCellValue('F' . $row, $arr['Kontrak_2'])
                    ->setCellValue('G' . $row, $arr['Kontrak_21']);
//            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $arr['nik']);
//            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['nama']);
//            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['initial']);
//            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $arr['section']);
//            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $arr['Kontrak_1']);
//            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $arr['Kontrak_11']);
//            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $arr['Kontrak_2']);
//            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $arr['Kontrak_21']);
//            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $arr['status_kepemilikan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $arr['status_karyawan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $arr['department']);
//            $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $arr['section']);
//            $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $arr['kerja']);
//            $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $arr['status_kelompok']);
//            $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $ketua);
//            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $arr['jabatan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('R' . $row, $arr['lokasi_kntr']);
//            $objPHPExcel->getActiveSheet()->setCellValue('S' . $row, $arr['pendidikan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('T' . $row, $arr['sekolah']);
//            $objPHPExcel->getActiveSheet()->setCellValue('U' . $row, $arr['jurusan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('V' . $row, $arr['no_ijazah']);
//            $objPHPExcel->getActiveSheet()->setCellValue('W' . $row, $arr['tmpt_lahir']);
//            $objPHPExcel->getActiveSheet()->setCellValue('X' . $row, $arr['tgl_lahir']);
//            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $row, $arr['alamat_jln']);
//            $objPHPExcel->getActiveSheet()->setCellValue('Z' . $row, $arr['rt']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row, $arr['rw']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AB' . $row, $arr['desa']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AC' . $row, $arr['kecamatan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AD' . $row, $arr['kabupaten']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AE' . $row, $arr['kode_pos']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AF' . $row, $arr['no_ktp']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AG' . $row, $arr['agama']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AH' . $row, $arr['status_pernikahan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AI' . $row, $arr['kewarganegaraan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AJ' . $row, $arr['tgl_masuk_kerja']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AK' . $row, $arr['kode_bank']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AL' . $row, $arr['nama_bank']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AM' . $row, $arr['gaji_pokok']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AN' . $row, $arr['t_fungsional']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AO' . $row, $arr['t_kehadiran']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AP' . $row, $arr['thp']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AQ' . $row, $arr['mgm']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AR' . $row, $arr['upah_tetap']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AS' . $row, $arr['pesangon']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AT' . $row, $arr['t_masa_kerja']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AU' . $row, $arr['penggantian_hak']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AF' . $row, $arr['normatif']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AW' . $row, $arr['jk']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AX' . $row, $arr['tgl_keluar_kerja']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AY' . $row, $arr['alasan_keluar']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AZ' . $row, $arr['status']);
//            $objPHPExcel->getActiveSheet()->setCellValue('BA' . $row, $arr['ket']);
//            $objPHPExcel->getActiveSheet()->setCellValue('BB' . $row, $arr['no_polis']);
//            $objPHPExcel->getActiveSheet()->setCellValue('BC' . $row, $arr['nm_asuransi']);
//            $objPHPExcel->getActiveSheet()->setCellValue('BD' . $row, $arr['no_npwp']);
//            $objPHPExcel->getActiveSheet()->setCellValue('BE' . $row, $arr['no_pasport']);
//                  
        }
//
        header("Content-type: application/vnd-ms-excel");
        header('Content-Disposition: attachment;filename="karyawan-kontrak-aktif.xlsx"');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function actionTes2() {
        set_time_limit(500);
        $query = new Query;
        $query->select("*")
                ->from('tbl_karyawan k')
                ->join('LEFT JOIN', 'pekerjaan as ss', 'ss.kd_kerja = k.sub_section')
                ->join('LEFT JOIN', 'tbl_section as s', 's.id_section = k.section')
                ->join('LEFT JOIN', 'tbl_department as d', 'd.id_department= k.department')
                ->join('LEFT JOIN', 'tbl_jabatan as j', 'j.id_jabatan = k.jabatan')
                ->where(['status_karyawan' => 'Kontrak'])
                ->andWhere(['status' => 'Keluar'])
                ->orderBy("k.nik ASC");

//        if($params['status'] = 'aktif'){
//            $query->andWhere(['status' => 'Kerja']);
//        }else{
//            $query->andWhere(['status' => 'Keluar']);
//        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $path = \Yii::$app->params['path'] . 'api/templates/tes2.xls';
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
        $baseRow = 3;

        $no = 0;
        foreach ($models as $r => $arr) {
            if (!empty($arr['nik_ketua'])) {
                $tbl = TblKaryawan::findOne(['nik' => $arr['nik_ketua']]);
                $ketua = $tbl->nama;
            } else {
                $ketua = '';
            }
            if (isset($row))
                $row++;
            else
                $row = $baseRow + $r;
////////
            $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
            $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($background);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $r + 1);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('B' . $row, $arr['nama'])
                    ->setCellValue('C' . $row, $arr['section'])
                    ->setCellValue('D' . $row, $arr['Kontrak_1'])
                    ->setCellValue('E' . $row, $arr['Kontrak_11'])
                    ->setCellValue('F' . $row, $arr['Kontrak_2'])
                    ->setCellValue('G' . $row, $arr['Kontrak_21']);
//            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $arr['nik']);
//            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['nama']);
//            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['initial']);
//            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $arr['section']);
//            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $arr['Kontrak_1']);
//            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $arr['Kontrak_11']);
//            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $arr['Kontrak_2']);
//            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $arr['Kontrak_21']);
//            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $arr['status_kepemilikan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $arr['status_karyawan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $arr['department']);
//            $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $arr['section']);
//            $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $arr['kerja']);
//            $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $arr['status_kelompok']);
//            $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $ketua);
//            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $arr['jabatan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('R' . $row, $arr['lokasi_kntr']);
//            $objPHPExcel->getActiveSheet()->setCellValue('S' . $row, $arr['pendidikan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('T' . $row, $arr['sekolah']);
//            $objPHPExcel->getActiveSheet()->setCellValue('U' . $row, $arr['jurusan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('V' . $row, $arr['no_ijazah']);
//            $objPHPExcel->getActiveSheet()->setCellValue('W' . $row, $arr['tmpt_lahir']);
//            $objPHPExcel->getActiveSheet()->setCellValue('X' . $row, $arr['tgl_lahir']);
//            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $row, $arr['alamat_jln']);
//            $objPHPExcel->getActiveSheet()->setCellValue('Z' . $row, $arr['rt']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row, $arr['rw']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AB' . $row, $arr['desa']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AC' . $row, $arr['kecamatan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AD' . $row, $arr['kabupaten']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AE' . $row, $arr['kode_pos']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AF' . $row, $arr['no_ktp']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AG' . $row, $arr['agama']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AH' . $row, $arr['status_pernikahan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AI' . $row, $arr['kewarganegaraan']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AJ' . $row, $arr['tgl_masuk_kerja']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AK' . $row, $arr['kode_bank']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AL' . $row, $arr['nama_bank']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AM' . $row, $arr['gaji_pokok']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AN' . $row, $arr['t_fungsional']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AO' . $row, $arr['t_kehadiran']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AP' . $row, $arr['thp']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AQ' . $row, $arr['mgm']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AR' . $row, $arr['upah_tetap']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AS' . $row, $arr['pesangon']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AT' . $row, $arr['t_masa_kerja']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AU' . $row, $arr['penggantian_hak']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AF' . $row, $arr['normatif']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AW' . $row, $arr['jk']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AX' . $row, $arr['tgl_keluar_kerja']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AY' . $row, $arr['alasan_keluar']);
//            $objPHPExcel->getActiveSheet()->setCellValue('AZ' . $row, $arr['status']);
//            $objPHPExcel->getActiveSheet()->setCellValue('BA' . $row, $arr['ket']);
//            $objPHPExcel->getActiveSheet()->setCellValue('BB' . $row, $arr['no_polis']);
//            $objPHPExcel->getActiveSheet()->setCellValue('BC' . $row, $arr['nm_asuransi']);
//            $objPHPExcel->getActiveSheet()->setCellValue('BD' . $row, $arr['no_npwp']);
//            $objPHPExcel->getActiveSheet()->setCellValue('BE' . $row, $arr['no_pasport']);
//                  
        }
//
        header("Content-type: application/vnd-ms-excel");
        header('Content-Disposition: attachment;filename="karyawan-kontrak-tidak-aktif.xlsx"');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
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
                ->join('LEFT JOIN', 'tbl_jabatan', 'tbl_jabatan.id_jabatan = tbl_karyawan.jabatan')
                ->where('status like "%Keluar%" AND (tgl_keluar_kerja >="' . date('Y-m-d', strtotime($params['tanggal']['startDate'])) . '" AND tgl_keluar_kerja <="' . date('Y-m-d', strtotime($params['tanggal']['endDate'])) . '")')
                ->orderBy($sort)
                ->select("tbl_karyawan.*,tbl_jabatan.jabatan as jabat");

        if (!empty($params['status'])) {
            $query->andWhere(['status_karyawan' => $params['status']]);
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
                ->join('LEFT JOIN', 'tbl_jabatan', 'tbl_jabatan.id_jabatan = tbl_karyawan.jabatan')
                ->join('LEFT JOIN', 'tbl_department', 'tbl_department.id_department = tbl_karyawan.department')
                ->join('LEFT JOIN', 'tbl_section', 'tbl_section.id_section = tbl_karyawan.section')
                ->orderBy($sort)
                ->select("tbl_karyawan.*,tbl_jabatan.jabatan as nama_jabatan, tbl_section.section as nama_section, tbl_department.department as nama_dept");

        if ($params['tipe'] == 'kelompok') {
            $adWhere = (!empty($params['Section']['id_section'])) ? ' AND section="' . $params['Section']['id_section'] . '"' : '';
            $adWhere .= (!empty($params['status'])) ? ' AND tbl_karyawan.status_karyawan="' . $params['status'] . '"' : '';
            $adWhere .= (!empty($params['lokasi_kantor'])) ? ' AND lokasi_kntr ="' . $params['lokasi_kantor'] . '"' : '';
            $adWhere .= (!empty($params['Jabatan']['id_department'])) ? ' AND tbl_karyawan.department="' . $params['Department']['id_department'] . '"' : '';
//           Yii::error($adWhere);
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

    public function actionRekapUlangTahun() {
//init variable
        $params = json_decode(file_get_contents("php://input"), true);
        $sort = "day(tbl_karyawan.tgl_lahir) ASC";
        $offset = 0;

//create query
        $query = new Query;
        $query->offset($offset)
//                ->limit($limit)
                ->from('tbl_karyawan')
                ->join('LEFT JOIN', 'tbl_jabatan', 'tbl_jabatan.id_jabatan = tbl_karyawan.jabatan')
                ->join('LEFT JOIN', 'tbl_department', 'tbl_department.id_department = tbl_karyawan.department')
                ->join('LEFT JOIN', 'tbl_section', 'tbl_section.id_section = tbl_karyawan.section')
                ->where('(MONTH(tbl_karyawan.tgl_lahir) >="' . $params['tanggal'] . '" AND MONTH(tbl_karyawan.tgl_lahir) <="' . $params['tanggal'] . '") and tbl_karyawan.status like "kerja"')
                ->orderBy($sort)
                ->select("tbl_karyawan.*,tbl_jabatan.jabatan as nama_jabatan, tbl_section.section as nama_section, tbl_department.department as nama_dept");


        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['params'] = $params;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        if (!empty($models)) {
            foreach ($models as $key => $val) {
                if (!empty($val['tgl_lahir'])) {
                    $date1 = date_create($val['tgl_lahir']);
                    $date2 = date_create(date('Y-m-d'));
                    $diff = date_diff($date1, $date2);
                    $models[$key]['usia'] = round(($diff->days) / 365) . ' tahun';
                }
            }
        }

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
        $adWhere .= (!empty($params['Department']['id_department'])) ? ' AND department="' . $params['Department']['id_department'] . '"' : '';
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
                ->from('tbl_karyawan')
                ->join('LEFT JOIN', 'tbl_jabatan', 'tbl_karyawan.jabatan = tbl_jabatan.id_jabatan')
                ->where('tbl_karyawan.status_karyawan like "Kontrak" AND tbl_karyawan.status like "Kerja"')
                ->orderBy($sort)
                ->select("*");
        if ($params['tipe'] == 'kelompok') {
            $adWhere = (!empty($params['Section']['id_section'])) ? ' AND section="' . $params['Section']['id_section'] . '"' : '';
            $adWhere .= (!empty($params['Jabatan']['id_jabatan'])) ? ' AND tbl_karyawan.jabatan="' . $params['Jabatan']['id_jabatan'] . '"' : '';
            $adWhere .= (!empty($params['Department']['id_department'])) ? ' AND tbl_karyawan.department="' . $params['Department']['id_department'] . '"' : '';

            if ($params['tipe_periode'] == 'rentang')
//                $query->andWhere('((Kontrak_11 >= "' . date('Y-m-d', strtotime($params['tanggal_rentang']['startDate'])) . '" AND Kontrak_11 <= "' . date('Y-m-d', strtotime($params['tanggal_rentang']['endDate'])) . '" ) OR (Kontrak_21 >= "' . date('Y-m-d', strtotime($params['tanggal_rentang']['startDate'])) . '" AND Kontrak_21 <= "' . date('Y-m-d', strtotime($params['tanggal_rentang']['endDate'])) . '"))' . $adWhere);
                $query->andWhere('((Kontrak_11 >= "' . date('Y-m-d', strtotime($params['tanggal_rentang']['startDate'])) . '" AND Kontrak_11 <= "' . date('Y-m-d', strtotime($params['tanggal_rentang']['endDate'])) . '" AND Kontrak_2 is NULL) OR (Kontrak_21 >= "' . date('Y-m-d', strtotime($params['tanggal_rentang']['startDate'])) . '" AND Kontrak_21 <= "' . date('Y-m-d', strtotime($params['tanggal_rentang']['endDate'])) . '"))' . $adWhere);
            else
//                $query->andWhere('((MONTH(Kontrak_11) >="' . date('m', strtotime($params['tanggal'])) . '" AND YEAR(Kontrak_11) >="' . date('Y', strtotime($params['tanggal'])) . '" ) OR (MONTH(Kontrak_21) >="' . date('m', strtotime($params['tanggal'])) . '" AND YEAR(Kontrak_21) >="' . date('Y', strtotime($params['tanggal'])) . '"))' . $adWhere);
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
            if (!empty($ketua)) {
                $ketua['agama'] = ucfirst(strtolower($ket->agama));
//                foreach($ketua as $key => $val){
//                    $ketua[$key] = $val;
//                    $ketua[$key]['agama'] = ucfirst(strtolower($val['agama']));
//                }
            }
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
        $model->nik_ketua = (!empty($params['Ketua']['nik'])) ? $params['Ketua']['nik'] : null;
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
        $model->nik_ketua = (!empty($params['Ketua']['nik'])) ? $params['Ketua']['nik'] : null;
        $model->tgl_masuk_kerja = (!empty($params['tgl_masuk_kerja'])) ? date('Y-m-d', strtotime($params['tgl_masuk_kerja'])) : null;
        $model->Kontrak_1 = (!empty($params['Kontrak_1'])) ? date('Y-m-d', strtotime($params['Kontrak_1'])) : null;
        $model->Kontrak_11 = (!empty($params['Kontrak_11'])) ? date('Y-m-d', strtotime($params['Kontrak_11'])) : null;
        if (!empty($params['Kontrak_2']) && date('Y-m-d', strtotime($model->Kontrak_11)) <= date('Y-m-d', strtotime($params['Kontrak_2']))) {
            $model->Kontrak_2 = (!empty($params['Kontrak_2'])) ? date('Y-m-d', strtotime($params['Kontrak_2'])) : null;
            $model->Kontrak_21 = (!empty($params['Kontrak_21'])) ? date('Y-m-d', strtotime($params['Kontrak_21'])) : null;
        }


        $model->tgl_lahir = (!empty($params['tgl_lahir'])) ? date('Y-m-d', strtotime($params['tgl_lahir'])) : null;
        if ($model->validate()) {
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
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionKeluar() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        $model = $this->findModel($params['form']['nik']);
        $model->status = 'Keluar';
        $model->tgl_keluar_kerja = date('Y-m-d', strtotime($params['form']['tgl_keluar_kerja']));
        $model->alasan_keluar = $params['form']['alasan_keluar'];

        if ($model->save()) {
            if (!empty($params['form']['no'])) {
                $ijazah = Tblijazah::find()->where(['no' => $params['form']['no']])->one();
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

        if (isset($_GET['print'])) {
            return $this->render("/expmaster/karyawan", ['data' => $models]);
        } else {
            $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/master-karyawan.xls';
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
                set_time_limit(40);
                if (isset($row))
                    $row++;
                else
                    $row = $baseRow + $r;

                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $arr['nik']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $arr['nama']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['status_karyawan']);
                $objPHPExcel->getActiveSheet()->mergeCells("D{$row}:E{$row}")->setCellValue('D' . $row, $arr['jabatan']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $arr['lokasi_kntr']);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $arr['status']);
            }

            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="master-karyawan.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
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
        if ($rekap == "karyawaniso") {
            if (!isset($_GET['print'])) {
                $data = array();
                $i = 0;

                $path = \Yii::$app->params['path'] . 'api/templates/karyawan-iso.xls';
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
                $baseRow = 11;
//
                $objPHPExcel->getActiveSheet()->setCellValue('Q5', " Dicetak :  " . date("d F Y"));
                $objPHPExcel->getActiveSheet()->mergeCells('I7:L7')->setCellValue('I7', " PERIODE :  " . date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)));
                $objPHPExcel->getActiveSheet()->mergeCells('I8:L8')->setCellValue('I8', " SEKSI :  " . $section);
                $path_img = \Yii::$app->params['path'] . "/img/logo.png";
                $objDrawing->setPath($path_img);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setHeight(70);
                $offsetX = 100 - $objDrawing->getWidth();
                $objDrawing->setOffsetX($offsetX);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                $no = 0;
                foreach ($models as $r => $arr) {
                    if (isset($row))
                        $row++;
                    else
                        $row = $baseRow + $r;
////
                    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                    $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':Q' . $row)->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $r + 1);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $arr['nik']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['nama']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['pendidikan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $arr['tmpt_lahir']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, date('d-M-Y', strtotime($arr['tgl_lahir'])));
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $arr['usia']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $arr['alamat_jln']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $arr['desa']);
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $arr['kecamatan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $arr['kabupaten']);
                    $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, ' ' . $arr['no_ktp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, strtoupper($arr['agama']));
                    $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, strtoupper($arr['status_pernikahan']));
                    $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, date('d-M-Y', strtotime($arr['tgl_masuk_kerja'])));
                    $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $arr['tahun']);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $arr['bulan']);
//           
                }
//
                header("Content-type: application/vnd-ms-excel");
                header('Content-Disposition: attachment;filename="karyawan-iso.xlsx"');

                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
            }else {
                return $this->render("/exprekap/karyawaniso", ['models' => $models, 'start' => $start, 'end' => $end, 'section' => $section]);
            }
        } else if ($rekap == "karyawanmasuk") {
            if (!isset($_GET['print'])) {
                $data = array();
                $i = 0;

                $path = \Yii::$app->params['path'] . 'api/templates/karyawan-masuk.xls';
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
                $baseRow = 10;
//
                $objPHPExcel->getActiveSheet()->setCellValue('A5', " Dicetak :  " . date("d F Y"));
                $objPHPExcel->getActiveSheet()->mergeCells('A8:C8')->setCellValue('A8', " PERIODE :  " . date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)));
                $path_img = \Yii::$app->params['path'] . "/img/logo.png";
                $objDrawing->setPath($path_img);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setHeight(70);
                $offsetX = 80 - $objDrawing->getWidth();
                $objDrawing->setOffsetX($offsetX);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                $no = 0;
                foreach ($models as $r => $arr) {
                    if (isset($row))
                        $row++;
                    else
                        $row = $baseRow + $r;
//////
                    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                    $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':E' . $row)->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $r + 1);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $arr['nik']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['nama']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['nama_jabatan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, date('d-M-Y', strtotime($arr['tgl_masuk_kerja'])));
                }
//
                header("Content-type: application/vnd-ms-excel");
                header('Content-Disposition: attachment;filename="karyawan-masuk.xlsx"');

                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
            }else {
                return $this->render("/exprekap/karyawanmasuk", ['models' => $models, 'start' => $start, 'end' => $end, 'section' => $section]);
            }
        } else if ($rekap == "karyawanmasukperpend") {
            if (!isset($_GET['print'])) {
                $data = array();
                $i = 0;

                $path = \Yii::$app->params['path'] . 'api/templates/karyawan-masuk-perpendidikan.xls';
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
                $baseRow = 12;
//
                $objPHPExcel->getActiveSheet()->setCellValue('A5', " Dicetak :  " . date("d F Y"));
                $objPHPExcel->getActiveSheet()->mergeCells('D8:H8')->setCellValue('D8', " PERIODE :  " . date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)));
                $path_img = \Yii::$app->params['path'] . "/img/logo.png";
                $objDrawing->setPath($path_img);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setHeight(70);
                $offsetX = 80 - $objDrawing->getWidth();
                $objDrawing->setOffsetX($offsetX);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                $no = 0;
                foreach ($models as $r => $arr) {
                    if (isset($row))
                        $row++;
                    else
                        $row = $baseRow + $r;
////////
                    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                    $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':K' . $row)->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $r + 1);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $arr['nik']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['nama']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['pendidikan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $arr['sekolah']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $arr['jurusan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $arr['no_ijazah']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $arr['tmpt_lahir']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, date("d-M-Y", strtotime($arr['tgl_lahir'])));
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $arr['bulan_lahir']);
                    $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, "");
//                  
                }
//
                header("Content-type: application/vnd-ms-excel");
                header('Content-Disposition: attachment;filename="karyawan-masuk-perpendidikan.xlsx"');

                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
            }else {
                return $this->render("/exprekap/karyawanmasukperpend", ['models' => $models, 'start' => $start, 'end' => $end, 'section' => $section]);
            }
        } else if ($rekap == "karyawanmasukpertunj") {
            if (!isset($_GET['print'])) {
                $data = array();
                $i = 0;

                $path = \Yii::$app->params['path'] . 'api/templates/karyawan-masuk-pertunjangan.xls';
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
                $baseRow = 12;
//
                $objPHPExcel->getActiveSheet()->setCellValue('A5', " Dicetak :  " . date("d F Y"));
                $objPHPExcel->getActiveSheet()->mergeCells('D8:H8')->setCellValue('D8', " PERIODE :  " . date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)));
                $path_img = \Yii::$app->params['path'] . "/img/logo.png";
                $objDrawing->setPath($path_img);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setHeight(70);
                $offsetX = 80 - $objDrawing->getWidth();
                $objDrawing->setOffsetX($offsetX);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                $no = 0;
                foreach ($models as $r => $arr) {
                    if (isset($row))
                        $row++;
                    else
                        $row = $baseRow + $r;
////////
                    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                    $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':K' . $row)->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $r + 1);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $arr['nik']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['nama']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['pendidikan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $arr['sekolah']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $arr['jurusan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $arr['no_ijazah']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $arr['tmpt_lahir']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, date("d-M-Y", strtotime($arr['tgl_lahir'])));
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $arr['bulan_lahir']);
                    $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, "");
//                  
                }
//
                header("Content-type: application/vnd-ms-excel");
                header('Content-Disposition: attachment;filename="karyawan-masuk-pertunjangan.xlsx"');

                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
            }else {
                return $this->render("/exprekap/karyawanmasukpertunj", ['models' => $models, 'start' => $start, 'end' => $end, 'section' => $section]);
            }
        } else if ($rekap == "karyawanmasukpergaji") {
            if (!isset($_GET['print'])) {
                $data = array();
                $i = 0;

                $path = \Yii::$app->params['path'] . 'api/templates/karyawan-masuk-pergaji.xls';
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
                $baseRow = 12;
//
                $objPHPExcel->getActiveSheet()->setCellValue('A6', " Dicetak :  " . date("d F Y"));
                $objPHPExcel->getActiveSheet()->mergeCells('E8:H8')->setCellValue('E8', " PERIODE :  " . date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)));
                $path_img = \Yii::$app->params['path'] . "/img/logo.png";
                $objDrawing->setPath($path_img);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setHeight(70);
                $offsetX = 80 - $objDrawing->getWidth();
                $objDrawing->setOffsetX($offsetX);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                $no = 0;
                foreach ($models as $r => $arr) {
                    if (isset($row))
                        $row++;
                    else
                        $row = $baseRow + $r;
                    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                    $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':M' . $row)->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $r + 1);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $arr['nik']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['nama']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['kode_bank']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $arr['gaji_pokok']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $arr['t_fungsional']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $arr['t_kehadiran']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $arr['thp']);
                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $arr['upah_tetap']);
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $arr['pesangon']);
                    $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $arr['t_masa_kerja']);
                    $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $arr['penggantian_hak']);
                    $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $arr['normatif']);
////                  
                }
//
                header("Content-type: application/vnd-ms-excel");
                header('Content-Disposition: attachment;filename="karyawan-masuk-pergaji.xlsx"');

                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
            }else {
                return $this->render("/exprekap/karyawanmasukpergaji", ['models' => $models, 'start' => $start, 'end' => $end, 'section' => $section]);
            }
        } else {
            return $this->render("/exprekap/" . $rekap, ['models' => $models, 'start' => $start, 'end' => $end, 'section' => $section]);
        }
    }

    public function actionExcelkontrak() {
        session_start();
        $query = $_SESSION['query'];
        $params = $_SESSION['params'];
        $start = '';
        $end = '';
        $tanggal = '';
        if ($params['tipe_periode'] == 'rentang') {
            $start = $params['tanggal_rentang']['startDate'];
            $end = $params['tanggal_rentang']['endDate'];
        } else {
            $tanggal = $params['tanggal'];
        }
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

        if ($rekap == "karyawankontrak") {
            if (isset($_GET['print'])) {
                return $this->render("/exprekap/karyawankontrak", [
                            'models' => $models,
                            'start' => $start,
                            'end' => $end,
                            'tanggal' => $tanggal,
                            'tipe' => $params['tipe_periode'],
                            'section' => $section
                ]);
            } else {
                $data = array();
                $i = 0;

                $path = \Yii::$app->params['path'] . 'api/templates/masa-kontrak.xls';
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
                $baseRow = 5;
                $periode = '';

                if ($params['tipe_periode'] == 'rentang') {
                    $periode = date('d F Y', strtotime($start)) . ' s/d ' . date('d F Y', strtotime($end));
                } else {
                    $periode = date('d F Y', strtotime($tanggal));
                }

                $objPHPExcel->getActiveSheet()->mergeCells('F2:H2')->setCellValue('F2', " PERIODE :  " . $periode);
                $objPHPExcel->getActiveSheet()->mergeCells('F3:H3')->setCellValue('F3', " SEKSI :  " . $section);
                $path_img = \Yii::$app->params['path'] . "/img/logo.png";
                $objDrawing->setPath($path_img);
                $objDrawing->setCoordinates('A2');
                $objDrawing->setHeight(70);
                $offsetX = 80 - $objDrawing->getWidth();
                $objDrawing->setOffsetX($offsetX);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                $no = 1;
                foreach ($models as $r => $val) {
                    set_time_limit(40);
                    if (isset($row))
                        $row++;
                    else
                        $row = $baseRow + $r;
//
                    $tglPenilaian = (!empty($val['tgl_penilaian'])) ? date('d-M-Y', strtotime($val['tgl_penilaian'])) : '';
                    $status_penilaian = (!empty($val['status_penilaian'])) ? $val['status_penilaian'] : '';
                    $kontrak1 = (empty($val['Kontrak_1'])) ? '' : date('d-M-Y', strtotime($val['Kontrak_1']));
                    $kontrak11 = (empty($val['Kontrak_11'])) ? '' : date('d-M-Y', strtotime($val['Kontrak_11']));
                    $kontrak2 = (empty($val['Kontrak_2'])) ? '' : date('d-M-Y', strtotime($val['Kontrak_2']));
                    $kontrak21 = (empty($val['Kontrak_21'])) ? '' : date('d-M-Y', strtotime($val['Kontrak_21']));


                    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                    $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':J' . $row)->applyFromArray($background);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $no);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $val['nik']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $val['nama']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $val['jabatan']);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $kontrak1);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $kontrak11);
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $kontrak2);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $kontrak21);
                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $tglPenilaian);
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $status_penilaian);
                    $no++;
                }

                header("Content-type: application/vnd-ms-excel");
                header('Content-Disposition: attachment;filename="masa-kontrak.xlsx"');

                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
            }
        } else {
            return $this->render("/exprekap/" . $rekap, [
                        'models' => $models,
                        'start' => $start,
                        'end' => $end,
                        'tanggal' => $tanggal,
                        'tipe' => $params['tipe_periode'],
                        'section' => $section
            ]);
        }
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

        if (isset($_GET['print'])) {
            return $this->render("/exprekap/karyawankeluar", ['models' => $models, 'start' => $start, 'end' => $end]);
        } else {
            $data = array();
            $i = 0;

            $path = \Yii::$app->params['path'] . 'api/templates/karyawan-keluar.xls';
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
            $baseRow = 11;
//
            $objPHPExcel->getActiveSheet()->setCellValue('A5', " Dicetak :  " . date("d F Y"));
            $objPHPExcel->getActiveSheet()->mergeCells('A8:E8')->setCellValue('A8', " PERIODE :  " . date('d F Y', strtotime($start)) . ' S/D ' . date('d F Y', strtotime($end)));
            $path_img = \Yii::$app->params['path'] . "/img/logo.png";
            $objDrawing->setPath($path_img);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setHeight(70);
            $offsetX = 100 - $objDrawing->getWidth();
            $objDrawing->setOffsetX($offsetX);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $no = 0;
            foreach ($models as $r => $arr) {
                $date_msk = new \DateTime($arr['tgl_masuk_kerja']);
                $date_klr = new \DateTime($arr['tgl_keluar_kerja']);
                if (isset($row))
                    $row++;
                else
                    $row = $baseRow + $r;

                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(21);
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($background);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $r + 1);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $arr['nik']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $arr['nama']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $arr['jabat']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $date_msk->format("d-M-Y"));
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $date_klr->format("d-M-Y"));
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $arr['alasan_keluar']);
            }
//
            header("Content-type: application/vnd-ms-excel");
            header('Content-Disposition: attachment;filename="karyawan-keluar.xlsx"');

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
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

    public function actionUrutJabatan() {
        $karyawan = TblKaryawan::findAll([
                    'tbl_karyawan.status like "kerja"'
        ]);

        $data = [];
        $subData = [];
        $subSubData = [];
//        $section = [];

        $department = \app\models\Department::find()->all();
        if (!empty($department)) {
            foreach ($department as $k => $v) {
                $data[$k]['label'] = $v->department;
                $data[$k]['kode'] = $v->id_department;
                $section = \app\models\Section::find()->where('dept ="' . $v->id_department . '"')->all();

                if (!empty($section)) {
                    foreach ($section as $ke => $va) {
                        $subData[] = $va->section;
                        $data[$k]['children'][$ke]['label'] = $va->section;
                        $data[$k]['children'][$ke]['kode'] = $va->id_section;
                        $subSection = \app\models\SubSection::find()->where('id_section="' . $va->id_section . '"')->all();
                        if (!empty($subSection)) {
                            foreach ($subSection as $key => $val) {
//                                $subSubData[] = $val->kerja;
                                $data[$k]['children'][$ke]['children'][$key]['label'] = $val->kerja;
                                $data[$k]['children'][$ke]['children'][$key]['kode'] = $val->kd_kerja;
//                                $data[$k]['children'][$ke]['children'][$key]['children'] = $subSubData;
                            }
                        }
                    }
                }
            }
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data));
    }

    public function actionDetKaryawan() {
        $params = $_REQUEST;
        $tipeKode = substr($params['kode'], 0, 2);
//        Yii::error($tipeKode);

        $params = $_REQUEST;
        $query = new Query;
        $query->from('tbl_karyawan as kar')
                ->join('LEFT JOIN', 'tbl_section as sec', 'sec.id_section = kar.section')
                ->join('LEFT JOIN', 'pekerjaan as sub', 'sub.kd_kerja = kar.sub_section')
                ->join('LEFT JOIN', 'tbl_department as dep', 'dep.id_department = kar.department')
                ->join('LEFT JOIN', 'tbl_jabatan as jab', 'jab.id_jabatan= kar.jabatan')
                ->select("kar.nik, kar.nama as nm_karyawan,jab.jabatan as nm_jabatan")
                ->where('kar.status like "Kerja"');



        if ($tipeKode == 'DP') {
            $query->andWhere(['kar.department' => $params['kode']]);
        } else if ($tipeKode == 'SC') {
            $query->andWhere(['kar.section' => $params['kode']]);
        } else if ($tipeKode == 'KR') {
            $query->andWhere(['kar.sub_section' => $params['kode']]);
        }
        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

}
