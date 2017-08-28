app.controller('lokasikantorCtrl', function ($scope, Data, toaster) {
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
        $scope.refreshJadwal();
    };

    $scope.refreshJadwal = function () {
        Data.get('lokasikantor').then(function (data) {
        for(var b=0; b<data.data.length; b++) {//Parse object to array then time data from date
        	var d = data.data[b]
            if(d.jadwal == undefined || d.jadwal.length == 0) continue;
            d.jadwal = $.map(JSON.parse(d.jadwal), function(value, index) { return [value]; });

            for(var a=0; a<d.jadwal.length; a++) {
                d.jadwal[a].jam_mulai = new Date(d.jadwal[a].jam_mulai); 
                d.jadwal[a].jam_selesai = new Date(d.jadwal[a].jam_selesai); 
            }
        }
        $scope.displayed = data.data;
        console.log("data:", data);
        console.log("data.data:", data.data);

        var offset = tableStateRef.pagination.start || 0;
        var limit = tableStateRef.pagination.number || 10;
        var param = {offset: offset, limit: limit};

        if (tableStateRef.sort.predicate) {
            param['sort'] = tableStateRef.sort.predicate;
            param['order'] = tableStateRef.sort.reverse;
        }
        if (tableStateRef.search.predicateObject) {
            param['filter'] = tableStateRef.search.predicateObject;
        }
        paramRef = param;

        tableStateRef.pagination.numberOfPages = Math.ceil(data.totalItems / limit);

        $scope.isLoading = false;
      });
    }

    $scope.excel = function () {
        Data.get('lokasikantor', paramRef).then(function (data) {
            window.location = 'api/web/lokasikantor/excel';
        });
    };
    $scope.print = function () {
        Data.get('lokasikantor', paramRef).then(function (data) {
            window.open('api/web/lokasikantor/excel?print=true');
        });
    };

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        Data.get('lokasikantor/kode').then(function (data) {
            $scope.form.id_lokasi_kantor = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.id_lokasi_kantor;
        $scope.form = form;
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.id_lokasi_kantor;
        $scope.form = form;
    };
    $scope.save = function (form) {
        form.jadwal = JSON.stringify(form.jadwal);

        var url = ($scope.is_create == true) ? 'lokasikantor/create' : 'lokasikantor/update/' + form.id_lokasi_kantor;
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
            Data.delete('lokasikantor/delete/' + row.id_lokasi_kantor).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
                toaster.pop('success', "Berhasil", "Data berhasil dihapus")
            });
        }
    };


    $scope.hariSmng = function (i) {
    	switch (i) {
    		case 0: return "Minggu";
    		case 1: return "Senin";
    		case 2: return "Selasa";
    		case 3: return "Rabu";
    		case 4: return "Kamis";
    		case 5: return "Jumat";
    		case 6: return "Sabtu";
    		default: return "Bukan hari sah";
    	}
    };


})
