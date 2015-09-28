app.controller('moFileLegalitasCtrl', function ($scope, Data, toaster) {
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
        Data.get('mlegalitas', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.open2 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened2 = true;
    };
    $scope.open3 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened3 = true;
    };

    $scope.excel = function () {
        Data.get('mlegalitas', paramRef).then(function (data) {
            window.location = 'api/web/mlegalitas/excel';
        });
    };

    $scope.cariFile = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('filelegalitas/cari', data).then(function (data) {
                $scope.results = data.data;
            });
        }
    };
    $scope.retFile = function (item, form) {
        form.no_file = item.no_file;
        form.nm_file = item.nm_file;
        form.instansi = item.instansi;
        form.atas_nm = item.atas_nm;
        form.jns_legalitas = item.jns_legalitas;
    };

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tgl_mflegalitas = new Date();
        $scope.form.tgl_pengesahan = new Date();
        $scope.form.masa_berlaku = new Date();
        Data.get('mlegalitas/kode').then(function (data) {
            $scope.form.no_mflegalitas = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_mflegalitas;
        $scope.form = form;
        $scope.form.tgl_mflegalitas = new Date(form.tgl_mflegalitas);
        $scope.form.tgl_pengesahan = new Date(form.tgl_pengesahan);
        $scope.form.masa_berlaku = new Date(form.masa_berlaku);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_mflegalitas;
        $scope.form = form;
    };
    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'mlegalitas/create' : 'mlegalitas/update/' + form.no_mflegalitas;
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
            Data.delete('mlegalitas/delete/' + row.no_mflegalitas).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

});
