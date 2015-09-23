app.controller('atkMasukCtrl', function ($scope, Data, toaster) {
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
        Data.get('atkmasuk', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };
    $scope.excel = function () {
        Data.get('atkmasuk', paramRef).then(function (data) {
            window.location = 'api/web/atkmasuk/excel';
        });
    };

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tgl = new Date();
        $scope.detBarang = [{}];
        Data.get('atkmasuk/kode').then(function (data) {
            $scope.form.no_transaksi = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_transaksi;
        $scope.form = form;
        $scope.form.tgl = new Date(form.tgl);
        $scope.retDetail(form);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_transaksi;
        $scope.form = form;
        $scope.retDetail(form);
    };
    $scope.save = function (form,detail) {
        var data = {
            form : form,
            detail : detail
        };
        
        var url = ($scope.is_create == true) ? 'atkmasuk/create' : 'atkmasuk/update/' + form.no_transaksi;
        Data.post(url, data).then(function (result) {
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
            Data.delete('atkmasuk/delete/' + row.no_transaksi).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

    $scope.cariBarangAtk = function (nama) {
        if (nama.length > 2) {
            Data.get('barangatk/cari', {nama: nama}).then(function (data) {
                $scope.barangAtk = data.data;
            });
        }
    };

    $scope.getBarangAtk = function (det, item) {
        det.kd_brng = item.kode_brng;
        det.nm_brng = item.nama_brng;
        det.jumlah_brng = item.jumlah_brng;
    };
    $scope.cariPegawai = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.detPegawai = data.data;
            });
        }
    };

    $scope.getPegawai= function (form, item) {
        form.kd_karyawan = item.nik;
    };

    $scope.retDetail = function (form) {
        Data.get('atkmasuk/view/' + form.no_transaksi).then(function (data) {
            $scope.detBarang = data.data;
        });
    };

    $scope.addrow = function () {
        $scope.detBarang.unshift({
            id: 0,
            barang: [],
            jmlh_brng: 0,
            kd_brng: '',
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
