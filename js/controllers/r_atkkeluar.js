app.controller('atkKeluarCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;

    $scope.print = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('atkkeluar/rekap', form).then(function (data) {
                window.open('api/web/atkkeluar/excel?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.excelkeluar = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('atkkeluar/rekap', form).then(function (data) {
                window.location = 'api/web/atkkeluar/excel';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

//    $scope.cariBarang = function ($query) {
//        if ($query.length >= 3) {
//            Data.get('atkkeluar/cari', {atkkeluar: $query}).then(function (data) {
//                $scope.resultsatkkeluar = data.data;
//            });
//        }
//    };

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            $scope.show_detail = true;
            Data.post('atkkeluar/rekap', form).then(function (data) {
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
