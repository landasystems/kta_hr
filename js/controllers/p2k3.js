app.controller('timP2k3Ctrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.detp2k3 = [{}];
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
        Data.get('timp2k3', param).then(function (data) {
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
    $scope.print = function () {
        Data.get('timp2k3', paramRef).then(function (data) {
            window.open('api/web/timp2k3/excel?print=true');
        });
    }

    $scope.cari = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.listKaryawan = data.data;

            });
        }
    };
    $scope.retKaryawan = function (det, item) {
        det.nik_karyawan = item.nik;
        det.jabatan = item.jabatan;
    };
    $scope.addrow = function () {
        $scope.detp2k3.push({
            id: 0,
            karyawan: [],
            bagian: '',
            jabatan: '',
            keterangan: '',
        });
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detp2k3);
        if (comArr.length > 1) {
            $scope.detp2k3.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form TIM P2K3";
        $scope.form = {};
        $scope.form.tgl = new Date();
        Data.get('timp2k3/kode', form).then(function (data) {
            $scope.form.no_tim = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form.tgl = new Date(form.tgl);
        $scope.formtitle = "Edit Data : " + form.no_tim;
    };
    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_tim;
    };
    $scope.save = function (data,detail) {
        var form = {
          form:data,
          detail:detail
        };
        var url = ($scope.is_create == true) ? 'timp2k3/create/' : 'timp2k3/update/' + form.no_tim;
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
        });
    };
    $scope.cancel = function () {
        $scope.is_edit = false;
        $scope.is_view = false;
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('timp2k3/delete/' + row.no_tim).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
});
