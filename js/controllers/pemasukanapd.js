app.controller('pemasukanApdCtrl', function ($scope, Data, toaster) {
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
        Data.get('pemasukanapd', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };

    $scope.excel = function () {
        Data.get('pemasukanapd', paramRef).then(function (data) {
            window.location = 'api/web/pemasukanapd/excel';
        });
    };
    
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Pengeluaran APD";
        $scope.form = {};
        $scope.detBarang = [{}];
        $scope.form.tgl = new Date();
        Data.get('pemasukanapd/kode', form).then(function (data) {
            $scope.form.no_transaksi = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form.tgl = new Date(form.tgl);
        $scope.formtitle = "Edit Data : " + form.no_transaksi;
        $scope.retDetail(form);
    };
    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_transaksi;
        $scope.retDetail(form);
    };
    $scope.save = function (form, detail) {
        var data = {
            form: form,
            detail: detail
        };

        var url = ($scope.is_create == true) ? 'pemasukanapd/create/' : 'pemasukanapd/update/' + form.no_transaksi;
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
            Data.delete('pemasukanapd/delete/' + row.no_transaksi).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

    $scope.cariStockApd = function (nama) {
        if (nama.length > 2) {
            Data.get('apd/cari', {nama: nama}).then(function (data) {
                $scope.barangAtk = data.data;
            });
        }
    };

    $scope.getBarangApd = function (det, item) {
        det.kd_apd = item.kode_apd;
        det.nm_apd = item.nama_apd;
        det.jumlah_apd = item.jumlah_apd;
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
        form.nik_karyawan = item.nik;
    };

    $scope.retDetail = function (form) {
        Data.get('pemasukanapd/view/' + form.no_transaksi).then(function (data) {
            $scope.detBarang = data.data;
        });
    };

    $scope.addrow = function () {
        $scope.detBarang.unshift({
            id: 0,
            apd: [],
            jmlh_apd: 0,
            kd_apd: '',
        });
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detBarang);
        if (comArr.length > 1) {
            $scope.detBarang.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };

});
