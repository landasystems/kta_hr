app.controller('supplierCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

    $scope.callServer = function callServer(tableState) {
        tableStateRef = tableState;
        $scope.isLoading = true;
        var offset = tableState.pagination.start || 0;
        var limit = tableState.pagination.number || 10;
        var param = {offset: offset, limit: limit};

        if (tableState.sort.predicate) {
            param['sort'] = tableState.sort.predicate;
            param['order'] = tableState.sort.reverse;
        }
        if (tableState.search.predicateObject) {
            param['filter'] = tableState.search.predicateObject;
        }
        paramRef = param;
        Data.get('supplier', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };
    $scope.excel = function () {
        Data.get('supplier', paramRef).then(function (data) {
            window.location = 'api/web/supplier/excel';
        });
    }
    
    $scope.create_kode = function () {
        var nama_sp = $scope.form.nama_supplier;
        var kods = $scope.form.kd_supplier;
        var buat = $scope.is_create;
        if (buat == true) {
            Data.get('supplier/kode', {nama: nama_sp}).then(function (data) {
                $scope.form.kd_supplier = data.data;
            });
        } else {
            $scope.form.kd_supplier = kods;
        }
    }

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.merk;
        $scope.form = form;
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.merk;
        $scope.form = form;
    };
    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'supplier/create' : 'supplier/update/' + form.kd_chassis;
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
            }
        });

        //---------
//        $scope.is_edit = false;
//        if ($scope.is_create == true) {
//            Data.post('chassis/create', form).then(function(result) {
//
//
//            });
//        } else {
//
//            Data.post('chassis/update/' + form.kd_chassis, form).then(function(result) {
//
//            });
//        }
    };
    $scope.cancel = function () {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('supplier/delete/' + row.kd_supplier).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


})
