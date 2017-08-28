<?php
//Hak Akses bersifat hardcoded, ada di m_roles/index.html

//Halaman Indeks untuk dites
get('/index', function () {
    $sql = new LandaDb();
    print("SLIM API for KTA HR");
});

get("/z/session", function() {
	echo json_encode($_SESSION);
});

//Mulai Riwayat Gaji
get("/riwayatgaji/:nik", function($nik) {
	$sql = new LandaDb();
	$get = $sql->select("*")
		->from("tbl_det_gaji")
		->where("=", "tbl_det_gaji.nik", $nik)
		->findAll();
	echo json_encode($get);
});

post("/riwayatgaji/insert", function() {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$model = $sql->insert("tbl_det_gaji", $r);
	if($model) { echo json_encode(array('status' => 1, 'data' => (array) $model), JSON_PRETTY_PRINT); }
	else { echo json_encode(array("status" => 0, "error_code" => 400, "errors" => "Gagal disimpan."), JSON_PRETTY_PRINT); }
});

post("/riwayatgaji/update/:id", function($id) {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$model = $sql->update("tbl_det_gaji", $r, array("id" => $id));
	if($model) { echo json_encode(array('status' => 1, 'data' => (array) $model), JSON_PRETTY_PRINT); }
	else { echo json_encode(array("status" => 0, "error_code" => 400, "errors" => "Gagal disimpan."), JSON_PRETTY_PRINT); }
});

post("/riwayatgaji/activate/:id", function ($id) {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$nik = $sql->select("*")->from("tbl_det_gaji")->where("=", "tbl_det_gaji.id", $id)->find()->nik;
	$sql->update("tbl_det_gaji", array("status" => 0), array("nik" => $nik));
	$model = $sql->update("tbl_det_gaji", array("status" => 1), array("id" => $id));

	echo json_encode(array("status" => 200, "data" => $model), JSON_PRETTY_PRINT);
});

post("/riwayatgaji/delete/:id", function ($id) {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$sql->delete("tbl_det_gaji", array("id" => $id));
	echo json_encode(array("status" => 1));
});


//Mulai Asuransi Karyawan
get("/karyawan/asuransi/:nik", function($nik) {
	$sql = new LandaDb();
	echo json_encode(array("status" => 1, "data" => $sql->select("*")->from("tbl_asuransi")->where("=", "tbl_asuransi.nik", $nik)->findAll()));
});

post("/karyawan/asuransi/insert", function() {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$model = $sql->insert("tbl_asuransi", $r);
	if($model) { echo json_encode(array('status' => 1, 'data' => (array) $model), JSON_PRETTY_PRINT); }
	else { echo json_encode(array("status" => 0, "error_code" => 400, "errors" => "Gagal disimpan."), JSON_PRETTY_PRINT); }
});

post("/karyawan/asuransi/update/:id", function($id) {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$model = $sql->update("tbl_asuransi", $r, array("id" => $id));
	if($model) { echo json_encode(array('status' => 1, 'data' => (array) $model), JSON_PRETTY_PRINT); }
	else { echo json_encode(array("status" => 0, "error_code" => 400, "errors" => "Gagal disimpan."), JSON_PRETTY_PRINT); }
});

post("/karyawan/asuransi/delete/:id", function($id) {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$sql->delete("tbl_asuransi", array("id" => $id));
	echo json_encode(array("status" => 1));
});

//Mulai UMK
get("/umk/karyawan/:namalokasikantor/:aktif", function($namalokasikantor, $aktif) {
	$sql = new LandaDb();
	$o = $sql->select("*")->from("tbl_karyawan")
		->where("=", "lokasi_kntr", $namalokasikantor)
		->where("=", "status", $aktif ? "Kerja" : "Keluar")
		->findAll();
	echo json_encode(array("status" => (count($o)>0?1:0), "data" => $o));
});

post("/umk/applyToAll/", function () {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$ary = $sql->select("*")->from("tbl_karyawan")
		->where("=", "lokasi_kntr", $r["lokasikantor"])
		->where("=", "status", "Kerja")
		->findAll();

	try {
	foreach($ary as $a) {
		$sql->update("tbl_det_gaji", array("status" => 0), array("nik" => $a->nik));
		$sql->insert("tbl_det_gaji", array("nik" => $a->nik, "gaji" => $r["umk"], "tgl" => date("Y-m-d", time()), "status" => 1));
	}
	} catch (Exception $e) {
		echo json_encode(array("status" => 0, "errors" => $e));
	} finally {
		echo json_encode(["status" => 1]);
	}
});

