app.controller('penilaianKontrakCtrl', function ($scope, Data, toaster) {
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
        Data.get('penilaiankontrak', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.tgl = new Date();
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
            Data.get('karyawan/carikontrak', data).then(function (data) {
                $scope.listKaryawan = data.data;
            });
        }
    };
    $scope.retKaryawan = function (item, form) {
        form.no_kntrk = item.no_kontrak;
        form.nik = item.nik;
        form.nama = item.nama;
        form.jabatan = item.jabatan;
        form.department = item.department;
        form.sub_section = item.sub_section;
    };
    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Penilaian Karyawan Kontrak";
        $scope.form = {};
        $scope.form.tgl_hse = new Date();
    };
    $scope.update = function (form) {
        $scope.form = form;
        $scope.form.tgl_hse = new Date(form.tgl_hse);
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nik;
    };
    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nik;
    };
    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'penilaiankontrak/create/' : 'penilaiankontrak/update/' + form.id;
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
            Data.delete('penilaiankontrak/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

    $scope.nilaiTotal = function () {
        var subTotal1 = 0;
        var subTotal2 = 0;
        var subTotal3 = 0;
        subTotal1 = parseInt($scope.form.mutu_kerja) + parseInt($scope.form.pengetahuan_teknis) + parseInt($scope.form.tgjawab_pekerjaan) + parseInt($scope.form.kerjasama_komunikasi) + parseInt($scope.form.sikap_kerja) + parseInt($scope.form.inisiatif) + parseInt($scope.form.rasa_turut_memiliki) + parseInt($scope.form.disiplinitas);
        subTotal3 = parseInt($scope.form.kehadiran) + parseInt($scope.form.administratif);
        subTotal2 = parseInt($scope.form.kepemimpinan) + parseInt($scope.form.pelaksanaan_managerial) + parseInt($scope.form.problem_solving);

        $scope.form.sub1 = ((subTotal1 / 100) * 40).toFixed(2);
        $scope.form.sub2 = ((subTotal2 / 100) * 20).toFixed(2);
        $scope.form.sub3 = ((subTotal3 / 100) * 40).toFixed(2);

        $scope.form.nilaiFinal = (parseFloat($scope.form.sub1) + parseFloat($scope.form.sub2) + parseFloat($scope.form.sub3)).toFixed(2);

    };
    
    $scope.nilaiToString = function(angka){
        var hasil = '';
        if(angka == 4){
            hasil = 'A';
        }else if(angka == 3){
            hasil = 'B';
        }else if(angka == 2){
            hasil = 'C';
        }else{
            hasil = 'D';
        }
        
        return hasil;
    };
});
