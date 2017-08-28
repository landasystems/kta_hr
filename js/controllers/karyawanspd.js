app.controller('tkaryawanSpdCtrl', function ($scope, Data, toaster) {
    //init data;
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
        Data.get('karyawanspd', param).then(function (data) {
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
    $scope.open3 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened3 = true;
    };
    $scope.excel = function () {
        Data.get('karyawanspd', paramRef).then(function (data) {
            window.location = 'api/web/karyawanspd/excel';
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
    
    $scope.getKaryawan = function (item, form) {
        form.sub_section = item.sub_section;
        form.nama = item.nama;
        form.jabatan = item.jabatan;
        form.department = item.department;
    };

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Karyawan SPD";
        $scope.form = {};
        $scope.form.tgl = new Date();
        $scope.form.tgl_berangkat = new Date();
        $scope.form.tgl_kembali = new Date();
        Data.get('karyawanspd/kode').then(function (data) {
            $scope.form.no_spd = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form.tgl = new Date(form.tgl);
        $scope.form.tgl_berangkat = new Date(form.tgl_berangkat);
        $scope.form.tgl_kembali = new Date(form.tgl_kembali);
        $scope.formtitle = "Edit Data : " + form.no_spd;
    };
    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.form.tgl = new Date(form.tgl);
        $scope.form.tgl_berangkat = new Date(form.tgl_berangkat);
        $scope.form.tgl_kembali = new Date(form.tgl_kembali);
        $scope.formtitle = "Lihat Data : " + form.no_spd;
    };
    $scope.save = function (form) {

        var url = ($scope.is_create == true) ? 'karyawanspd/create/' : 'karyawanspd/update/' + form.no_spd;
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
            Data.delete('karyawanspd/delete/' + row.no_spd).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    }
    ;
});