//Mulai Transaksi Lembur
get("/karyawan/:nik", function ($nik) {
	$sql = new LandaDb();
	$d = $sql->select("*")
		->from("tbl_karyawan")
		->where("=", "nik", $nik)
		->find();

	echo json_encode($d);
});

get("/department/:id", function ($id) {
	$sql = new LandaDb();
	$d = $sql->select("*")
		->from("tbl_department")
		->where("=", "id_department", $id)
		->find();

	echo json_encode($d);
});

get("/section/:id", function ($id) {
	$sql = new LandaDb();
	$d = $sql->select("*")
		->from("tbl_section")
		->where("=", "id_section", $id)
		->find();

	echo json_encode($d);
});

get("/karyawan/gajiaktif/:nik", function ($nik) {
	$sql = new LandaDb();
	$ga = $sql->select("*")
		->from("tbl_det_gaji")
		->where("=", "nik", $nik)
		->where("=", "status", 1)
		->find();

	echo json_encode($ga);
});

get("/transaksi/lembur", function () {
	$sql = new LandaDb();
	$d = $sql->select("t_trans_lembur.*, tbl_karyawan.nama, tbl_lokasi_kantor.*")
		->from("t_trans_lembur")
		->join('LEFT JOIN', 'tbl_karyawan', 'tbl_karyawan.nik = t_trans_lembur.nik')
		->join("LEFT JOIN", "tbl_lokasi_kantor", "tbl_lokasi_kantor.lokasi_kantor = tbl_karyawan.lokasi_kntr")
		->findAll();
	echo json_encode(["status" => 1, "data" => $d]);
});

get("/lokasikantor/:lk", function ($lk) {
	$sql = new LandaDb();
	$d = $sql->select("tbl_lokasi_kantor.*")
		->from("tbl_lokasi_kantor")
		->where("=", "lokasi_kantor", $lk)
		->find();
	echo json_encode(["status" => 1, "data" => $d]);
});

//Cek hari libur
get("/transaksi/lembur/libur/:tgl", function($tgl) {
	$sql = new LandaDb();
	$d = $sql->select("tbl_kalender.*")
		->from("tbl_kalender")
		->where("=", "tbl_kalender.tgl", $tgl)
		->find();

	echo json_encode(["status" => 1, "data" => $d]);
});


//Mulai post lembur
post("/transaksi/lembur/insert", function () {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$r["created_at"] = time();
	$r["created_by"] = $_SESSION["user"]["id"];
	$r["modified_at"] = time();
	$r["modified_by"] = $_SESSION["user"]["id"];

	$model = $sql->insert("t_trans_lembur", $r);
	if($model) { echo json_encode(array('status' => 1, 'data' => (array) $model), JSON_PRETTY_PRINT); }
	else { echo json_encode(array("status" => 0, "error_code" => 400, "errors" => "Gagal disimpan."), JSON_PRETTY_PRINT); }
});

post("/transaksi/lembur/update/:id_lembur", function ($id_lembur) {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$r["modified_at"] = time();
	$r["modified_by"] = $_SESSION["user"]["id"];

	$model = $sql->update("t_trans_lembur", $r, array("id_lembur" => $id_lembur));
	if($model) { echo json_encode(array('status' => 1, 'data' => (array) $model), JSON_PRETTY_PRINT); }
	else { echo json_encode(array("status" => 0, "error_code" => 400, "errors" => "Gagal disimpan."), JSON_PRETTY_PRINT); }
});

post("/transaksi/lembur/delete/:id_lembur", function ($id_lembur) {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$sql->delete("t_trans_lembur", array("id_lembur" => $id_lembur));
	echo json_encode(array("status" => 1));
});

