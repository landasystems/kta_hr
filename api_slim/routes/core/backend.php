<?php

post('/app/upload.html', function() {
    if (file_exists("img/article" . $_FILES["upload"]["name"])) {
        echo $_FILES["upload"]["name"] . " already exists please choose another image.";
    } else {
        $newName = urlParsing($_FILES["upload"]["name"]);
        $uploadPath = img_path() . 'article' . DIRECTORY_SEPARATOR . $newName;

        move_uploaded_file($_FILES["upload"]["tmp_name"], $uploadPath);

        $crtImg = createImg('article' . '/', $newName, date("dYh"), true);

        $url = img_url() . DIRECTORY_SEPARATOR . 'article' . DIRECTORY_SEPARATOR . $crtImg['big'];

//        $url = str_replace("app", "", $crtImg['big']);
        // Required: anonymous function reference number as explained above.
        $funcNum = $_GET['CKEditorFuncNum'];
        // Optional: instance name (might be used to load a specific configuration file or anything else).
        $CKEditor = $_GET['CKEditor'];
        // Optional: might be used to provide localized messages.
        $langCode = $_GET['langCode'];

        echo "<script type='text/javascript'> window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '');</script>";
    }
});

get('/site/session', function () {
    if (isset($_SESSION['user']['roles_id'])) {
        echo json_encode(array('status' => 1, 'data' => array_filter($_SESSION)), JSON_PRETTY_PRINT);
    } else {
        echo json_encode(array('status' => 1, 'data' => 'undefined'), JSON_PRETTY_PRINT);
    }
});

post('/site/login', function () {
    $params = json_decode(file_get_contents("php://input"), true);
    $sql = new LandaDb();
    $model = $sql->select("m_user.*, m_roles.akses")
            ->from("m_user")
            ->join("left join", "m_roles", "m_roles.id = m_user.roles_id")
            ->where("=", "username", $params['username'])
            ->andWhere("=", "password", sha1($params['password']))
            ->find();

    if (!empty($model)) {
        $_SESSION['user']['id'] = $model->id;
        $_SESSION['user']['username'] = $model->username;
        $_SESSION['user']['nama'] = $model->nama;
        $_SESSION['user']['email'] = $model->email;
        $_SESSION['user']['roles_id'] = $model->roles_id;
        $_SESSION['user']['akses'] = $model->akses;

        echo json_encode(array('status' => 1, 'data' => array_filter($_SESSION)), JSON_PRETTY_PRINT);
    } else {
        echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => "Authentication Systems gagal, username atau password Anda salah."), JSON_PRETTY_PRINT);
    }
});

get('/site/logout', function () {
    session_destroy();
});

get('/appartikel/as', function () {
    echo 'asdasd';
});
