app.controller('absenMasukCtrl', function ($scope, Data, toaster) {
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
        Data.get('absent', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.tgl = new Date();

    //begin jam

//    $scope.mytime = new Date();

    $scope.hstep = 1;
    $scope.mstep = 1;

    $scope.options = {
        hstep: [1, 2, 3],
        mstep: [1, 5, 10, 15, 25, 30]
    };

    $scope.ismeridian = true;
    $scope.toggleMode = function () {
        $scope.ismeridian = !$scope.ismeridian;
    };
    $scope.clear = function () {
        $scope.form.jmasuk = null;
    };

    //end jam


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
    $scope.cari = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.listKaryawan = data.data;
            });
        }
    };
    $scope.retKaryawan = function (item, form) {
        form.nik = item.nik;
        form.nama = item.nama;
    };
    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Absen Tidak Masuk";
        $scope.form = {};
        $scope.form.tanggal = new Date();
//        $scope.form.jmasuk = new Date();
//        $scope.form.jkeluar = new Date();
        Data.get('absent/kode').then(function (data) {
            $scope.form.no_absent = data.kode;
        });

    };
    
    $scope.update = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form.tanggal = new Date(form.tanggal);
//        $scope.form.jmasuk = new Date(form.jmasuk);
//        $scope.form.jkeluar = new Date(form.jkeluar);
        $scope.formtitle = "Edit Data : " + form.no_absent;
    };
    
    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_absent;
    };
    
    $scope.save = function (form) {
        console.log(form);
        var url = ($scope.is_create == true) ? 'absent/create/' : 'absent/update/' + form.no_absent;
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.is_view = true;
                $scope.view(form);
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
                $scope.callServer(tableStateRef); //reload grid ulang
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
            Data.delete('absent/delete/' + row.no_absent).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

});