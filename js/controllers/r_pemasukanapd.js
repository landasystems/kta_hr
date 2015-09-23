app.controller('pemasukanApdCtrl', function ($scope, Data, toaster) {
    //init data
   var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;

    $scope.print = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('pemasukanapd/rekap', form).then(function (data) {
                window.open('api/web/pemasukanapd/excel?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.excelkeluar = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('pemasukanapd/rekap', form).then(function (data) {
                window.location = 'api/web/pemasukanapd/excel';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

//    $scope.cariBarang = function ($query) {
//        if ($query.length >= 3) {
//            Data.get('pemasukanapd/cari', {pemasukanapd: $query}).then(function (data) {
//                $scope.resultspemasukanapd = data.data;
//            });
//        }
//    };

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            $scope.show_detail = true;
            Data.post('pemasukanapd/rekap', form).then(function (data) {
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
