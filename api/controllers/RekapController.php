<?php

namespace app\controllers;

use Yii;
use app\models\ReturBbk;
use app\models\Barang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class RekapController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'rekapchassisin' => ['get'],
                    'excelchassisin' => ['get'],
                    'rekapwokeluar' => ['get'],
                    'excelwokeluar' => ['get'],
                    'rekapwomasuk' => ['get'],
                    'excelwomasuk' => ['get'],
                    'excelwomasuk2' => ['get'],
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

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionRekapchassisin() {
        $params = $_REQUEST;
        $filter = array();
        $sort = "sti.tgl_terima DESC";
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
                ->from('view_wo_spk as vws')
                ->join('JOIN', 'chassis', 'chassis.kd_chassis = vws.kd_chassis')
                ->join('JOIN', 'customer', 'customer.kd_cust = vws.kd_cust')
                ->join('JOIN', 'serah_terima_in as sti', 'sti.no_spk = vws.no_spk')
                ->join('JOIN', 'spk', 'vws.no_spk = spk.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->where('tk.department="DPRT005"')
                ->orderBy($sort)
                ->select("sti.tgl_terima, tk.nama, customer.nm_customer, customer.provinsi, sti.kd_titipan, vws.no_wo, chassis.merk, chassis.tipe, sti.no_chassis,sti.no_mesin, spk.total_harga, spk.no_spk, chassis.jenis");
//filter

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                if (isset($key) && $key == 'tgl_terima') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'sti.tgl_terima', $start, $end]);
                } elseif ($key == 'no_wo') {
                    $query->andFilterWhere(['like', 'vws.' . $key, $val]);
                } elseif ($key == 'no_spk') {
                    $query->andFilterWhere(['like', 'spk.' . $key, $val]);
                } elseif ($key == 'model') {
                    $query->andFilterWhere(['like', 'model.' . $key, $val]);
                } elseif ($key == 'kd_titipan') {
                    $query->andFilterWhere(['like', 'sti.' . $key, $val]);
                } elseif ($key == 'nm_customer') {
                    $query->andFilterWhere(['like', 'customer.' . $key, $val]);
                } elseif ($key == 'no_chassis') {
                    $query->andFilterWhere(['like', 'spk.' . $key, $val]);
                } elseif ($key == 'merk') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                }
            }
        }
        Yii::error($query);
        $command = $query->createCommand();
        $models = $command->queryAll();
//        Yii::error($models);
        $totalItems = $query->count();

        $query->limit(null);
        $query->offset(null);
        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['filter'] = $filter;

//        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionRekapwokeluar() {
        $params = $_REQUEST;
        $filter = array();
        $sort = "sti.tgl_terima DESC";
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
                ->from('view_wo_spk as vws')
                ->join('JOIN', 'wo_masuk as wm', 'wm.no_wo = vws.no_wo')
                ->join('JOIN', 'chassis', 'chassis.kd_chassis = vws.kd_chassis')
                ->join('JOIN', 'model', 'model.kd_model = vws.kd_model')
                ->join('JOIN', 'customer', 'customer.kd_cust = vws.kd_cust')
                ->join('JOIN', 'serah_terima_in as sti', 'sti.no_spk = vws.no_spk')
                ->join('JOIN', 'spk', 'vws.no_spk = spk.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->where('tk.department="DPRT005" and wm.tgl_keluar IS NOT NULL')
                ->orderBy($sort)
                ->select("sti.tgl_terima, customer.provinsi, customer.nm_customer, chassis.jenis, spk.no_spk, vws.no_wo, sti.kd_titipan, wm.tgl_keluar ,
                        chassis.merk, chassis.tipe, model.model, tk.nama, spk.jml_unit");
//filter

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                if (isset($key) && $key == 'tgl_terima') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'sti.tgl_terima', $start, $end]);
                } elseif ($key == 'no_wo') {
                    $query->andFilterWhere(['like', 'vws.' . $key, $val]);
                } elseif ($key == 'no_spk') {
                    $query->andFilterWhere(['like', 'spk.' . $key, $val]);
                } elseif ($key == 'model') {
                    $query->andFilterWhere(['like', 'model.' . $key, $val]);
                } elseif ($key == 'nama') {
                    $query->andFilterWhere(['like', 'customer.' . $key, $val]);
                } elseif ($key == 'nm_customer') {
                    $query->andFilterWhere(['like', 'customer.' . $key, $val]);
                } elseif ($key == 'jenis') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                } elseif ($key == 'merk') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                } elseif ($key == 'tipe') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                }
            }
        }
        Yii::error($query);
        $command = $query->createCommand();
        $models = $command->queryAll();
