app.controller('moAsuransiCtrl', function ($scope, Data, toaster) {
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
        Data.get('masuransi', param).then(function (data) {
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

    $scope.excel = function () {
        Data.get('masuransi', paramRef).then(function (data) {
            window.location = 'api/web/masuransi/excel';
        });
    };

    $scope.cari= function (nama) {
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
        form.user = item.user ;
        form.masa_berlaku = item.masa_berlaku;
    };

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tgl = new Date();
//        var start = new Date();
//        var end = new Date();
//        $scope.form.masa_berlaku = {startDate: start, endDate: end};
        Data.get('masuransi/kode').then(function (data) {
            $scope.form.no_mansuransi = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_mansuransi;
        $scope.form = form;
        $scope.form.tgl = new Date(form.tgl);
        var start = new Date(form.masa_berlaku_mulai);
        var end = new Date(form.masa_berlaku_sampai);
        $scope.form.masa_berlaku = {startDate: start, endDate: end};
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_mansuransi;
        $scope.form = form;
        var start = new Date(form.masa_berlaku_mulai);
        var end = new Date(form.masa_berlaku_sampai);
        $scope.form.masa_berlaku = {startDate: start, endDate: end};
    };
    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'masuransi/create' : 'masuransi/update/' + form.no_mansuransi;
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
            Data.delete('masuransi/delete/' + row.no_mansuransi).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    
    $scope.setDate = function(form){
        form.masa_berlaku_mulai = form.masa_berlaku.startDate;
        form.masa_berlaku_sampai = form.masa_berlaku.endDate;
    };
});
