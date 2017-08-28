<?php

function cek_validate($data, $validasi) {
    if (!empty($custom)) {
        $validasi = $custom;
    }

    Validator::set_field_name("article_category_id", "Kategori");

    $validate = Validator::is_valid($data, $validasi);

    if ($validate === true) {
        return true;
    } else {
        $error = '';
        foreach ($validate as $val) {
            $error .= $val . '<br>';
        }
        return $error;
    }
}

function cek_user($data, $custom = array()) {
    $validasi = array(
        'username' => 'required',
        'nama' => 'required'
    );

    $cek = cek_validate($data, $validasi, $custom);
    return $cek;
}

function cek_artikel($data, $custom = array()) {
    $validasi = array(
        'title' => 'required',
        'content' => 'required',
        'publish' => 'required',
    );

    $cek = cek_validate($data, $validasi, $custom);
    return $cek;
}

function cek_setting($data, $custom = array()) {
    $validasi = array(
        'nama' => 'required',
        'alamat' => 'required',
        'telepon' => 'required',
        'email' => 'required'
    );

    $cek = cek_validate($data, $validasi, $custom);
    return $cek;
}

function cek_grafik($data, $custom = array()){
    $validasi = array("nama" => "required", "isi" => "required");
    return cek_validate($data, $validasi, $custom);
}

function cek_tanyajawab($data, $custom = array()){
	$validasi = array(
		"judul"=>"required",
		"isi"=>"required",
		"nip"=>"required",
		"created_at"=>"required");
	return cek_validate($data, $validasi, $custom);
}

function cek_workshop($data, $custom = array()){
    $validasi = array("nama" => "required", "aktif" => "required", "tgl_event" => "required");
    return cek_validate($data, $validasi, $custom);
}
