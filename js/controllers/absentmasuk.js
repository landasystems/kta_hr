app.controller('absenMasukCtrl', function ($scope, $state, $stateParams, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.is_absen = false;
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
            Data.get('apppegawai/cari', data).then(function (data) {
                $scope.listnama = data.data;
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
        if ($stateParams.form != null) {
            $scope.is_absen = true;
            $scope.form = $stateParams.form;
        } else {
            $scope.is_absen = false;
            $scope.form = {};
        }
        var startDate = new Date();
        var endDate = new Date();
        $scope.form.datesRange = {
            startDate: startDate,
            endDate: endDate,
        };
        Data.get('absent/kode').then(function (data) {
            $scope.form.no_absent = data.kode;
        });

    };

    if ($stateParams.form != null) { //pengecekan jika ada pencarian, dilempar ke view
        $scope.create();
    }
    
    $scope.kembali = function(){
        $state.go('rekap.absensiharian');
    }

    $scope.update = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_absen = false;
        var startDate = new Date(form.tanggal);
        var endDate = (form.tanggal_kembali == null) ? new Date(form.tanggal) : new Date(form.tanggal_kembali);
        $scope.form.datesRange = {
            startDate: startDate,
            endDate: endDate,
        };
        $scope.formtitle = "Edit Data : " + form.no_absent;
    };

    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        if ($stateParams.form != null) {
            $scope.is_absen = true;
        } else {
            $scope.is_absen = false;
        }
        var startDate = new Date(form.tanggal);
        var endDate = new Date(form.tanggal_kembali);
        $scope.form.datesRange = {
            startDate: startDate,
            endDate: endDate,
        };
        $scope.formtitle = "Lihat Data : " + form.no_absent;
    };

    $scope.print = function (form) {
        Data.get('absent/view/' + form.no_absent).then(function (data) {
            window.open('api/web/absent/print?print=true', "", "width=500");
        });
    };

    $scope.save = function (form) {

        var url = ($scope.is_create == true) ? 'absent/create/' : 'absent/update/' + form.id;
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.is_view = true;

                //function view
                $scope.is_create = false;
                $scope.is_edit = true;
                $scope.is_view = true;
                $scope.formtitle = "Lihat Data : " + form.no_absent;
                //end function view

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
