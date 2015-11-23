app.controller('penilaianKontrakCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;

    $scope.print = function (form) {
        if ('karyawan' in form && form.karyawan != undefined) {
            Data.post('penilaiankontrak/rekap', form).then(function (data) {
                window.open('api/web/penilaiankontrak/excel?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih karyawan terlebih dahulu!");
        }
    }

    $scope.excel = function (form) {
        if ('karyawan' in form && form.karyawan != undefined) {
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

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if ('karyawan' in form && form.karyawan != undefined) {
            $scope.show_detail = true;
            Data.post('penilaiankontrak/rekap', form).then(function (data) {
                $scope.listSrc = [];
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
