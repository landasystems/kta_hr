app.controller('jamsostekCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.is_nn = true;
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
        Data.get('tbljamsostek', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };

// start date picker


    $scope.setStatus = function () {
        $scope.openedDet = -1;
    };

    $scope.openDet = function ($event, $index) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedDet = $index;
    };

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.clear1 = function () {
        $scope.form.Section = undefined;
        $scope.form.tanggal = undefined;
    };
    $scope.clear2 = function () {
        $scope.form.Karyawan = undefined;
    };

    $scope.clearTgl = function () {
        $scope.form.tanggal = undefined;
    };
    $scope.clearRentang = function () {
        $scope.form.tanggal_rentang = undefined;
    };

//    end date picker

//start print and export
    $scope.excel = function () {
        Data.get('jamsostek', paramRef).then(function (data) {
            window.location = 'api/web/jamsostek/excel';
        });
    }
    $scope.print = function () {
        Data.get('jamsostek', paramRef).then(function (data) {
            window.open('api/web/jamsostek/excel?print=true');
        });
    }
//end print and export

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah BPJS Ketenaga Kerjaan";
        $scope.form = {};
        $scope.detJamsostek = [{}];

    };
    $scope.update = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nik;
        $scope.retDetail(form);
    };
    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.detJamsostek = [{}];
        $scope.formtitle = "Lihat Data : " + form.nik;
        $scope.retDetail(form);
    };
    $scope.save = function (form, detail) {
        var data = {
            form: form,
            detail: detail
        };

        var url = ($scope.is_create == true) ? 'jamsostek/create/' : 'jamsostek/update/' + form.id;
        Data.post(url, data).then(function (result) {
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
            Data.delete('jamsostek/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
                toaster.pop('success', "Berhasil", "Data berhasil dihapus");
            });
        }
    };
    $scope.retDetail = function (form) {
        Data.get('jamsostek/view/' + form.nik).then(function (data) {
//            $scope.detJamsostek = data.data;
            if ((data.data).length > 0) {

                $scope.detJamsostek = data.data;
            } else {
                $scope.detJamsostek = [{}];
            }
        });
    };

    $scope.addrow = function () {
        $scope.detJamsostek.unshift({
            id: 0,
            barang: [],
            jmlh_brng: 0,
            kd_brng: '',
        });
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detJamsostek);
        if (comArr.length > 1) {
            $scope.detJamsostek.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };

    $scope.cariPegawai = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.detPegawai = data.data;
            });
        }
    };

    $scope.getPegawai = function (form, item) {
        form.nik = item.nik;
        form.status_pernikahan = item.status_pernikahan;
        form.upah_tetap = item.upah_tetap;
    };

    $scope.setType = function (det, gaji) {
        if (det.nn === 'NN040454' || det.nn === 'NN040356') {
            $scope.is_nn = true;
            var gajiTk = parseInt(gaji);
            det.jht = (gajiTk * 5.7) / 100;
            det.jkm = (gajiTk * 0.3) / 100;
            det.jkk = (gajiTk * 1.27) / 100;
            det.pensiun = (gajiTk * 3) / 100;
            det.iuran = det.jht + det.jkm + det.jkk + det.pensiun;
        } else {
            $scope.is_nn = false;
        }

    };

});
