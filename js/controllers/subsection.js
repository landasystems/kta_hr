app.controller('subsectionCtrl', function ($scope, Data, toaster) {
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
        Data.get('subsection', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.excel = function () {
        Data.get('subsection', paramRef).then(function (data) {
            window.location = 'api/web/subsection/excel';
        });
    };
    
    $scope.print = function () {
        Data.get('subsection', paramRef).then(function (data) {
            window.open('api/web/subsection/excel?print=true');
        });
    };
    
     Data.get('section/list').then(function(data) {
        $scope.listsection = data.data;
    });

    $scope.cariSection = function ($query) {

        if ($query.length >= 3) {
            Data.get('section/cari', {nama: $query}).then(function (data) {

                $scope.results = data.data;
            });
        }
    }

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        Data.get('subsection/kode').then(function (data) {
            $scope.form.kd_kerja = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.kd_kerja;
        $scope.selected(form.kd_kerja);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.kd_kerja;
        $scope.selected(form.kd_kerja);
    };
    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'subsection/create' : 'subsection/update/' + form.kd_kerja;
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
            }
        });

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
            Data.delete('subsection/delete/' + row.kd_kerja).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
                toaster.pop('success', "Berhasil", "Data berhasil dihapus")
            });
        }
    };
    
    $scope.selected = function (id) {
        Data.get('subsection/view/' + id).then(function (data) {
            $scope.form = data.data;
        });
    };


})
