app.controller('pemakaianLatCtrl', function($scope, Data, toaster) {
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
        Data.get('pemakaianlat', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    
    $scope.excel = function() {
        Data.get('pemakaianlat', paramRef).then(function(data) {
            window.location = 'api/web/pemakaianlat/excel';
        });
    };
    
    $scope.create = function(form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Pemakaian Listrik Air Telp";
        $scope.form = {};
        $scope.form.tgl = new Date();
        $scope.form.tgl_reff = new Date();
        Data.get('pemakaianlat/kode',form).then(function(data){
            $scope.form.no_pemakaian = data.kode;
        });
    };
    $scope.update = function(form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form.tgl = new Date(form.tgl);
        $scope.form.tgl_reff = new Date(form.tgl_reff);
        $scope.formtitle = "Edit Data : " + form.no_pemakaian;
    };
    $scope.view = function(form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_pemakaian;
    };
    $scope.save = function(form) {
        var url = ($scope.is_create == true) ? 'pemakaianlat/create/' : 'pemakaianlat/update/' + form.no_pemakaian;
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
            Data.delete('pemakaianlat/delete/' + row.no_pemakaian).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
});
