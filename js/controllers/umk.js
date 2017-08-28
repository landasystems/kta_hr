app.controller('umkCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;

    $scope.displayed = [];
    $scope.lokasikantor = [];
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
        Data.get('umk', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        Data.get("lokasikantor").then(function (data) {
            $scope.lokasikantor = data.data;
        });

        $scope.isLoading = false;
    };

    $scope.excel = function () {
        Data.get('umk', paramRef).then(function (data) {
            window.location = 'api/web/umk/excel';
        });
    };
    
    $scope.print = function () {
        Data.get('umk', paramRef).then(function (data) {
            window.open('api/web/umk/excel?print=true');
        });
    }

    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_umk;
        $scope.form = form;
    };

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        Data.get('umk/kode').then(function (data) {
            $scope.form.no_umk = data.kode;
        });
    };

    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_umk;
        $scope.form = form;
    };

    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('umk/delete/' + row.no_umk).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
                toaster.pop('success', "Berhasil", "Data berhasil dihapus")
            });
        }
    };

    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'umk/create' : 'umk/update/' + form.no_umk;
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

    //Terapkan UMK kepada semua pegawai yang aktif & pada daerah tertentu
    $scope.applyUmk = function (f_umk) {
    	Data.post("umk/applyToAll/", f_umk, "s").then(function (result) {
    		if (result.status == 0)
			toaster.pop('error', "Terjadi Kesalahan", result.errors);
		else toaster.pop("success", "Berhasil", "UMK sudah ditambahkan.");
		
    	});
    }
})
