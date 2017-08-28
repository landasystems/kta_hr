app.controller('ijazahCtrl', function($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.tgl= new Date();
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
        Data.get('ijazah', param).then(function(data) {
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
    $scope.open4 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened4 = true;
    };
    
    $scope.cariPegawai = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.detPegawai = data.data;
            });
        }
    };

    $scope.getPegawai = function (item, form) {
//        console.log(item);
        form.nik = item.nik;
        form.atas_nama = item.nama;
        form.tempat_lahir = item.tmpt_lahir;
        form.tgl_lahir = new Date(item.tgl_lahir);
    };
    
    $scope.create = function(form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Ijazah";
        $scope.form = {};
        $scope.form.tgl_masuk = new Date();
        $scope.form.tgl_ijazah = new Date();
        $scope.form.tgl_lahir = new Date();
        $scope.form.tgl_keluar = new Date();
        Data.get('ijazah/kode',form).then(function(data){
            $scope.form.no = data.kode;
        });
    };
    $scope.update = function(form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form.tgl_masuk = new Date(form.tgl_masuk);
        $scope.form.tgl_ijazah = new Date(form.tgl_ijazah);
        $scope.form.tgl_lahir = new Date(form.tgl_lahir);
        $scope.form.tgl_keluar = new Date(form.tgl_keluar);
        $scope.formtitle = "Edit Data : " + form.no;
    };
    $scope.view = function(form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no;
    };
    $scope.save = function(form) {
        var url = ($scope.is_create == true) ? 'ijazah/create/' : 'ijazah/update/' + form.no;
        Data.post(url, form).then(function(result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.is_view = true;
                $scope.callServer(tableStateRef); //reload grid ulang
                $scope.view(form);
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
        });
    };
    $scope.cancel = function() {
        $scope.is_edit = false;
        $scope.is_view = false;
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('ijazah/delete/' + row.no).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
});
