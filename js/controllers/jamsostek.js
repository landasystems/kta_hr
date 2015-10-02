app.controller('jamsostekCtrl', function($scope, Data, toaster) {
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
        Data.get('tbljamsostek', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    
    $scope.openedDet = -1;

    $scope.openDet = function($event, $index) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedDet = $index;
    };
    $scope.setStatus = function() {
        $scope.openedDet = -1;
    };
    
    $scope.create = function(form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Jamsostek";
        $scope.form = {};
        $scope.detJamsostek = [{}];
    };
    $scope.update = function(form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nik;
        $scope.retDetail(form);
    };
    $scope.view = function(form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nik;
    };
    $scope.save = function(form) {
        var url = ($scope.is_create == true) ? 'barang/create/' : 'barang/update/' + form.id;
        Data.post(url, form).then(function(result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
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
            Data.delete('barang/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.retDetail = function (form) {
        Data.get('tbljamsostek/view/' + form.id).then(function (data) {
            $scope.detJamsostek = data.data;
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

    $scope.getPegawai= function (form, item) {
        form.nik = item.nik;
        form.status_pernikahan = item.status_pernikahan;
        form.upah_tetap = item.upah_tetap;
    };
    
});
