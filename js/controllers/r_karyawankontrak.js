app.controller('karyawanKontrakCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;
    $scope.form.tanggal = new Date();

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.print = function (form) {
        if ('tanggal' in form && form.tanggal != null) {
            Data.post('karyawan/rekapkontrak', form).then(function (data) {
                window.open('api/web/karyawan/excelkontrak?print=true&rekap=karyawankontrak', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.excel = function (form) {
        if ('tanggal' in form && form.tanggal != null) {
            Data.post('karyawan/rekapkontrak', form).then(function (data) {
                window.location = 'api/web/karyawan/excelkontrak?rekap=karyawankontrak';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.cariSection = function ($query) {
        if ($query.length >= 3) {
            Data.get('section/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    };

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if ('tanggal' in form && form.tanggal != null) {
            $scope.show_detail = true;
            Data.post('karyawan/rekapkontrak', form).then(function (data) {
                $scope.listSrc = [];
                angular.forEach(data.data, function ($value, $key) {
                    $scope.listSrc.push($value);
                });
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };
});
