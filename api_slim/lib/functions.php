<?php

/* product */

function facebook_connect() {
    return array('appId' => '825402027535189', 'secret' => '4540ca279550c32c25e168b7e3e0a1a9');
}

function get_gallery_src($html) {
    $post_html = str_get_html($html);
    $img = $post_html->find('img');
    $image = array();
    if (!empty($img)) {
        foreach ($img as $val) {
            $image[] = $val->src;
        }
    }
    return $image;
}

function get_first_image($html, $size, $class = null, $style = null) {
	$class = (!empty($class)) ? $class : 'img img-responsive';
	$post_html = str_get_html($html);
	$first_img = $post_html->find('img', 0);

	if ($size == "small")
		$a = site_url() . "app/img/no.jpg";
	else if ($size == "medium")
		$a = site_url() . "app/img/system/350x350-noimage.jpg";
	else $a = site_url() . "app/img/system/white.png";
return (!is_null($first_img)) ? '<img src="' . $first_img->src . '" class="' . $class . '" onError="this.src=\'' . $a . '\'" style="' . $style .'" />' :
	'<img src="' . $a . '" class="' . $class . '" style="' . $style . '" />';
}

function getString($string, $start, $end) {
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}

function isNew($tanggal) {
    $tgl_created = date("Y-m-d", strtotime
                    ($tanggal));
    $tgl = date("Y-m-d");
    $awal = new DateTime($tgl);
    $akhir = $awal->modify('-7 day');
    $akhir = new DateTime($tgl);
    $akhir = $akhir->modify('+1 day');

    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($awal, $interval, $akhir);

    $arr_tgl = [];
    foreach ($daterange as $val) {
        $arr_tgl[] = $val->format("Y-m-d");
    }

    return (in_array($tgl_created, $arr_tgl)) ? 1 : 0;
}

function addHits($id) {
    $sql = new LandaDb();
    $sql->run('UPDATE article SET hits = hits+1 WHERE id = ' . $id);
}

function cuplikan($str, $panjang) {
	return (strlen(strip_tags($str)) <= $panjang) ? strip_tags($str) : substr(strip_tags($str), 0, $panjang) . '...';
}

function rmImg($str) {
    return preg_replace('/<img.*?>/', '', $str, 1);
}

function sensornip($nip){
	return (strlen($nip) > 5) ? substr($nip, 0, strlen($nip) - 4)."****" : $nip;
}

function indoDayName($N) {
	switch (intval($N)) {
		case 1: return "Senin";
		case 2: return "Selasa";
		case 3: return "Rabu";
		case 4: return "Kamis";
		case 5: return "Jumat";
		case 6: return "Sabtu";
		case 7: return "Minggu";
	}
}