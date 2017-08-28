app.controller('notifAbkCtrl', function ($scope, Data, toaster) {
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
        Data.get('notifbarang', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

//    $scope.excel = function () {
//        Data.get('modelkendaraan', paramRef).then(function (data) {
//            window.location = 'api/web/modelkendaraan/excel';
//        });
//    }

//    $scope.create = function (form) {
//        $scope.is_create = true;
//        $scope.is_edit = true;
//        $scope.is_view = false;
//        $scope.formtitle = "Form Tambah Data";
//        $scope.form = {};
//        Data.get('modelkendaraan/kode').then(function (data) {
//            $scope.form.kd_model = data.kode;
//        });
//    };
//    $scope.update = function (form) {
//        $scope.is_create = false;
//        $scope.is_edit = true;
//        $scope.is_view = false;
//        $scope.formtitle = "Edit Data : " + form.kd_model;
//        $scope.form = form;
//    };
//    $scope.view = function (form) {
//        $scope.is_edit = true;
//        $scope.is_view = true;
//        $scope.formtitle = "Lihat Data : " + form.kd_model;
//        $scope.form = form;
//    };
    $scope.cancel = function () {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.is_edit = false;
        $scope.is_view = false;
    };

})