//Mulai Transaksi Gaji
get("/transaksi/gaji", function () {
	$sql = new LandaDb();
	$tg = $sql->select("t_trans_gaji.*, tbl_department.department, tbl_lokasi_kantor.lokasi_kantor")
		->from("t_trans_gaji")
		->join('LEFT JOIN', 'tbl_department', 'tbl_department.id_department = t_trans_gaji.id_dep')
		->join("LEFT JOIN", "tbl_lokasi_kantor", "tbl_lokasi_kantor.id_lokasi_kantor = t_trans_gaji.id_lokasi_kntr")
		->findAll();
	echo json_encode($tg);
});

get("/transaksi/gaji/detail/:id", function ($id) {
	$sql = new LandaDb();
	$dg = $sql->select("t_det_trans_gaji.*, tbl_karyawan.nama")
		->from("t_det_trans_gaji")
		->join("LEFT JOIN", "tbl_karyawan", "tbl_karyawan.nik = t_det_trans_gaji.nik")
		->where("=", "id_gaji", $id)
		->findAll();
	echo json_encode($dg);
});

//Todo: implement transaksi/gaji, transaksi/gaji/:bln_thn, transaksi/gaji/insert, transaksi/gaji/update, transaksi/gaji/del/:id
//Mulai detail
post("/transaksi/gaji/insert", function (){
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$r["created_at"] = time();
	$r["created_by"] = $_SESSION["user"]["id"];
	$r["modified_at"] = time();
	$r["modified_by"] = $_SESSION["user"]["id"];

	//id, bln_thn, id_dep, id_lokasi_kntr
	$n = $sql->select("*")
		->from("t_trans_gaji")
		->where("=", "bln_thn", $r["bln_thn"])
		->where("=", "id_dep", $r["id_department"])
		->where("=", "id_lokasi_kntr", $r["id_lokasi_kntr"])
		->find();
	if(!$n) {
		$sql->insert("t_trans_gaji", array("bln_thn" => $r["bln_thn"], "id_dep" => $r["id_department"], "id_lokasi_kntr" => $r["id_lokasi_kntr"]));
		$max = $sql->select("max(id) as max")->from("t_trans_gaji")->find()->max;
		$r["id_gaji"] = $max;
	} else $r["id_gaji"] = $n->id;

	//Todo: implement insert to t_trans_gaji & t_det_trans_gaji
	$model = $sql->insert("t_det_trans_gaji", $r);
	if($model) { echo json_encode(array('status' => 1, 'data' => (array) $model), JSON_PRETTY_PRINT); }
	else { echo json_encode(array("status" => 0, "error_code" => 400, "errors" => "Gagal disimpan."), JSON_PRETTY_PRINT); }
});

post("/transaksi/gaji/update/:id_det_trans_gaji", function ($id_det_trans_gaji) {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$r["modified_at"] = time();
	$r["modified_by"] = $_SESSION["user"]["id"];

	$model = $sql->update("t_det_trans_gaji", $r, array("id" => $id_det_trans_gaji));
	if($model) { echo json_encode(array('status' => 1, 'data' => (array) $model), JSON_PRETTY_PRINT); }
	else { echo json_encode(array("status" => 0, "error_code" => 400, "errors" => "Gagal disimpan."), JSON_PRETTY_PRINT); }
});

post("/transaksi/gaji/delete/:id_det_trans_gaji", function ($id_det_trans_gaji) {
	$sql = new LandaDb();
	$r = json_decode(file_get_contents("php://input"), true);

	$sql->delete("t_det_trans_gaji", array("id" => $id));
	echo json_encode(array("status" => 1));
});


get("/laporan/lembur", function () {
	$sql = new LandaDb();
	$g = $_GET;


	//Todo: query Karyawan of the department then query t_trans_lembur of the karyawan

	$r = $sql->select("*")
		->from("t_trans_lembur");
		//->customWhere("tgl between '" . $g["awal_bln_thn"] . "' and '" . $g["akhir_bln_thn"] . "'");

	if(!isset($g["nik"])) {
	if(isset($g["id_department"])) {
		$dep = $g["id_department"];
		$k = $sql->select("nik") //Gets all karyawan of the same department
			->from("tbl_karyawan")
			->where("=", "department", $dep)
			->findAll();

		foreach ($k as $n)
			$r->orWhere("=", "nik", $n->nik);
	} } else {
		$r->orWhere("=", "nik", $n);
	}

	$o = $r->findAll();

	echo json_encode(["status" => 200, "data" => $o]);
});