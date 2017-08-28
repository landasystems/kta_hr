app.controller('moServiceKendaraanCtrl', function ($scope, Data, toaster) {
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
        Data.get('mservice', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };
    $scope.excel = function () {
        Data.get('mservice', paramRef).then(function (data) {
            window.location = 'api/web/mservice/excel';
        });
    };
    $scope.cari = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('kendaraan/cari', data).then(function (data) {
                $scope.results = data.data;
            });
        }
    };
    $scope.retriv = function (item, form) {
        form.nopol = item.nopol;
        form.merk = item.merk;
        form.tipe = item.tipe;
        form.model = item.model;
        form.warna = item.warna;
        form.thn_pembuatan = item.thn_pembuatan;
        form.no_rangka = item.no_rangka;
        form.no_mesin = item.no_mesin;
        form.user = item.user;
        form.masa_berlaku = item.masa_berlaku;
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
        $scope.detService = [{}];
        Data.get('mservice/kode').then(function (data) {
            $scope.form.no_mservice = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_mservice;
        $scope.form = form;
        $scope.form.tgl = new Date(form.tgl);
        $scope.retDetail(form);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_mservice;
        $scope.form = form;
        $scope.retDetail(form);
        
    };

    $scope.retDetail = function (no) {
        Data.get('mservice/view/' + no.no_mservice).then(function (data) {
            $scope.detService = data.data;
            $scope.total($scope.detService);
        });
    };

    $scope.save = function (form, detail) {
        var data = {
            form: form,
            detail: detail
        };

        var url = ($scope.is_create == true) ? 'mservice/create' : 'mservice/update/' + form.no_mservice;
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
    
    $scope.total = function(detService){
        var total = 0;
        angular.forEach(detService, function (detail) {
            total += parseInt(detail.biaya);
        });
        $scope.form.total = total;
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
            Data.delete('mservice/delete/' + row.no_mservice).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


    $scope.addrow = function () {
        $scope.detService.unshift({
            id: 0,
            no: '',
            ket_service: '',
            biaya: '',
        });
    };
    
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detService);
        if (comArr.length > 1) {
            $scope.detService.splice(paramindex, 1);
            $scope.total($scope.detService);
        } else {
            alert("Something gone wrong");
        }
    };

});
