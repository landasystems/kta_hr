app.controller('jadwalPenilaianCtrl', function($scope, Data, toaster) {
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
        Data.get('jpenilaian', param).then(function(data) {
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
    
    $scope.cariTernilai = function (nama){
        if(nama.length > 2){
            var data = {
                nama : nama
            };
            Data.get('karyawan/cari',data).then(function(data){
                $scope.detTernilai= data.data;
            });
        }
    };
    
    $scope.cariPenilai = function (nama){
        if(nama.length > 2){
            var data = {
                nama : nama
            };
            Data.get('karyawan/cari',data).then(function(data){
                $scope.detPenilai = data.data;
            });
        }
    };
    
    $scope.getPenilai = function (item,form){
        form.dep_penilai = item.department;
        form.nik_penilai = item.nik;
        form.penilai = item.nama;
    };
    
    $scope.getTernilai = function (item,form){
        form.bagian = item.department;
        form.nik = item.nik;
        form.nama = item.nama;
    };
    
    $scope.create = function(form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Jadwal Penilaian";
        $scope.form = {};
        $scope.form.tgl_penilaian = new Date();
        Data.get('jpenilaian/kode',form).then(function(data){
            $scope.form.no_jpenilaian = data.kode;
        });
    };
    $scope.update = function(form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form.tgl_penilaian = new Date(form.tgl_penilaian);
        $scope.formtitle = "Edit Data : " + form.no_jpenilaian;
    };
    $scope.view = function(form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_jpenilaian;
    };
    $scope.save = function(form) {
        var url = ($scope.is_create == true) ? 'jpenilaian/create/' : 'jpenilaian/update/' + form.no_jpenilaian;
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
            Data.delete('jpenilaian/delete/' + row.no_jpenilaian).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
});
