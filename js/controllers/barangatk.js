app.controller('barangatkCtrl', function($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.detSatuan = [{}];
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
        Data.get('barangatk', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    
    $scope.excel = function() {
        Data.get('barangatk', paramRef).then(function(data) {
            window.location = 'api/web/barangatk/excel';
        });
    };
    $scope.print = function () {
        Data.get('barangatk', paramRef).then(function (data) {
            window.open('api/web/barangatk/excel?print=true');
        });
    }
    $scope.addrow = function () {
        $scope.detSatuan.push({ 
            satuan: '',
            konversi: 0, 
        });
    };
     $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detSatuan);
        if (comArr.length > 1) {
            $scope.detSatuan.splice(paramindex, 1);
        } else {
            alert("Satuan Dasar Tidak Bisa di Hapus");
        }
    };
    
    $scope.create = function(form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Barang ATK";
        $scope.form = {};
        $scope.detSatuan = [{}];
        Data.get('barangatk/kode',form).then(function(data){
            $scope.form.kode_brng = data.kode;
        });
    };
    $scope.update = function(form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.kode_brng;
         $scope.detail(form.kode_brng);
    };
    $scope.view = function(form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.kode_brng;
        $scope.detail(form.kode_brng);
    };
    $scope.detail = function(kode){
         Data.post('barangatk/view', {kode:kode}).then(function(result) {
             $scope.detSatuan = result.detail;
         });
    }
    $scope.save = function(detail,form) {
        var data = {
          form:form,
          detail:detail
        };
        var url = ($scope.is_create == true) ? 'barangatk/create/' : 'barangatk/update/' + form.kode_brng;
        Data.post(url, data).then(function(result) {
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
            Data.delete('barangatk/delete/' + row.kode_brng).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
                toaster.pop('success', "Berhasil", "Data berhasil dihapus");
            });
        }
    };
});
