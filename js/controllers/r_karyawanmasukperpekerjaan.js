app.controller('karyawanMasukCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.form.tipe = 'kelompok';
    $scope.show_detail = false;

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.print = function (form) {
        if (('tanggal' in form && form.tanggal != null) || ('Karyawan' in form && form.Karyawan != undefined)) {
            Data.post('karyawan/rekapmasuk', form).then(function (data) {
                window.open('api/web/karyawan/excelmasuk?print=true&rekap=karyawanmasukperpekerjaan', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.excel = function (form) {
        if (('tanggal' in form && form.tanggal != null) || ('Karyawan' in form && form.Karyawan != undefined)) {
            Data.post('karyawan/rekapmasuk', form).then(function (data) {
                window.location = 'api/web/karyawan/excelmasuk?rekap=karyawanmasukperpekerjaan';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.cariDepartment = function ($query) {
        if ($query.length >= 3) {
            Data.get('departement/cari', {nama: $query}).then(function (data) {
                $scope.listDepartment = data.data;
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
    $scope.cariKaryawan = function ($query) {
        if ($query.length >= 3) {
            Data.get('karyawan/cari', {nama: $query}).then(function (data) {
                $scope.listKaryawan = data.data;
            });
        }
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
            $scope.show_detail = true;
            Data.post('karyawan/rekapmasuk', form).then(function (data) {
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
