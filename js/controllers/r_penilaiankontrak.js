app.controller('penilaianKontrakCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.form.tipe = 'kelompok';
    $scope.show_detail = false;
    $scope.form.tanggal = new Date();

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.print = function (form) {
        if (('tanggal' in form && form.tanggal != null) || ('Karyawan' in form && form.Karyawan != undefined)) {
            Data.post('penilaiankontrak/rekap', form).then(function (data) {
                window.open('api/web/penilaiankontrak/excel?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih karyawan terlebih dahulu!");
        }
    }

    $scope.excel = function (form) {
        if (('tanggal' in form && form.tanggal != null) || ('Karyawan' in form && form.Karyawan != undefined)) {
            Data.post('penilaiankontrak/rekap', form).then(function (data) {
                window.location = 'api/web/penilaiankontrak/excel';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih karyawan terlebih dahulu!");
        }
    };

    $scope.cariKaryawan = function (nama) {
        if (nama.length > 2) {
            Data.get('karyawan/cari', {nama: nama}).then(function (data) {
                $scope.listKaryawan = data.data;
            });
        }
    };

    $scope.cariSection = function ($query) {
        if ($query.length >= 3) {
            Data.get('section/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    };

    $scope.setDate = function (form, tanggal) {
        form.tgl_start = new Date(tanggal.startDate);
        form.tgl_end = new Date(tanggal.endDate);
    };

    $scope.clear1 = function () {
        $scope.form.Section = undefined;
        $scope.form.tanggal = undefined;
    };
    $scope.clear2 = function () {
        $scope.form.Karyawan = undefined;
    };

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if (('tanggal' in form && form.tanggal != null) || ('Karyawan' in form && form.Karyawan != undefined)) {
            
            Data.post('penilaiankontrak/rekap', form).then(function (data) {
                $scope.listSrc = [];
                $scope.show_detail = true;
                angular.forEach(data.data, function ($value, $key) {
                    $scope.listSrc.push($value);
                });
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih karyawan terlebih dahulu!");
        }
    };
    $scope.blank = function () {
        $scope.karyawan = undefined;
        $scope.show_detail = false;
    };

    $scope.convAngka = function (item) {
        var hasil = '';
        if (item == 4) {
            hasil = 'A';
        } else if (item == 3) {
            hasil = 'B';
        } else if (item == 2) {
            hasil = 'C';
        } else {
            hasil = 'D';
        }
        return hasil;
    };

});
