app.controller('transPotonganCtrl', function ($scope, Data, toaster) {
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
        Data.get('transpotongan', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
        Data.get('potongan/list').then(function(data){
//            console.log(data);
            $scope.listPotongan = data.data;
        });
    };

    $scope.cariKaryawan = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.results = data.data;
            });
        }
    };
    
    
    
    $scope.getKaryawan= function (item, form) {
        form.nik = item.nik;
        form.nama = item.nama;
    };
    
    $scope.cariPotongan = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('potongan/cari', data).then(function (data) {
                $scope.detailPotongan = data.data;
            });
        }
    };
    
    $scope.getPotongan = function (item, det) {
        det.kd_pot = item.kode_potongan;
        det.nm_pot = item.nm_potongan;
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
    
    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Transaksi Potongan";
        $scope.form = {};
        $scope.form.tgl = new Date();
        $scope.detPotongan = [{}];
        Data.get('transpotongan/kode', form).then(function (data) {
            $scope.form.no_pot = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form.tgl = new Date(form.tgl);
        $scope.formtitle = "Edit Data : " + form.no_pot;
        Data.get('transpotongan/view/' + form.no_pot).then(function (data) {
            $scope.detPotongan = data.data;
        });
    };
    
    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.form.tgl = new Date(form.tgl);
        $scope.formtitle = "Lihat Data : " + form.no_pot;
        Data.get('transpotongan/view/' + form.no_pot).then(function (data) {
            $scope.detPotongan = data.data;
        });
    };
    
    $scope.save = function (form, detail) {
        var data = {
            form: form,
            detail: detail
        };
        var url = ($scope.is_create == true) ? 'transpotongan/create/' : 'transpotongan/update/' + form.no_pot;
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
            Data.delete('transpotongan/delete/' + row.no_pot).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    
    $scope.addrow = function () {
        $scope.detPotongan.unshift({
            id: 0,
            no: '',
            potongan: [],
            jmlh: 0,
        });
    };
    
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detPotongan);
        if (comArr.length > 1) {
            $scope.detPotongan.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
});
