<?php

get('/setting/get', function() {
    $sql = new LandaDb();
    $get = $sql->find("select * from m_web_setting");
    echo json_encode(array('data' => $get));
});

post('/setting/save', function() {
    check_access(array('admin' => true));

    $params = json_decode(file_get_contents("php://input"), true);

    if (cek_setting($params) === true) {
        $sql = new LandaDb();
        $model = $sql->update('m_web_setting', $params, array('id' => $params['id']));

        if ($model) {
            echo json_encode(array('status' => 1, 'data' => (array) $model), JSON_PRETTY_PRINT);
        } else {
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => 'Data gagal disimpan'), JSON_PRETTY_PRINT);
        }
    } else {
        echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => cek_setting($params)), JSON_PRETTY_PRINT);
    }
});