//        Yii::error($models);
        $totalItems = $query->count();

        $query->limit(null);
        $query->offset(null);
        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['filter'] = $filter;

//        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }
    public function actionRekapwomasuk() {
        $params = $_REQUEST;
        $filter = array();
        $sort = "spk.tgl DESC";
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
                ->from('view_wo_spk as vws')
                ->join('JOIN', 'wo_masuk as wm', 'wm.no_wo = vws.no_wo')
                ->join('JOIN', 'chassis', 'chassis.kd_chassis = vws.kd_chassis')
                ->join('JOIN', 'model', 'model.kd_model = vws.kd_model')
                ->join('JOIN', 'customer', 'customer.kd_cust = vws.kd_cust')
                ->join('JOIN', 'serah_terima_in as sti', 'sti.no_spk = vws.no_spk')
                ->join('JOIN', 'spk', 'vws.no_spk = spk.no_spk')
                ->join('LEFT JOIN', 'tbl_karyawan as tk', 'tk.nik = spk.nik')
                ->where('tk.department="DPRT005" and wm.tgl_keluar IS NOT NULL')
                ->orderBy($sort)
                ->select("sti.kd_titipan, spk.tgl, customer.provinsi, customer.nm_customer, spk.jml_unit, spk.no_spk, wm.no_wo, sti.no_chassis, sti.no_mesin, 
                            tk.nama, customer.market, model.model, chassis.merk, chassis.tipe, chassis.jenis");
//filter

        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {

                if (isset($key) && $key == 'tgl') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'spk.tgl', $start, $end]);
                } elseif ($key == 'no_wo') {
                    $query->andFilterWhere(['like', 'vws.' . $key, $val]);
                } elseif ($key == 'no_spk') {
                    $query->andFilterWhere(['like', 'spk.' . $key, $val]);
                } elseif ($key == 'model') {
                    $query->andFilterWhere(['like', 'model.' . $key, $val]);
                } elseif ($key == 'nama') {
                    $query->andFilterWhere(['like', 'customer.' . $key, $val]);
                } elseif ($key == 'nm_customer') {
                    $query->andFilterWhere(['like', 'customer.' . $key, $val]);
                } elseif ($key == 'jenis') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                } elseif ($key == 'merk') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                } elseif ($key == 'tipe') {
                    $query->andFilterWhere(['like', 'chassis.' . $key, $val]);
                }
            }
        }
        Yii::error($query);
        $command = $query->createCommand();
        $models = $command->queryAll();
//        Yii::error($models);
        $totalItems = $query->count();

        $query->limit(null);
        $query->offset(null);
        session_start();
        $_SESSION['query'] = $query;
        $_SESSION['filter'] = $filter;

//        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionExcelchassisin() {
         session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];

        $command = $query->createCommand();
        $models = $command->queryAll();
    return $this->render("/expretur/chassisin", ['models' => $models, 'filter' => $filter]);
    }

    public function actionExcelwokeluar() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];

        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/wokeluar", ['models' => $models, 'filter' => $filter]);
    }
    public function actionExcelwomasuk() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];

        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/womasuk", ['models' => $models, 'filter' => $filter]);
    }
    public function actionExcelwomasuk2() {
        session_start();
        $query = $_SESSION['query'];
        $filter = $_SESSION['filter'];

        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("/expretur/womasuk2", ['models' => $models, 'filter' => $filter]);
    }

}

?>
