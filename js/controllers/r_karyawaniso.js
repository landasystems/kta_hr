app.controller('karyawanIsoCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;

    $scope.print = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('karyawan/rekapiso', form).then(function (data) {
                window.open('api/web/karyawan/excelmasuk?print=true&rekap=karyawaniso', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.excel= function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('karyawan/rekapiso', form).then(function (data) {
                window.location = 'api/web/karyawan/excelmasuk?rekap=karyawaniso';
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
    
    $scope.setDate = function(form,tanggal){
        form.tgl_start = new Date(tanggal.startDate);
        form.tgl_end = new Date(tanggal.endDate);
    };

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            $scope.show_detail = true;
            Data.post('karyawan/rekapiso', form).then(function (data) {
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
