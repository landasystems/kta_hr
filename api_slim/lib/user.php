<?php

/*
  Tabel yang dibutuhkan

  ==== CREATE TABLE ====

  CREATE TABLE IF NOT EXISTS `m_roles` (
  `id` int(11) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `akses` text NOT NULL,
  ) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

  CREATE TABLE IF NOT EXISTS `m_user` (
  `id` int(11) NOT NULL,
  `roles_id` int(5) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  ) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

  ==== END CREATE TABLE ====

  contoh penggunaan batasan hak akses

  $allow = check_access(array('admin' => true)); <= Hanya boleh diakses oleh admin
  $allow = check_access(array('login' => true)); <= Hanya boleh diakses oleh user yang sudah login
  $allow = check_access(array('modul' => 'create_product')); <= Hanya boleh diakses oleh user yang memiliki akses create product dan admin

  contoh penggunaan login

  $login = login(array('username'=>$username,'password'=>sha1($password)));
  if($login){
  echo 'LOGIN SUCCESS';
  }else{
  echo 'LOGIN FAILED';
  }
 */

function login($param = array('username' => '', 'password' => '', 'oauth_provider' => '', 'oauth_uid' => '')) {
    $sql = new LandaDb();
    $sql->select("m_user.*")->from("m_user");

    if (!empty($param['oauth_provider']) && !empty($param['oauth_uid'])) {
        $sql->where("=", "m_user.oauth_provider", $param['oauth_provider'])
                ->andWhere("=", "m_user.oauth_uid", $param['oauth_uid']);
    } else {
        $sql->where("=", "m_user.username", $param['username'])
                ->andWhere("=", "m_user.password", $param['password']);
    }

    $model = $sql->find();
    if (!empty($model)) {
        $_SESSION['user'] = array('id' => $model->id, 'email' => $model->email, 'username' => $model->username, 'nama' => $model->name, 'akses' => '', 'roles_id' => $model->roles_id, 'oauth_provider' => $model->oauth_provider, 'oauth_uid' => $model->oauth_uid);
        return true;
    } else return false;
    
}

function check_access($param = array('admin' => true, 'login' => false, 'module' => '', '*'), $redirect = '') {
    $r = false;
    if (isset($param['admin']) and $param['admin'] == true) {
        $r = (isset($_SESSION['user']['roles_id']) && $_SESSION['user']['roles_id'] == "1");
    }

    if (isset($param['login']) and $param['login'] === true) {
        $r = isset($_SESSION['user']);
    }

    if (isset($param['module']) and ! empty($param['module'])) {
       $r = isset($_SESSION['user']['akses']) && (in_array([$param['module']], $_SESSION['user']['akses']) || $_SESSION['user']['roles_id'] == "1");
    }

    if (isset($param['*'])) $r = true;

    if ($r == false) {
        if (empty($redirect))
            not_found();
        else
            redirect($redirect);
    }
}

?>